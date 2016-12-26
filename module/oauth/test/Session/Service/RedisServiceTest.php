<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Session\Service;

/**
 * Redis service test
 *
 * @vendor   Salaaap
 * @package  OAuth
 * @author   Christian Blanquera <cblanquera@openovate.com>
 */
class Cradle_Module_Oauth_Session_Service_RedisServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RedisService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Oauth\Session\Service\RedisService::__construct
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

        $this->assertEquals(1, $actual['session_id']);
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
        $this->assertEquals(1, $actual['rows'][0]['session_id']);
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
    }
}
