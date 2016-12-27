<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Auth\Service;

/**
 * Redis service test
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_Auth_Service_RedisServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RedisService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\RedisService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('redis');
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function testCreateDetail()
    {
        $actual = $this->object->createDetail(1, 1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);

        $actual = $this->object->createDetail('john@doe.com', 1);
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createSearch
     */
    public function testCreateSearch()
    {
        $actual = $this->object->createSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);

        $actual = $this->object->createSearch(['q' => 'john']);
        $this->assertEquals(1, $actual);

        $actual = $this->object->createSearch(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     */
    public function testGetDetail()
    {
        $actual = $this->object->getDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->getDetail('john@doe.com');
        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->getDetail(99999);
        $this->assertFalse($actual);

        $actual = $this->object->getDetail('foobar');
        $this->assertFalse($actual);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::hasDetail
     */
    public function testHasDetail()
    {
        $actual = $this->object->hasDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertTrue($actual);

        $actual = $this->object->hasDetail(9999);
        $this->assertFalse($actual);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     */
    public function testGetSearch()
    {
        $actual = $this->object->getSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //keyword
        $actual = $this->object->getSearch(['q' => 'john']);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //filter
        $actual = $this->object->getSearch(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::hasSearch
     */
    public function testHasSearch()
    {
        $actual = $this->object->hasSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertTrue($actual);

        $actual = $this->object->hasSearch(['q' => 'foobar']);
        $this->assertFalse($actual);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     */
    public function testRemoveDetail()
    {
        $actual = $this->object->removeDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testRemoveSearch()
    {
        $actual = $this->object->removeSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);

        $actual = $this->object->removeSearch(['q' => 'john']);
        $this->assertEquals(1, $actual);

        $actual = $this->object->removeSearch(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual);
    }
}
