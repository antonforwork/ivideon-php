<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * ServerItems.
 *
 * @method Server[] getItems()
 * @method bool isItems()
 * @method $this setItems(Server[] $value)
 * @method $this unsetItems()
 *
 * @property Server[] $items
 */
class ServerItems extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'items' => 'Server[]',
    ];
}
