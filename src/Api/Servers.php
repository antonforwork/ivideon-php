<?php

namespace IVideon\Api;

class Servers
{
    /**
     * Servers constructor.
     *
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @param int $limit
     * @param int $skip
     *
     * @return Responses\Server[]
     */
    public function getServers($limit = 100, $skip = 0)
    {
        $response = $this->api->request('POST', 'servers', [
            'query' => [
                'op' => 'FIND',
            ],
            'json' => [
                'user'       => $this->api->getAccount()->getUserId(),
                'limit'      => $limit,
                'skip'       => $skip,
                'projection' => [
                    'cameras' => [
                        'id' => 1,
                    ],
                ],
            ],
        ]);

        return (new \IVideon\Responses\Servers($response))->getResult()->getItems();
    }
}
