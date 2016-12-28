<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\{{capital name}}\Service;

use Cradle\Module\{{capital name}}\Service\SqlService;
use Cradle\Module\{{capital name}}\Service\RedisService;
use Cradle\Module\{{capital name}}\Service\ElasticService;

/**
 * Service layer test
 *
 * @vendor   Acme
 * @package  {{capital name}}
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_{{capital name}}_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\{{capital name}}\Service::get
     */
    public function testGet()
    {
        $this->assertInstanceOf(SqlService::class, Service::get('sql'));
        $this->assertInstanceOf(RedisService::class, Service::get('redis'));
        $this->assertInstanceOf(ElasticService::class, Service::get('elastic'));
    }
}
