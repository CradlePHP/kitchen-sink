<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\Service;
use Cradle\App\Core\Model\Auth;

/**
 * Auth Model Test
 *
 * Columns
 * - auth_slug            string   REQUIRED
 * - auth_password        string   REQUIRED
 * - auth_token           string   generated
 * - auth_secret          string   generated
 * - auth_permissions     JSON     REQUIRED
 * - auth_facebook_token  string
 * - auth_facebook_secret string
 * - auth_twitter_token   string
 * - auth_twitter_secret  string
 * - auth_google_token    string
 * - auth_google_secret   string
 * - auth_linkedin_token  string
 * - auth_linkedin_secret string
 * - auth_active          bool     1
 * - auth_type            string
 * - auth_flag            small    0
 * - auth_created         datetime generated
 * - auth_updated         datetime generated
 *
 * @vendor   Salaaap
 * @package  Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Cradle_App_Core_Model_Auth_Test extends \Codeception\Test\Unit
{
    /**
     * @var Auth $object
     */
    protected $object;

    /**
     * @covers Cradle\App\Core\Model\Auth::__construct
     */
    protected function setUp()
    {
        $service = new Service(cradle());
        $this->object = new Auth($service);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseCreate
     */
    public function testDatabaseCreate()
    {
        // create auth
        $auth = $this->object->databaseCreate([
            'auth_slug' => 'model_auth_1@email.com',
            'auth_password' => 'foobar',
            'auth_permissions' => '["public_profile", "personal_profile", "user_profile"]'
        ]);

        $this->assertEquals(2, $auth['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     */
    public function testDatabaseDetail()
    {
        $actual = $this->object->databaseDetail(1);
        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->databaseDetail('john@doe.com');
        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->databaseDetail(99999);
        $this->assertNull($actual);

        $actual = $this->object->databaseDetail('foobar');
        $this->assertNull($actual);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseSearch
     */
    public function testDatabaseSearch()
    {
        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //keyword
        $actual = $this->object->databaseSearch(['q' => 'info']);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //filter
        $actual = $this->object->databaseSearch(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate(array(
            'auth_id' => 2,
            'auth_slug' => 'model_auth_2@email.com'
        ));

        $this->assertEquals(2, $actual['auth_id']);
        $this->assertEquals('model_auth_2@email.com', $actual['auth_slug']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('model_auth_2@email.com');

        // it returns a boolean so we're expecting it to be true because
        // the slug provided is saved in the database
        $this->assertTrue($actual);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(2);

        $this->assertEquals(2, $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('auth', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals('auth', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(1);

        //if it's false, it's not enabled
        if($actual === false) {
            return;
        }

        $this->assertEquals(1, $actual['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexSearch
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
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
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
        $this->assertEquals('auth', $actual['_index']);
        $this->assertEquals('main', $actual['_type']);
        $this->assertEquals(1, $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getCreateErrors
     * @covers Cradle\App\Core\Model\Auth::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = $this->object->getCreateErrors([
            'auth_slug' => '',
            'auth_permissions' => '',
            'auth_password' => '',
            'confirm' => ''
        ]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = $this->object->getCreateErrors([
            'auth_slug' => 'john@doe.com',
            'auth_password' => '321',
            'confirm' => '123'
        ]);

        $this->assertEquals('User Exists', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Passwords do not match', $actual['confirm']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getForgotErrors
     */
    public function testGetForgotErrors()
    {
        $actual = $this->object->getForgotErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getLoginErrors
     */
    public function testGetLoginErrors()
    {
        $actual = $this->object->getLoginErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);

        $actual = $this->object->getLoginErrors([
            'auth_slug' => 'not a real user',
            'auth_password' => 'not a real password'
        ]);

        $this->assertEquals('User does not exist', $actual['auth_slug']);

        $actual = $this->object->getLoginErrors([
            'auth_slug' => 'john@doe.com',
            'auth_password' => 'not a real password'
        ]);

        $this->assertEquals('Password is incorrect', $actual['auth_password']);
    }

    /**
     * @covers Cradle\App\Core\Model\Auth::getRecoverErrors
     */
    public function testGetRecoverErrors()
    {
        $actual = $this->object->getRecoverErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = $this->object->getRecoverErrors([
            'auth_password' => '',
            'confirm' => ''
        ]);

        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = $this->object->getRecoverErrors([
            'auth_password' => '321',
            'confirm' => '123'
        ]);

        $this->assertEquals('Passwords do not match', $actual['confirm']);
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
     * @covers Cradle\App\Core\Model\Auth::getVerifyErrors
     */
    public function testGetVerifyErrors()
    {
        $actual = $this->object->getVerifyErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
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

        $actual = $this->object->cacheCreateDetail('john@doe.com', 1);
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

        $actual = $this->object->cacheCreateSearch(['q' => 'john']);
        $this->assertEquals(1, $actual);

        $actual = $this->object->cacheCreateSearch(['filter' => ['auth_id' => 1]]);
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

        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->cacheDetail('john@doe.com');
        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->cacheDetail(99999);
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
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //keyword
        $actual = $this->object->cacheSearch(['q' => 'john']);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //filter
        $actual = $this->object->cacheSearch(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);
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

        $actual = $this->object->cacheRemoveSearch(['q' => 'john']);
        $this->assertEquals(1, $actual);

        $actual = $this->object->cacheRemoveSearch(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual);
    }
}
