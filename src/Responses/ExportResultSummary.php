<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * ExportResultSummary.
 *
 * @method ExportResult getResult()
 * @method bool isResult()
 * @method $this setResult(ExportResult $value)
 * @method $this unsetResult()
 *
 * @property ExportResult $result
 */
class ExportResultSummary extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'result' => 'ExportResult',
    ];
}
