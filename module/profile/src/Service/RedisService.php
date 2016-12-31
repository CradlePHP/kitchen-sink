<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Profile\Service;

use Cradle\Module\Profile\Service;

use Predis\Client as Resource;

use Cradle\Module\Utility\Service\RedisServiceInterface;
use Cradle\Module\Utility\Service\AbstractRedisService;

/**
 * Profile Redis Service
 *
 * @vendor   Acme
 * @package  profile
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class RedisService extends AbstractRedisService implements RedisServiceInterface
{
    /**
     * @const CACHE_SEARCH Cache search key
     */
    const CACHE_SEARCH = 'core-profile-search';

    /**
     * @const CACHE_DETAIL Cache detail key
     */
    const CACHE_DETAIL = 'core-profile-detail';

    /**
     * Registers the resource for use
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->sql = Service::get('sql');
        $this->elastic = Service::get('elastic');
    }
}
