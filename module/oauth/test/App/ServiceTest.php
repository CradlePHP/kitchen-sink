<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\App\Service;

use Cradle\Module\Oauth\App\Service\SqlService;
use Cradle\Module\Oauth\App\Service\RedisService;
use Cradle\Module\Oauth\App\Service\ElasticService;

/**
 * Service layer test
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_App_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\Oauth\App\Service::get
     */
    public function testGet()
    {
        $this->assertInstanceOf(SqlService::class, Service::get('sql'));
        $this->assertInstanceOf(RedisService::class, Service::get('redis'));
        $this->assertInstanceOf(ElasticService::class, Service::get('elastic'));
    }
}
