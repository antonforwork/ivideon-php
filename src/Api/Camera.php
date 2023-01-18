<?php

namespace IVideon\Api;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use IVideon\Constants;
use IVideon\Exceptions\ExportRequestFailedException;
use IVideon\Responses\ExportResult;
use IVideon\Responses\ExportResultSummary;
use IVideon\Responses\Exports;

class Camera
{
    /**
     * Camera constructor.
     *
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Problem with colon in URL
     * IVideon require ID in url like "100-xxxxxxx:01" (see colon)
     * If we push colon in URL guzzle throw exception
     * If we will encode colon - IVideon API dont understand.
     *
     * So we have to FORCE send colon with middleware
     *
     * @see https://github.com/guzzle/guzzle/issues/1550
     *
     * @param $cameraId
     * @param $query
     *
     * @return \Closure
     */
    protected function buildDirtyRequest($cameraId, $query)
    {
        return function (callable $handler) use ($cameraId, $query) {
            return function (
                Request $request,
                array $options
            ) use ($handler, $cameraId, $query) {
                $parts = [
                    'scheme' => parse_url($this->api->getAccount()->getUserApiUrl(), PHP_URL_SCHEME),
                    'host'   => parse_url($this->api->getAccount()->getUserApiUrl(), PHP_URL_HOST),
                    'path'   => 'cameras/'.$cameraId.'/archive',
                    'query'  => http_build_query($query),
                ];
                $request = $request->withUri(Uri::fromParts($parts));

                return $handler($request, $options);
            };
        };
    }

    /**
     * Delete export records after downloading.
     *
     * @param $exportId
     *
     * @return bool
     */
    public function deleteExport($exportId)
    {
        $response = $this->api->request('POST', 'exported_records/'.$exportId, [
            'query' => [
                'op' => 'DELETE',
            ],
        ]);

        if (isset($response['success']) && $response['success']) {
            return true;
        }

        return false;
    }

    /**
     * Create export request.
     *
     * @param $cameraId
     * @param $start  - Unix timestamp in server timezone
     * @param $end    - Unix timestamp in server timezone
     *
     * @return ExportResult
     * @throws ExportRequestFailedException
     */
    public function exportMp4($cameraId, $start, $end)
    {
        $handler = new HandlerStack();
        $handler->setHandler(new CurlHandler());
        $handler->push($this->buildDirtyRequest($cameraId, [
            'op'           => 'EXPORT',
            'access_token' => $this->api->getAccount()->getAccessToken(),
        ]));
        $response = $this->api->request('POST', 'cameras/'.urlencode($cameraId).'/archive?', [
            'handler' => $handler,
            'json'    => [
                'start_time' => $start,
                'end_time'   => $end,
            ],
        ]);

        if (isset($response['success']) && $response['success'] == false) {
            throw new ExportRequestFailedException($response['code'] ?? 'no defined error', Constants::EXCEPTION_EXPORT_FAILED);
        }

        return (new ExportResultSummary($response))->getResult();
    }

    /**
     * @param   int       $limit
     * @param   int       $skip
     * @param   int|null  $from  Unix timestamp in server timezone
     * @param   int|null  $to    Unix timestamp in server timezone
     * @param   array     $camera_ids
     *
     * @return ExportResult
     */
    public function getExports(int $limit = 100, int $skip = 0, int $from = NULL, int $to = NULL, array $camera_ids = [])
    {
        $json = [
            'limit' => $limit,
            'skip'  => $skip,
        ];

        if(count($camera_ids) > 0) {
            $json['sources'] = array_map(function (string $id) {
                return [
                    'id' => $id,
                    'type' => 'camera'
                ];
            }, $camera_ids);
        }

        if(!is_null($from)) {
            $json['timeframe_since'] = $from;
        }

        if(!is_null($to)) {
            $json['timeframe_until'] = $to;
        }

        $response = $this->api->request('POST', 'exported_records', [
            'query' => [
                'op' => 'FIND',
            ],
            'json' => $json,
        ]);

        return (new Exports($response))->getResult()->getItems();
    }
}
