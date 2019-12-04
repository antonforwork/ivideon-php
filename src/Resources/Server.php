<?php

namespace IVideon\Resources;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Class Server.
 *
 * @method Camera[] getCameras()
 * @method bool getConnected()
 * @method string getId()
 * @method string getCreatedAt()
 * @method string[] getFeatures()
 * @method string getMacAddress()
 * @method string getName()
 * @method string getNetworkType()
 * @method bool getOnline()
 * @method string getOwner()
 * @method string getOwnerName()
 * @method string getSoftwareVersion()
 */
class Server extends LazyJsonMapper
{
    const JSON_PROPERTY_MAP = [
        'cameras'          => 'Resources\\Cameras[]',
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
    ];
}
