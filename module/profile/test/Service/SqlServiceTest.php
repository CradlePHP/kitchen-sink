<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Profile\Service;

/**
 * SQL service test
  * Profile Model Test
  *
  * Columns
  * - profile_name         string    REQUIRED
  * - profile_slug         string
  * - profile_email        string
  * - profile_phone        string
  * - profile_detail       float
  * - profile_image        string
  * - profile_company      string
  * - profile_job          string
  * - profile_gender       string
  * - profile_birth        date
  * - profile_website      string
  * - profile_facebook     string
  * - profile_linkedin     string
  * - profile_twitter      string
  * - profile_google       string
  * - profile_active       bool     1
  * - profile_type         string
  * - profile_flag         small    0
  * - profile_created      datetime generated
  * - profile_updated      datetime generated
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
        $actual = $this->object->create(array(
            'profile_email'     => 'model_profile_1@email.com',
            'profile_name'      => 'Model Profile 1',
            'profile_locale'  => 'philippines'
        ));

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
        $actual = $this->object->update(array(
            'profile_id' => 2,
            'profile_name' => 'Model Profile 3'
        ));

        $this->assertTrue(is_numeric($actual['profile_id']));
        $this->assertEquals(2, $actual['profile_id']);
        $this->assertEquals('Model Profile 3', $actual['profile_name']);
    }

    /**
     * @covers Cradle\Module\Profile\Service\SqlService::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('john@doe.com');

        $this->assertTrue(!empty($actual));
        $this->assertEquals('john@doe.com', $actual['profile_email']);
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
