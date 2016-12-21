<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\Service;
use Cradle\App\Core\Model\App;

/**
 * App Model Test
 *
 * Columns
 * - app_name        string   REQUIRED
 * - app_domain      string
 * - app_website     string
 * - app_permissions JSON     REQUIRED
 * - app_token       string   generated
 * - app_secret      string   generated
 * - app_active      bool     1
 * - app_type        string
 * - app_flag        small    0
 * - app_created     datetime generated
 * - app_updated     datetime generated
 *
 * @vendor   Salaaap
 * @package  Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Cradle_App_Core_Model_App_Test extends \Codeception\Test\Unit
{
    /**
     * @var App $object
     */
    protected $object;

    /**
     * @covers Cradle\App\Core\Model\App::__construct
     */
    protected function setUp()
    {
        $service = new Service(cradle());
        $this->object = new App($service);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseCreate
     */
    public function testDatabaseCreate()
    {
        //success
        $app = $this->object->databaseCreate([
            'app_name' => 'Model App 1',
            'app_permissions' => '["public_profile", "personal_profile", "user_profile"]'
        ]);

        $this->assertEquals(2, $app['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseDetail
     */
    public function testDatabaseDetail()
    {
        $actual = $this->object->databaseDetail(1);
        $this->assertEquals('Cradle App 1', $actual['app_name']);
        $this->assertEquals('John Doe', $actual['profile_name']);

        $actual = $this->object->databaseDetail('87d02468a934cb717cc15fe48a244f43');
        $this->assertEquals('Cradle App 1', $actual['app_name']);
        $this->assertEquals('John Doe', $actual['profile_name']);

        $actual = $this->object->databaseDetail(9999);
        $this->assertNull($actual);

        $actual = $this->object->databaseDetail('foobar');
        $this->assertNull($actual);
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

        //keyword
        $actual = $this->object->databaseSearch(['q' => 'Cradle']);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);

        //filter
        $actual = $this->object->databaseSearch(['filter' => ['profile_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate([
            'app_id' => 2,
            'app_name' => 'Model App 2'
        ]);

        $this->assertEquals(2, $actual['app_id']);
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
     * @covers Cradle\App\Core\Model\App::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(1);

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
     * @covers Cradle\App\Core\Model\App::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('app', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['app_id']);
        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexSearch
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
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\App::indexUpdate
     */
    public function testIndexUpdate()
    {
        // update the data stored in elastic
        $actual = $this->object->indexUpdate(1);

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

    /**
     * @covers Cradle\App\Core\Model\App::getCreateErrors
     * @covers Cradle\App\Core\Model\App::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors([]);

        $this->assertEquals('Cannot be empty', $actual['app_name']);
        $this->assertEquals('Cannot be empty', $actual['app_domain']);
        $this->assertEquals('Cannot be empty', $actual['app_permissions']);
        $this->assertEquals('Invalid ID', $actual['profile_id']);

        $actual = $this->object->getCreateErrors([
            'app_name' => '',
            'app_permissions' => '',
            'app_domain' => '',
            'profile_id' => 'foobar'
        ]);

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
        $actual = $this->object->getUpdateErrors([]);

        $this->assertEquals('Invalid ID', $actual['app_id']);

        $actual = $this->object->getUpdateErrors([
            'app_id' => 'foobar',
            'app_name' => '',
            'app_permissions' => '',
            'app_domain' => ''
        ]);

        $this->assertEquals('Invalid ID', $actual['app_id']);
        $this->assertEquals('Cannot be empty', $actual['app_name']);
        $this->assertEquals('Cannot be empty', $actual['app_domain']);
        $this->assertEquals('Cannot be empty', $actual['app_permissions']);
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

        $actual = $this->object->cacheCreateDetail('87d02468a934cb717cc15fe48a244f43', 1);
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

        $actual = $this->object->cacheCreateSearch(['q' => 'Cradle']);
        $this->assertEquals(1, $actual);

        $actual = $this->object->cacheCreateSearch(['filter' => ['profile_id' => 1]]);
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

        $this->assertEquals('Cradle App 1', $actual['app_name']);
        $this->assertEquals('John Doe', $actual['profile_name']);

        $actual = $this->object->cacheDetail('87d02468a934cb717cc15fe48a244f43');
        $this->assertEquals('Cradle App 1', $actual['app_name']);
        $this->assertEquals('John Doe', $actual['profile_name']);

        $actual = $this->object->cacheDetail(9999);
        $this->assertFalse($actual);

        $actual = $this->object->cacheDetail('foobar');
        $this->assertFalse($actual);
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

        $actual = $this->object->cacheDetailExists(9999);
        $this->assertFalse($actual);
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
        $this->assertEquals(1, $actual['rows'][0]['app_id']);

        //keyword
        $actual = $this->object->cacheSearch(['q' => 'Cradle']);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);

        //filter
        $actual = $this->object->cacheSearch(['filter' => ['profile_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
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

        $actual = $this->object->cacheSearchExists(['q' => 'foobar']);
        $this->assertFalse($actual);
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

        $actual = $this->object->cacheRemoveSearch(['q' => 'Cradle']);
        $this->assertEquals(1, $actual);

        $actual = $this->object->cacheRemoveSearch(['filter' => ['profile_id' => 1]]);
        $this->assertEquals(1, $actual);
    }
}
