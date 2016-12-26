<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Utility;

/**
 * Service interface
 *
 * @vendor   Salaaap
 * @package  Utility
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
interface ServiceInterface
{
    /**
     * Returns a service
     *
     * @param *string $name
     *
     * @return SqlService|RedisService|ElasticService|NoopService
     */
    public static function get($name);
}
