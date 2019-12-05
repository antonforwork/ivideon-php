<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * ExportItems.
 *
 * @method ExportResult[] getItems()
 * @method bool isItems()
 * @method $this setItems(ExportResult[] $value)
 * @method $this unsetItems()
 *
 * @property ExportResult[] $items
 */
class ExportItems extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'items' => 'ExportResult[]',
    ];
}
