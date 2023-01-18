# ivideon-php
![compatible](https://img.shields.io/badge/PHP%207-Compatible-brightgreen.svg) 

This is PHP Library which allow work with IVideon API

## Instalation
```sh
composer require antonforwork/ivideon-php
```

```php
$account = new Account('login_email', 'password', '.cache');
$api = new Api($account, new WebFlow());
```

Please use `.cache` file. 

In first run library will try to login with your email and password and store your `access_token`, `userid` and `userApiUrl` in cache file
all next runs will use these data. 

If `cacheFile` not set api will try login in every run (slowly)
 

### Print all server and cameras
```php
$servers = $api->servers->getServers();

foreach ($servers as $server) {
    echo $server->getTimezone(); // Important! see below
    foreach ($server->getCameras() as $camera) {
        echo $camera->getId() . PHP_EOL;
    }
}
```

### Create export request
```php
/* Create export request
 * Please note that $start, $end is unix timestamp values
 * Please verify that $start, $end in your server timezone
 * 
 * Important! 
 * IVideon not allow to export "empty" videos, when nobody in all period
 * ExportRequestFailedException will be thrown
 */
$exportResult = $api->camera->exportMp4($cameraId, $start, $end);
$exportId = $exportResult->getId();
```

### Get all exports
```php
$exports = $api->camera->getExports();
foreach ($exports as $export) {
    echo $export->getId() . ' = ' . $export->getStatus();

    if ($export->getStatus() == \IVideon\Responses\ExportResult::EXPORT_STATUS_READY) {
        echo ' = ' . $export->getVideoUrl();
    }
    echo PHP_EOL;
};

```

### Delete export file
```php
$api->camera->deleteExport($exportId); // bool
```

