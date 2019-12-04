<?php

namespace IVideon\Resources;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Class Camera.
 *
 * @method bool getConnected()
 * @method string getCreatedAt()
 * @method string[] getFeatures()
 * @method int getHeight()
 * @method string getId()
 * @method string getName()
 * @method bool getOnline()
 * @method string[] getPermissions()
 * @method int getRotate()
 * @method string getServer()
 * @method int getWidth()
 * @method bool getSoundEnabled()
 */
class Camera extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'connected'     => 'bool',
        'created_at'    => 'string',
        'features'      => 'string[]',
        'height'        => 'int',
        'id'            => 'string',
        'mode'          => 'string',
        'name'          => 'string',
        'online'        => 'bool',
        'permissions'   => 'string[]',
        'rotate'        => 'int',
        'server'        => 'string',
        'width'         => 'int',
        'sound_enabled' => 'bool',
    ];
}
