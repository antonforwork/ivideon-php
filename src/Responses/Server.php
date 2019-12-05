<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Class Server.
 *
 * @method Camera[] getCameras()
 * @method bool getConnected()
 * @method string getCreatedAt()
 * @method string[] getFeatures()
 * @method string getId()
 * @method string getMacAddress()
 * @method string getName()
 * @method string getNetworkType()
 * @method bool getOnline()
 * @method string getOwner()
 * @method string getOwnerName()
 * @method string getSoftwareVersion()
 * @method string getTimezone()
 * @method bool isCameras()
 * @method bool isConnected()
 * @method bool isCreatedAt()
 * @method bool isFeatures()
 * @method bool isId()
 * @method bool isMacAddress()
 * @method bool isName()
 * @method bool isNetworkType()
 * @method bool isOnline()
 * @method bool isOwner()
 * @method bool isOwnerName()
 * @method bool isSoftwareVersion()
 * @method bool isTimezone()
 * @method $this setCameras(Camera[] $value)
 * @method $this setConnected(bool $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setFeatures(string[] $value)
 * @method $this setId(string $value)
 * @method $this setMacAddress(string $value)
 * @method $this setName(string $value)
 * @method $this setNetworkType(string $value)
 * @method $this setOnline(bool $value)
 * @method $this setOwner(string $value)
 * @method $this setOwnerName(string $value)
 * @method $this setSoftwareVersion(string $value)
 * @method $this setTimezone(string $value)
 * @method $this unsetCameras()
 * @method $this unsetConnected()
 * @method $this unsetCreatedAt()
 * @method $this unsetFeatures()
 * @method $this unsetId()
 * @method $this unsetMacAddress()
 * @method $this unsetName()
 * @method $this unsetNetworkType()
 * @method $this unsetOnline()
 * @method $this unsetOwner()
 * @method $this unsetOwnerName()
 * @method $this unsetSoftwareVersion()
 * @method $this unsetTimezone()
 *
 * @property Camera[] $cameras
 * @property bool $connected
 * @property string $created_at
 * @property string[] $features
 * @property string $id
 * @property string $mac_address
 * @property string $name
 * @property string $network_type
 * @property bool $online
 * @property string $owner
 * @property string $owner_name
 * @property string $software_version
 * @property string $timezone
 */
class Server extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'cameras'          => 'Camera[]',
        'connected'        => 'bool',
        'id'               => 'string',
        'created_at'       => 'string',
        'features'         => 'string[]',
        'mac_address'      => 'string',
        'name'             => 'string',
        'network_type'     => 'string',
        'online'           => 'bool',
        'owner'            => 'string',
        'owner_name'       => 'string',
        'software_version' => 'string',
        'timezone'         => 'string',
    ];
}
