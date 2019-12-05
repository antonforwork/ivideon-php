<?php

namespace IVideon\Flows;

use GuzzleHttp\Client;

abstract class AbstractFlow implements LoginFlowInterface
{
    /**
     * @var Client
     */
    protected $httpClient = null;

    public function __construct($httpClientConfig = [])
    {
        $this->configureHttpClient($httpClientConfig);
    }

    abstract protected function configureHttpClient($httpClientConfig = []);
}
