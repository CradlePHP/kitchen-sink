<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Utility\Service;

/**
 * Comment Noop Service
 *
 * @vendor   Salaaap
 * @package  Utility
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class NoopService
{
    /**
     * Always return false
     *
     * @param *string $name
     * @param *array  $args
     *
     * @return false
     */
    public function __call($name, array $args)
    {
        return false;
    }
}
