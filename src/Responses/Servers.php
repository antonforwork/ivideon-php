<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Servers.
 *
 * @method ServerItems getResult()
 * @method bool getSuccess()
 * @method bool isResult()
 * @method bool isSuccess()
 * @method $this setResult(ServerItems $value)
 * @method $this setSuccess(bool $value)
 * @method $this unsetResult()
 * @method $this unsetSuccess()
 *
 * @property ServerItems $result
 * @property bool $success
 */
class Servers extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'success' => 'bool',
        'result'  => 'ServerItems',
    ];
}
