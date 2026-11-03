<?php

namespace IVideon\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use IVideon\Account;
use IVideon\Constants;
use IVideon\Flows\AbstractFlow;

class Api
{
    /**
     * @var AbstractFlow|null
     */
    protected ?AbstractFlow $flow = null;

    protected bool $triedRelogin = false;
    /**
     * @var Account
     */
    protected Account $account;

    /**
     * @var Servers
     */
    public Servers $servers;

    /**
     * @var Camera
     */
    public Camera $camera;

    public function __construct(Account $account, AbstractFlow $flow)
    {
        $this->flow = $flow;
        $this->account = $account;
        if (empty($account->getAccessToken())) {
            $this->flow->login($account);
        }
        $this->servers = new Servers($this);
        $this->camera = new Camera($this);
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param   string  $method
     * @param   string  $uri
     * @param   array   $options
     *
     * @return array|null
     * @throws GuzzleException
     */
    public function request(string $method, string $uri, array $options = []): ?array
    {
        $options['query']['access_token'] = $this->account->getAccessToken();

        try {
            $client = $this->buildClient();
            $requestOptions = $options;

            if (array_key_exists('handler', $requestOptions)) {
                if ($requestOptions['handler'] !== null) {
                    $config = $client->getConfig();
                    $config['handler'] = $requestOptions['handler'];
                    $client = new Client($config);
                }

                unset($requestOptions['handler']);
            }

            $response = $client->request($method, $uri, $requestOptions);

            // Export's custom stack returns HTTP errors as JSON instead of throwing.
            if ($response->getStatusCode() === 401 && !$this->triedRelogin) {
                return $this->retryUnauthorized($method, $uri, $options);
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            if ($exception->getCode() == 401 && !$this->triedRelogin) {
                return $this->retryUnauthorized($method, $uri, $options);
            }

            throw $exception;
        }
    }

    /**
     * @return Client
     */
    protected function buildClient(): Client
    {
        return new Client([
            'base_uri' => $this->account->getUserApiUrl(),
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => Constants::HTTPCLIENT_USERAGENT,
            ],
        ]);
    }

    private function retryUnauthorized(string $method, string $uri, array $options): ?array
    {
        $this->triedRelogin = true;
        $this->flow->login($this->account, true);

        return $this->request($method, $uri, $options);
    }
}
