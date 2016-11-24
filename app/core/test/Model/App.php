<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

class Cradle_App_Core_Model_App_Test extends PHPUnit_Framework_TestCase
{
    protected $object;
    protected $profile;

    /**
     * @covers Cradle\App\Core\Model\App::__construct
     */
    protected function setUp()
    {
        $service = new Cradle\App\Core\Service(cradle());
        $this->object = new Cradle\App\Core\Model\App($service);
        $this->profile = new Cradle\App\Core\Model\Profile($service);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseCreate
     * @covers Cradle\App\Core\Model\Profile::databaseCreate
     * @covers Cradle\App\Core\Model\App::linkProfile
     */
    public function testDatabaseCreate()
    {
        $app = $this->object->databaseCreate([
            'app_name' => 'Foobar 1',
            'app_permissions' => 'foobar'
        ]);

        $profile = $this->profile->databaseCreate([
            'profile_name' => 'Barfoo 1',
            'profile_location' => 'foobar'
        ]);

        $this->object->linkProfile($app['app_id'], $profile['profile_id']);

        $this->assertEquals(1, $app['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseCreate
     * @covers Cradle\App\Core\Model\Profile::databaseCreate
     * @covers Cradle\App\Core\Model\App::linkProfile
     * @covers Cradle\App\Core\Model\App::databaseDetail
     */
    public function testDatabaseDetail()
    {
        $app = $this->object->databaseCreate([
            'app_name' => 'Foobar 2',
            'app_permissions' => 'foobar'
        ]);

        $profile = $this->profile->databaseCreate([
            'profile_name' => 'Barfoo 2',
            'profile_location' => 'foobar'
        ]);

        $this->object->linkProfile($app['app_id'], $profile['profile_id']);

        $actual = $this->object->databaseDetail($app['app_id']);
        $this->assertEquals('Foobar 2', $actual['app_name']);
        $this->assertEquals('Barfoo 2', $actual['profile_name']);

        $actual = $this->object->databaseDetail($app['app_token']);
        $this->assertEquals('Foobar 2', $actual['app_name']);
        $this->assertEquals('Barfoo 2', $actual['profile_name']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(2);
        $this->assertEquals(2, $actual['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseSearch
     */
    public function testDatabaseSearch()
    {
        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseCreate([
            'app_id' => 1,
            'app_name' => 'Foobar2'
        ]);

        $this->assertEquals(1, $actual['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(1);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('app', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(1);

        $this->assertEquals(1, $actual['app_id']);
        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(1);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('app', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexSearch
     */
    public function testIndexSearch()
    {
        $actual = $this->object->indexSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexUpdate
     */
    public function testIndexUpdate()
    {
        // we need to create a new one because the first one that we created
        // was already removed from the collection
        // create app
        $app = $this->object->databaseCreate([
            'app_name' => 'Foobar 2',
            'app_permissions' => 'foobar'
        ]);

        // create profile
        $profile = $this->profile->databaseCreate([
            'profile_name' => 'Barfoo 2',
            'profile_location' => 'foobar'
        ]);

        // link
        $this->object->linkProfile($app['app_id'], $profile['profile_id']);

        // index it
        $this->object->indexCreate($app['app_id']);

        // update the data stored in elastic
        $actual = $this->object->indexUpdate($app['app_id']);

        // now, test it
        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('app', $actual['_type']);
        $this->assertEquals($app['app_id'], $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::getCreateErrors
     * @covers Cradle\App\Core\Model\App::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['app_name']);
        $this->assertEquals('Cannot be empty', $actual['app_domain']);
        $this->assertEquals('Cannot be empty', $actual['app_permissions']);
        $this->assertEquals('Invalid ID', $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::getUpdateErrors
     * @covers Cradle\App\Core\Model\App::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = $this->object->getUpdateErrors(array());

        $this->assertEquals('Invalid ID', $actual['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::linkProfile
     */
    public function testLinkProfile()
    {
        // link profile
        $actual = $this->object->linkProfile(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['app_id']);
        $this->assertEquals(999, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::unlinkProfile
     */
    public function testUnlinkProfile()
    {
        $actual = $this->object->unlinkProfile(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['app_id']);
        $this->assertEquals(999, $actual['profile_id']);
    }
}
