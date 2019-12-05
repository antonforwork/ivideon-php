<?php

namespace IVideon;

use IVideon\Exceptions\InvalidArgumentException;

class Account
{
    protected $login;

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getCacheFile()
    {
        return $this->cacheFile;
    }

    protected $password;
    protected $cacheFile = null;
    protected $userId = null;
    protected $userApiUrl = null;
    protected $token = null;

    public function __construct($login, $password, $cacheFile = null)
    {
        if (empty($login)) {
            throw new InvalidArgumentException('login required', Constants::EXCEPTION_INVALID_LOGIN);
        }
        if (empty($password)) {
            throw new InvalidArgumentException('password required', Constants::EXCEPTION_INVALID_PASSWORD);
        }
        $this->login = $login;
        $this->password = $password;

        if (!empty($cacheFile)) {
            if (!file_exists($cacheFile)) {
                @touch($cacheFile);
            }
            if (!is_writeable($cacheFile)) {
                throw new InvalidArgumentException('cacheFile is not writeable', Constants::EXCEPTION_CACHEFILE_NOT_WRITEABLE);
            }
            $this->cacheFile = $cacheFile;
            $this->getCachedConfig();
        }
    }

    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param null $userId
     *
     * @return Account
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    protected function getCachedConfig()
    {
        $config = @\json_decode(file_get_contents($this->cacheFile), true);
        if (empty($config)) {
            return;
        }
        if (!empty($config['access_token'])) {
            $this->setAccessToken($config['access_token']);
        }
        if (!empty($config['userApiUrl'])) {
            $this->setUserApiUrl($config['userApiUrl']);
        }
        if (!empty($config['userId'])) {
            $this->setUserId($config['userId']);
        }
    }

    /**
     * Set User Api Url.
     *
     * @param $url
     *
     * @return $this
     */
    public function setUserApiUrl($url)
    {
        $this->userApiUrl = $url;

        return $this;
    }

    /**
     * Set token.
     *
     * @param $token
     *
     * @return $this
     */
    public function setAccessToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getAccessToken()
    {
        return $this->token;
    }

    public function getUserApiUrl()
    {
        return $this->userApiUrl;
    }

    protected function storeCacheFile()
    {
        if (empty($this->cacheFile)) {
            return;
        }
        if (empty($this->token) || empty($this->userApiUrl) || empty($this->userId)) {
            return;
        }
        $config = [
            'access_token' => $this->getAccessToken(),
            'userApiUrl'   => $this->getUserApiUrl(),
            'userId'       => $this->getUserId(),
        ];
        @file_put_contents($this->cacheFile, \json_encode($config));
    }

    public function eraseCacheFile()
    {
        if (empty($this->cacheFile)) {
            return;
        }
        @fclose(@fopen($this->cacheFile, 'w'));
        $this->setAccessToken(null);
        $this->setUserApiUrl(null);

        return $this;
    }

    public function __destruct()
    {
        $this->storeCacheFile();
    }
}
