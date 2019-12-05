<?php

namespace IVideon\Flows;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use IVideon\Account;
use IVideon\Constants;
use IVideon\Exceptions\LoginException;

class WebFlow extends AbstractFlow
{
    protected function configureHttpClient($httpClientConfig = [])
    {
        $config = array_merge($httpClientConfig, [
            'cookies' => CookieJar::fromArray(
                [
                    'ivideon-preferredCdn' => 'https%3A//static.iv-cdn.com',
                    'ivideon-lid'          => time(),
                ],
                '.ivideon.com'),
            'headers' => [
                'User-Agent' => Constants::HTTPCLIENT_USERAGENT,
            ],
        ]);
        $this->httpClient = new Client($config);
    }

    public function login(Account $account, $forceLogin = false)
    {
        if (!empty($account->getAccessToken()) && !$forceLogin) {
            return $this;
        }
        // step 1: Get login page for CSRF token
        $loginPageRequest = $this->httpClient->request('GET', Constants::ENDPOINT_LOGIN);
        $loginPageHtml = $loginPageRequest->getBody()->getContents();
        preg_match('/<input type="hidden" value="(.*?)" name="CSRF_TOKEN"/ims', $loginPageHtml, $regexp);
        if (empty($regexp) || !isset($regexp[1])) {
            throw new LoginException('no csrf token found on login page', Constants::EXCEPTION_LOGIN_CSRF);
        }
        // step 2: Build post request
        $postVars = [];
        $postVars['CSRF_TOKEN'] = $regexp[1];
        $postVars['LoginForm'] = [
            'username' => $account->getLogin(),
            'password' => $account->getPassword(),
        ];
        $redirectedPageRequest = $this->httpClient
            ->request('POST', Constants::ENDPOINT_LOGIN,
                [
                    'form_params' => $postVars,
                    'headers'     => [
                        'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                        'Accept-Language' => 'ru',
                        'Referer'         => Constants::ENDPOINT_LOGIN,
                    ],
                ]);

        // get js config object
        $regexp = null;

        $redirectedPageContent = $redirectedPageRequest->getBody()->getContents();
        preg_match('/var config = (.*?);\n/ism', $redirectedPageContent, $regexp);
        if (!isset($regexp[1])) {
            throw new LoginException('unable to read userApiUrl', Constants::EXCEPTION_LOGIN_CONFIG);
        }
        $config = @\json_decode($regexp[1]);
        if (empty($config) || !isset($config->openApi)) {
            throw new LoginException('unable to parse object or openApi prop not exists', Constants::EXCEPTION_LOGIN_CONFIG);
        }

        $openApi = $config->openApi;
        $account->setAccessToken($openApi->userAccessToken);
        $account->setUserApiUrl($openApi->userUrl);

        $regexp = null;
        preg_match('/var userId = (.*?);\n/ism', $redirectedPageContent, $regexp);
        if (!isset($regexp[1])) {
            throw new LoginException('unable to read userId', Constants::EXCEPTION_LOGIN_USERID);
        }
        $account->setUserId($regexp[1]);
    }
}
