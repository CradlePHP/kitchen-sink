<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\{{camel name 1}}\Service;

use Cradle\Module\{{camel name 1}}\Service;

use Predis\Client as Resource;

use Cradle\Module\Utility\Service\RedisServiceInterface;
use Cradle\Module\Utility\Service\AbstractRedisService;

/**
 * {{capital name}} Redis Service
 *
 * @vendor   Acme
 * @package  {{name}}
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class RedisService extends AbstractRedisService implements RedisServiceInterface
{
    /**
     * @const CACHE_SEARCH Cache search key
     */
    const CACHE_SEARCH = 'core-{{name}}-search';

    /**
     * @const CACHE_DETAIL Cache detail key
     */
    const CACHE_DETAIL = 'core-{{name}}-detail';

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
