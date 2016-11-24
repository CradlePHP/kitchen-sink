<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

class Cradle_App_Core_Model_Auth_Test extends PHPUnit_Framework_TestCase
{
    protected $object;
    protected $profile;
    protected $service;

    /**
     * @covers Cradle\App\Core\Model\Auth::__construct
     */
    protected function setUp()
    {
        $this->service  = new Cradle\App\Core\Service(cradle());
        $this->object   = new Cradle\App\Core\Model\Auth($this->service);
        $this->profile  = new Cradle\App\Core\Model\Profile($this->service);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseCreate
     */
    public function testDatabaseCreate()
    {
        // create auth
        $auth = $this->object->databaseCreate([
            'auth_slug' => 'foobar@email.com',
            'auth_password' => 'foobar',
            'auth_permissions' => 'foobar'
        ]);

        // create profile
        $profile = $this->profile->databaseCreate([
            'profile_name' => 'Barfoo 1',
            'profile_location' => 'foobar'
        ]);

        $this->object->linkProfile($auth['auth_id'], $profile['profile_id']);

        $this->assertEquals(1, $auth['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     */
    public function testDatabaseDetail()
    {
        // create another auth/profile
        // create auth
        $auth = $this->object->databaseCreate([
            'auth_slug' => 'foobar2@email.com',
            'auth_password' => 'foobar2',
            'auth_permissions' => 'foobar'
        ]);

        // create profile
        $profile = $this->profile->databaseCreate([
            'profile_name' => 'Barfoo 2',
            'profile_location' => 'foobar'
        ]);

        $this->object->linkProfile($auth['auth_id'], $profile['profile_id']);

        // get the auth
        $actual = $this->object->databaseDetail(2);

        $this->assertEquals(2, $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(1);

        $this->assertEquals(1, $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseSearch
     */
    public function testDatabaseSearch()
    {
        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(2, $actual['rows'][0]['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate(array(
            'auth_id' => 2,
            'auth_slug' => 'foobarbaz@email.com'
        ));

        $this->assertEquals(2, $actual['auth_id']);
        $this->assertEquals('foobarbaz@email.com', $actual['auth_slug']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('foobarbaz@email.com');

        // it returns a boolean so we're expecting it to be true because
        // the slug provided is saved in the database
        $this->assertTrue($actual);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(2);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('auth', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(2);

        $this->assertEquals(2, $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(2);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('auth', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexSearch
     */
    public function testIndexSearch()
    {
        $actual = $this->object->indexSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(2, $actual['rows'][0]['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
     */
    public function testIndexUpdate()
    {
        // create auth
        $auth = $this->object->databaseCreate([
            'auth_slug' => 'foobar3@email.com',
            'auth_password' => 'foobar',
            'auth_permissions' => 'foobar'
        ]);

        // create profile
        $profile = $this->profile->databaseCreate([
            'profile_name' => 'Barfoo 3',
            'profile_location' => 'foobar'
        ]);

        $this->object->linkProfile($auth['auth_id'], $profile['profile_id']);

        // index it
        $this->object->indexCreate($auth['auth_id']);

        // update the data stored in elastic
        $actual = $this->object->indexUpdate($auth['auth_id']);

        // now, test it
        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('auth', $actual['_type']);
        $this->assertEquals($auth['auth_id'], $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getCreateErrors
     * @covers Cradle\App\Core\Model\Auth::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getLoginErrors
     */
    public function testGetLoginErrors()
    {
        $actual = $this->object->getLoginErrors(array());

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getUpdateErrors
     * @covers Cradle\App\Core\Model\Auth::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = $this->object->getUpdateErrors(array());

        $this->assertEquals('Invalid ID', $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::linkProfile
     */
    public function testLinkProfile()
    {
        // link profile
        $actual = $this->object->linkProfile(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['auth_id']);
        $this->assertEquals(999, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::unlinkProfile
     */
    public function testUnlinkProfile()
    {
        $actual = $this->object->unlinkProfile(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['auth_id']);
        $this->assertEquals(999, $actual['profile_id']);
    }
}
