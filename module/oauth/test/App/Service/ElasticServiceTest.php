<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\App\Service;

/**
 * Elastic service test
 *
 * @vendor   Salaaap
 * @package  OAuth
 * @author   Christian Blanquera <cblanquera@openovate.com>
 */
class Cradle_Module_Oauth_App_Service_ElasticServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Oauth\App\Service\ElasticService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('elastic');
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::remove
     */
    public function testRemove()
    {
        $actual = $this->object->remove(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('app', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::create
     */
    public function testCreate()
    {
        $actual = $this->object->create(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('app', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     */
    public function testGet()
    {
        $actual = $this->object->get(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['app_id']);
        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\ElasticService::search
     */
    public function testSearch()
    {
        $actual = $this->object->search();

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
    }

    /**
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     */
    public function testUpdate()
    {
        // update the data stored in elastic
        $actual = $this->object->update(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        // now, test it
        $this->assertEquals('app', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }
}
