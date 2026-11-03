<?php

namespace IVideon\Tests;

use Composer\Autoload\ClassLoader;
use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use IVideon\Account;
use IVideon\Api\Api;
use IVideon\Api\Camera;
use IVideon\Constants;
use IVideon\Exceptions\ExportRequestFailedException;
use IVideon\Flows\WebFlow;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionMethod;
use RuntimeException;

// Run: php tests/php85-compatibility.php [path/to/vendor/autoload.php]. No provider access.
$autoload = $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException('Install package dependencies or pass a Composer autoload.php path');
}

$previousReporting = error_reporting(E_ALL);
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    $loader = require $autoload;

    if (!$loader instanceof ClassLoader || class_exists(Camera::class, false)) {
        throw new RuntimeException('A fresh Composer loader with unloaded SDK classes is required');
    }

    $source = dirname(__DIR__) . '/src';
    // A consumer can have an optimized class map, which takes precedence over its PSR-4 entries.
    $sdkLoader = new ClassLoader();
    $sdkLoader->addPsr4('IVideon\\', $source . '/', true);
    $sdkLoader->register(true);
    $assertions = 0;
    $check = static function (bool $condition, string $description) use (&$assertions): void {
        if (!$condition) {
            throw new RuntimeException($description);
        }

        $assertions++;
    };

    // Load every package class/interface while all PHP warnings and deprecations are fatal.
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source));

    foreach ($files as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $relative = substr($file->getPathname(), strlen($source) + 1, -4);
        $class = 'IVideon\\' . str_replace(['/', '\\'], '\\', $relative);
        $check(class_exists($class) || interface_exists($class), 'Cannot autoload ' . $class);
    }

    $method = new ReflectionMethod(Camera::class, 'getExports');
    $check(realpath($method->getFileName()) === realpath($source . '/Api/Camera.php'), 'Wrong SDK source');
    $parameters = $method->getParameters();
    $check(array_map(static fn ($parameter): string => $parameter->getName(), $parameters) === [
        'limit', 'skip', 'from', 'to', 'camera_ids',
    ], 'Named arguments changed');
    $check(array_map(static fn ($parameter): string => (string) $parameter->getType(), $parameters) === [
        'int', 'int', '?int', '?int', 'array',
    ], 'Parameter types changed');
    $check(array_map(static fn ($parameter) => $parameter->getDefaultValue(), $parameters) === [
        100, 0, null, null, [],
    ], 'Default arguments changed');

    $handler = new MockHandler(array_map(static fn (int $status): Response => new Response($status, [
        'Content-Type' => 'application/json',
    ], '{"success":true,"result":{"items":[]}}'), [200, 200, 200, 200]));
    $account = (new Account('synthetic@example.test', 'synthetic-password'))
        ->setAccessToken('synthetic-token')
        ->setUserApiUrl('https://camera.example.test');
    $loginHistory = [];
    $loginStack = HandlerStack::create(new MockHandler([new Response(200, [], json_encode([
        'owner_id' => 'synthetic-owner',
        'access_token' => 'refreshed-token',
        'api_host' => 'camera.example.test',
    ], JSON_THROW_ON_ERROR))]));
    $loginStack->push(Middleware::history($loginHistory));
    $flow = new WebFlow([
        'handler' => $loginStack,
    ]);
    $api = new class ($account, $flow, $handler) extends Api {
        public array $history = [];

        private MockHandler $handler;

        public function __construct(Account $account, WebFlow $flow, MockHandler $handler)
        {
            $this->handler = $handler;
            parent::__construct($account, $flow);
        }

        public function request(string $method, string $uri, array $options = []): ?array
        {
            if (($options['handler'] ?? null) instanceof HandlerStack) {
                $options['handler']->setHandler($this->handler);
                $options['handler']->remove('compatibility_history');
                $options['handler']->push(Middleware::history($this->history), 'compatibility_history');
            }

            return parent::request($method, $uri, $options);
        }

        protected function buildClient(): Client
        {
            $config = parent::buildClient()->getConfig();
            $stack = HandlerStack::create($this->handler);
            $stack->push(Middleware::history($this->history));
            $config['handler'] = $stack;

            return new Client($config);
        }
    };

    $check($api->camera->getExports() === [], 'Default export result changed');
    $check($api->camera->getExports(from: null, to: null) === [], 'Explicit null bounds changed');
    $check($api->camera->getExports(
        limit: 20,
        skip: 40,
        from: 1793700000,
        to: 1793703600,
        camera_ids: ['100-synthetic:01'],
    ) === [], 'Bounded export result changed');
    $check($api->camera->getExports(from: 0, to: 0) === [], 'Zero bounds changed');

    $bodies = [];

    foreach ($api->history as $transaction) {
        $request = $transaction['request'];
        $check($request->getMethod() === 'POST', 'Export method changed');
        $check(
            (string) $request->getUri() === 'https://camera.example.test/exported_records'
                . '?op=FIND&access_token=synthetic-token',
            'Export address/auth changed',
        );
        $check($request->getHeaderLine('Accept') === 'application/json', 'Export Accept changed');
        $bodies[] = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    $check($bodies[0] === [
        'limit' => 100,
        'skip' => 0,
    ], 'Default body changed');
    $check($bodies[1] === $bodies[0], 'Null bounds must be omitted');
    $check($bodies[2] === [
        'limit' => 20,
        'skip' => 40,
        'sources' => [
            [
                'id' => '100-synthetic:01',
                'type' => 'camera',
            ],
        ],
        'timeframe_since' => 1793700000,
        'timeframe_until' => 1793703600,
    ], 'Explicit bounds/filter body changed');
    $check($bodies[3] === [
        'limit' => 100,
        'skip' => 0,
        'timeframe_since' => 0,
        'timeframe_until' => 0,
    ], 'Zero bounds must not be omitted');

    $handler->append(
        new Response(401, [], '{"success":false,"code":"UNAUTHORIZED"}'),
        new Response(201, [], '{"success":true,"result":{"id":"compat-export","status":"ready"}}'),
        new Response(400, [], '{"success":false,"code":"BAD_REQUEST"}'),
        new Response(500, [], '{"success":false,"code":"SERVER_ERROR"}'),
        new Response(401, [], '{"success":false,"code":"UNAUTHORIZED"}'),
    );
    $export = $api->camera->exportMp4('100-synthetic:01', 1793700000, 1793703600);
    $check($export->getId() === 'compat-export', 'Export retry failed');
    $check(count($loginHistory) === 1, 'Export must log in exactly once');
    $check(count($api->history) === 6, 'Export must make exactly two HTTP requests');
    $check($account->getAccessToken() === 'refreshed-token', 'Login did not rotate the account token');

    foreach ([4 => 'synthetic-token', 5 => 'refreshed-token'] as $index => $token) {
        $request = $api->history[$index]['request'];
        $check(
            (string) $request->getUri() === 'https://camera.example.test/cameras/100-synthetic:01/archive'
                . '?op=EXPORT&access_token=' . $token,
            'Retry lost the colon camera ID, operation or current token',
        );
        $check($request->getMethod() === 'POST', 'Retry changed the export method');
        $check(json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR) === [
            'start_time' => 1793700000,
            'end_time' => 1793703600,
        ], 'Retry changed the export body');
    }

    foreach (['BAD_REQUEST', 'SERVER_ERROR', 'UNAUTHORIZED'] as $error) {
        try {
            $api->camera->exportMp4('100-synthetic:01', 1793700000, 1793703600);
            throw new RuntimeException('Expected the provider export failure');
        } catch (ExportRequestFailedException $exception) {
            $check($exception->getMessage() === $error, 'Provider export failure changed');
            $check($exception->getCode() === Constants::EXCEPTION_EXPORT_FAILED, 'Provider error code changed');
        }
    }

    $check(count($loginHistory) === 1, 'A repeated 401 must not trigger another login');
    echo 'OK: ' . $assertions . " assertions; all SDK source loaded without warnings/deprecations\n";
} finally {
    if (isset($sdkLoader)) {
        $sdkLoader->unregister();
    }

    restore_error_handler();
    error_reporting($previousReporting);
}
