<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

class Cradle_App_Core_Model_Session_Test extends PHPUnit_Framework_TestCase
{
    protected $object;

    /**
     * @covers Cradle\App\Core\Model\Session::__construct
     */
    protected function setUp()
    {
        $service = new Cradle\App\Core\Service(cradle());
        $this->object = new Cradle\App\Core\Model\Session($service);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseCreate
     */
    public function testDatabaseCreate()
    {
        // just create something
        $actual = $this->object->databaseCreate(array(
            'session_permissions' => 'foobar',
            'session_status' => 'foobar'
        ));

        // then link to app, auth, and profile
        $this->object->linkApp($actual['session_id'], 1);
        $this->object->linkAuth($actual['session_id'], 1);

        $this->assertEquals(1, $actual['session_id']);
        $this->assertEquals('foobar', $actual['session_permissions']);
        $this->assertEquals('foobar', $actual['session_status']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseDetail
     */
    public function testDatabaseDetail()
    {
        $actual = $this->object->databaseDetail(1);

        $this->assertEquals(1, $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(1);

        $this->assertEquals(1, $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseSearch
     */
    public function testDatabaseSearch()
    {
        // create another session
        $actual = $this->object->databaseCreate(array(
            'session_permissions' => 'foobar',
            'session_status' => 'foobar'
        ));

        // then link to app, auth, and profile
        $this->object->linkApp($actual['session_id'], 1);
        $this->object->linkAuth($actual['session_id'], 1);

        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertTrue(is_numeric($actual['rows'][0]['session_id']));
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate(array(
            'session_id' => 2,
            'session_permissions' => 'foobarbaz',
            'session_status' => 'foobarbaz'
        ));

        $this->assertTrue(is_numeric($actual['session_id']));
        $this->assertEquals(2, $actual['session_id']);
        $this->assertEquals('foobarbaz', $actual['session_permissions']);
        $this->assertEquals('foobarbaz', $actual['session_status']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(2);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('session', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(2);

        $this->assertEquals(2, $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(2);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('session', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexSearch
     */
    public function testIndexSearch()
    {
        $actual = $this->object->indexSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(2, $actual['rows'][0]['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexUpdate
     */
    public function testIndexUpdate()
    {
        // bring back!
        $this->object->indexCreate(2);

        $actual = $this->object->indexUpdate(2);

        // now, test it
        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('session', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::getAccessErrors
     */
    public function testGetAccessErrors()
    {
        $actual = $this->object->getAccessErrors(array());

        $this->assertEquals('Cannot be empty', $actual['code']);
        $this->assertEquals('Cannot be empty', $actual['client_id']);
        $this->assertEquals('Cannot be empty', $actual['client_secret']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::getCreateErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['session_permissions']);
        $this->assertEquals('Invalid ID', $actual['auth_id']);
        $this->assertEquals('Invalid ID', $actual['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::getUpdateErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = $this->object->getUpdateErrors(array());

        $this->assertEquals('Invalid ID', $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::linkApp
     */
    public function testLinkApp()
    {
        $actual = $this->object->linkApp(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::linkAuth
     */
    public function testLinkAuth()
    {
        $actual = $this->object->linkAuth(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::unlinkApp
     */
    public function testUnlinkApp()
    {
        $actual = $this->object->unlinkApp(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::unlinkAuth
     */
    public function testUnlinkAuth()
    {
        $actual = $this->object->unlinkAuth(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['auth_id']);
    }
}
