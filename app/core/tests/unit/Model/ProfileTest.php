<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\Service;
use Cradle\App\Core\Model\Profile;

/**
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
 * @vendor   Salaaap
 * @package  Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Cradle_App_Core_Model_Profile_Test extends \Codeception\Test\Unit
{
    /**
     * @var Profile $object
     */
    protected $object;

    /**
     * @covers Cradle\App\Core\Model\Profile::__construct
     */
    protected function setUp()
    {
        $service = new Service(cradle());
        $this->object = new Profile($service);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseCreate
     */
    public function testDatabaseCreate()
    {
        $actual = $this->object->databaseCreate(array(
            'profile_email'     => 'model_profile_1@email.com',
            'profile_name'      => 'Model Profile 1'
        ));

        $this->assertEquals(2, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseDetail
     */
    public function testDatabaseDetail()
    {
        $actual = $this->object->databaseDetail(1);

        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseSearch
     */
    public function testDatabaseSearch()
    {
        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate(array(
            'profile_id' => 2,
            'profile_name' => 'Model Profile 2'
        ));

        $this->assertTrue(is_numeric($actual['profile_id']));
        $this->assertEquals(2, $actual['profile_id']);
        $this->assertEquals('Model Profile 2', $actual['profile_name']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('john@doe.com');

        $this->assertTrue(!empty($actual));
        $this->assertEquals('john@doe.com', $actual['profile_email']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(2);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(2, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('profile', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('profile', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexSearch
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
        $this->assertEquals(1, $actual['rows'][0]['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexUpdate
     */
    public function testIndexUpdate()
    {
        $this->object->indexCreate(1);

        $actual = $this->object->indexUpdate(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        // now, test it
        $this->assertEquals('profile', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::getCreateErrors
     * @covers Cradle\App\Core\Model\Profile::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['profile_name']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::getUpdateErrors
     * @covers Cradle\App\Core\Model\Profile::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = $this->object->getUpdateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['profile_id']);
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

        $this->assertEquals(1, $actual['profile_id']);
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
        $this->assertEquals(1, $actual['rows'][0]['profile_id']);
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
