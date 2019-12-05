<?php

namespace IVideon;

class Constants
{
    const HTTPCLIENT_USERAGENT = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

    const ENDPOINT_LOGIN = 'https://ru.ivideon.com/my/service/login';

    const EXCEPTION_INVALID_LOGIN = 1;
    const EXCEPTION_INVALID_PASSWORD = 2;
    const EXCEPTION_CACHEFILE_NOT_WRITEABLE = 3;

    const EXCEPTION_LOGIN_CSRF = 4;
    const EXCEPTION_LOGIN_CONFIG = 5;
    const EXCEPTION_LOGIN_USERID = 6;
    const EXCEPTION_EXPORT_FAILED = 7; // not defined error. See exception message for details
}
