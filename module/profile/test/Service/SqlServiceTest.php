<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Profile\Service;

/**
 * SQL service test
 * Profile Model Test
 *
 * @vendor   Acme
 * @package  Profile
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Profile_Service_SqlServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SqlService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('sql');
    }

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::create
     */
    public function testCreate()
    {
        $actual = $this->object->create([
            'profile_name' => 'John Doe',
        ]);

        $this->assertEquals(2, $actual['profile_id']);
    }

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::get
     */
    public function testGet()
    {
        $actual = $this->object->get(1);

        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::search
     */
    public function testSearch()
    {
        $actual = $this->object->search();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['profile_id']);
    }

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::update
     */
    public function testUpdate()
    {
        $actual = $this->object->update([
            'profile_id' => 2,
            'profile_name' => 'John Doe',
        ]);

        $this->assertEquals(2, $actual['profile_id']);
    }

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::remove
     */
    public function testRemove()
    {
        $actual = $this->object->remove(2);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(2, $actual['profile_id']);
    }
}
