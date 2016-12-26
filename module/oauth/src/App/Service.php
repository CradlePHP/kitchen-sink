<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
namespace Cradle\Module\Oauth\App;

use Cradle\Module\Oauth\App\Service\SqlService;
use Cradle\Module\Oauth\App\Service\RedisService;
use Cradle\Module\Oauth\App\Service\ElasticService;

use Cradle\Module\Utility\Service\NoopService;

use Cradle\Module\Utility\ServiceInterface;

/**
 * Service layer
 *
 * @vendor   Acme
 * @package  App
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Service implements ServiceInterface
{
    /**
     * Returns a service. To prevent having to define a method per
     * service, instead we roll everything into one function
     *
     * @param *string $name
     *
     * @return object
     */
    public static function get($name)
    {
        if ($name === 'sql') {
            $resource = cradle()->package('global')->service('sql-main');
            return new SqlService($resource);
        }

        if ($name === 'redis') {
            $resource = cradle()->package('global')->service('redis-main');
            return new RedisService($resource);
        }

        if ($name === 'elastic') {
            $resource = cradle()->package('global')->service('elastic-main');
            return new ElasticService($resource);
        }

        return new NoopService();
    }
}
