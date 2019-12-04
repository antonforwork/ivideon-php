<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Class Camera.
 *
 * @method bool getConnected()
 * @method string getCreatedAt()
 * @method string[] getFeatures()
 * @method int getHeight()
 * @method string getId()
 * @method string getMode()
 * @method string getName()
 * @method bool getOnline()
 * @method string[] getPermissions()
 * @method int getRotate()
 * @method string getServer()
 * @method bool getSoundEnabled()
 * @method int getWidth()
 * @method bool isConnected()
 * @method bool isCreatedAt()
 * @method bool isFeatures()
 * @method bool isHeight()
 * @method bool isId()
 * @method bool isMode()
 * @method bool isName()
 * @method bool isOnline()
 * @method bool isPermissions()
 * @method bool isRotate()
 * @method bool isServer()
 * @method bool isSoundEnabled()
 * @method bool isWidth()
 * @method $this setConnected(bool $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setFeatures(string[] $value)
 * @method $this setHeight(int $value)
 * @method $this setId(string $value)
 * @method $this setMode(string $value)
 * @method $this setName(string $value)
 * @method $this setOnline(bool $value)
 * @method $this setPermissions(string[] $value)
 * @method $this setRotate(int $value)
 * @method $this setServer(string $value)
 * @method $this setSoundEnabled(bool $value)
 * @method $this setWidth(int $value)
 * @method $this unsetConnected()
 * @method $this unsetCreatedAt()
 * @method $this unsetFeatures()
 * @method $this unsetHeight()
 * @method $this unsetId()
 * @method $this unsetMode()
 * @method $this unsetName()
 * @method $this unsetOnline()
 * @method $this unsetPermissions()
 * @method $this unsetRotate()
 * @method $this unsetServer()
 * @method $this unsetSoundEnabled()
 * @method $this unsetWidth()
 *
 * @property bool $connected
 * @property string $created_at
 * @property string[] $features
 * @property int $height
 * @property string $id
 * @property string $mode
 * @property string $name
 * @property bool $online
 * @property string[] $permissions
 * @property int $rotate
 * @property string $server
 * @property bool $sound_enabled
 * @property int $width
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
