<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Exports.
 *
 * @method ExportItems getResult()
 * @method bool getSuccess()
 * @method bool isResult()
 * @method bool isSuccess()
 * @method $this setResult(ExportItems $value)
 * @method $this setSuccess(bool $value)
 * @method $this unsetResult()
 * @method $this unsetSuccess()
 *
 * @property ExportItems $result
 * @property bool $success
 */
class Exports extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'success' => 'bool',
        'result'  => 'ExportItems',
    ];
}
