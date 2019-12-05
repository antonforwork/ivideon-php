<?php

namespace IVideon\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use IVideon\Account;
use IVideon\Constants;
use IVideon\Flows\AbstractFlow;

class Api
{
    /**
     * @var AbstractFlow|null
     */
    protected $flow = null;

    protected $triedRelogin = false;
    /**
     * @var Account
     */
    protected $account;

    /**
     * @var Servers
     */
    public $servers;

    /**
     * @var Camera
     */
    public $camera;

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
    public function getAccount()
    {
        return $this->account;
    }

    public function request($method, $uri, $options = [])
    {
        $options['query']['access_token'] = $this->account->getAccessToken();

        try {
            $response = $this->buildClient()->request($method, $uri, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            if ($exception->getCode() == 401 && !$this->triedRelogin) {
                // unauth
                // token invalid, try to re-login and re-try request
                $this->triedRelogin = true;
                $this->flow->login($this->account, true);

                return $this->request($method, $uri, $options);
            }

            throw $exception;
        }
    }

    /**
     * @return Client
     */
    protected function buildClient()
    {
        return new Client([
            'base_uri' => $this->account->getUserApiUrl(),
            'headers'  => [
                'Accept'     => 'application/json',
                'User-Agent' => Constants::HTTPCLIENT_USERAGENT,
            ],
        ]);
    }
}
