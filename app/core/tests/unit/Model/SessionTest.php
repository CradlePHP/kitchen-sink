<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\Service;
use Cradle\App\Core\Model\Session;

/**
 * Session Model Test
 *
 * Columns
 * - session_token       string   generated
 * - session_secret      string   generated
 * - session_permissions JSON     REQUIRED
 * - session_status      string   REQUIRED
 * - comment_active      bool     1
 * - comment_type        string
 * - comment_flag        small    0
 * - comment_created     datetime generated
 * - comment_updated     datetime generated
 *
 * Relations
 * - auth*
 * - app*
 *
 * @vendor   Salaaap
 * @package  Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Cradle_App_Core_Model_Session_Test extends \Codeception\Test\Unit
{
    /**
     * @var Session $object
     */
    protected $object;

    /**
     * @covers Cradle\App\Core\Model\Session::__construct
     */
    protected function setUp()
    {
        $service = new Service(cradle());
        $this->object = new Session($service);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseCreate
     */
    public function testDatabaseCreate()
    {
        // just create something
        $actual = $this->object->databaseCreate(array(
            'session_permissions' => '["public_profile", "personal_profile", "user_profile"]',
            'session_status' => 'REQUEST'
        ));

        // then link to app, auth, and profile
        $this->object->linkApp($actual['session_id'], 1);
        $this->object->linkAuth($actual['session_id'], 1);

        $this->assertEquals(2, $actual['session_id']);
        $this->assertEquals('REQUEST', $actual['session_status']);
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
     * @covers Cradle\App\Core\Model\Session::databaseSearch
     */
    public function testDatabaseSearch()
    {
        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate(array(
            'session_id' => 2,
            'session_status' => 'ACCESS'
        ));

        $this->assertEquals(2, $actual['session_id']);
        $this->assertEquals('ACCESS', $actual['session_status']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(2);

        $this->assertEquals(2, $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('session', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('session', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexSearch
     */
    public function testIndexSearch()
    {
        $actual = $this->object->indexSearch();

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['session_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Session::indexUpdate
     */
    public function testIndexUpdate()
    {
        $actual = $this->object->indexUpdate(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        // now, test it
        $this->assertEquals('session', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
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

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheCreateDetail
     */
    public function testCacheCreateDetail()
    {
        $actual = $this->object->cacheCreateDetail(1, 1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheCreateSearch
     */
    public function testCacheCreateSearch()
    {
        $actual = $this->object->cacheCreateSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheDetail
     */
    public function testCacheDetail()
    {
        $actual = $this->object->cacheDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['session_id']);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheDetailExists
     */
    public function testCacheDetailExists()
    {
        $actual = $this->object->cacheDetailExists(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertTrue($actual);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheSearch
     */
    public function testCacheSearch()
    {
        $actual = $this->object->cacheSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['session_id']);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheSearchExists
     */
    public function testCacheSearchExists()
    {
        $actual = $this->object->cacheSearchExists([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertTrue($actual);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheRemoveDetail
     */
    public function testCacheRemoveDetail()
    {
        $actual = $this->object->cacheRemoveDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\App\Core\AbstractModel::cacheRemoveSearch
     */
    public function testCacheRemoveSearch()
    {
        $actual = $this->object->cacheRemoveSearch([]);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual);
    }
}
