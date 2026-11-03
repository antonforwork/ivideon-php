<?php

namespace IVideon\Flows;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use IVideon\Account;
use IVideon\Constants;
use IVideon\Exceptions\LoginException;

class WebFlow extends AbstractFlow
{
    protected function configureHttpClient(array $httpClientConfig = []): void
    {
        $config = array_merge($httpClientConfig, [
            'cookies' => CookieJar::fromArray(
                [
                    'ivideon-preferredCdn' => 'https%3A//static.iv-cdn.com',
                    'ivideon-lid'          => (string) time(),
                ],
                '.ivideon.com'),
            'headers' => [
                'User-Agent' => Constants::HTTPCLIENT_USERAGENT,
            ],
        ]);

        $this->httpClient = new Client($config);
    }

    /**
     * @param   Account  $account
     * @param   bool     $forceLogin
     *
     * @return $this|void
     * @throws LoginException
     * @throws GuzzleException
     */
    public function login(Account $account, bool $forceLogin = false)
    {
        if (!empty($account->getAccessToken()) && !$forceLogin) {
            return $this;
        }
        // step 2: Build post request
        $postVars = [];
        $postVars['grant_type'] = 'password';
        $postVars['client_version'] = Constants::CLIENT_VERSION;
        $postVars['device_type'] = Constants::HTTPCLIENT_USERAGENT;
        $postVars['username'] = $account->getLogin();
        $postVars['password'] = $account->getPassword();
        $postVars['trusted_device'] = true;

        $redirectedPageRequest = $this->httpClient
            ->request('POST', Constants::ENDPOINT_LOGIN,
                [
                    'form_params' => $postVars,
                    'headers'     => [
                        'Authorization'=> 'Basic '.base64_encode('web-client'),
                        'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                        'Accept-Language' => 'ru',
                        'User-Agent'=> Constants::HTTPCLIENT_USERAGENT,
                        'Referer'         => Constants::ENDPOINT_LOGIN,
                    ],
                ]);

        $jsonBody = $redirectedPageRequest->getBody()->getContents();

        $json = json_decode($jsonBody);

        if(!isset($json->owner_id)){
            throw new LoginException('unable to find owner_id, probably incorrect l/p', Constants::EXCEPTION_INVALID_LOGIN);
        }

        $account->setAccessToken($json->access_token);
        $account->setUserApiUrl('https://'.$json->api_host);
        $account->setUserId($json->owner_id);
    }
}
