# Changelog

## 1.0.2 — 2026-11-03

- Declare the optional `getExports()` time bounds explicitly nullable for PHP 8.5, retaining PHP 8.0 support, parameter names, defaults and request bodies.
- Serialize the timestamp cookie as a string to satisfy Guzzle's cookie contract without changing its wire value.
- Configure export handlers on a dedicated HTTP client instead of deprecated request options. Retry an HTTP 401 once with the refreshed token while retaining the colon camera ID, body and non-401 provider error handling.
- Add the sealed source-loading and request regression check: `php tests/php85-compatibility.php [path/to/vendor/autoload.php]`.
