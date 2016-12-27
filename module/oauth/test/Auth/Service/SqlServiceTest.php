<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Auth\Service;

/**
 * SQL service test
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
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_Auth_Service_SqlServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SqlService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('sql');
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::create
     */
    public function testCreate()
    {
        // create auth
        $auth = $this->object->create([
            'auth_slug' => 'model_auth_1@email.com',
            'auth_password' => 'foobar',
            'auth_permissions' => '["public_profile", "public_product", "personal_profile", "personal_product", "personal_comment", "personal_review", "user_profile", "user_product", "user_comment", "user_review"]'
        ]);

        $this->assertEquals(2, $auth['auth_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::get
     */
    public function testGet()
    {
        $actual = $this->object->get(1);
        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->get('john@doe.com');
        $this->assertEquals(1, $actual['auth_id']);
        $this->assertEquals('john@doe.com', $actual['auth_slug']);

        $actual = $this->object->get(99999);
        $this->assertNull($actual);

        $actual = $this->object->get('foobar');
        $this->assertNull($actual);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::search
     */
    public function testSearch()
    {
        $actual = $this->object->search();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //keyword
        $actual = $this->object->search(['q' => 'john']);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);

        //filter
        $actual = $this->object->search(['filter' => ['auth_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['auth_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::update
     */
    public function testUpdate()
    {
        $actual = $this->object->update(array(
            'auth_id' => 2,
            'auth_slug' => 'model_auth_2@email.com'
        ));

        $this->assertEquals(2, $actual['auth_id']);
        $this->assertEquals('model_auth_2@email.com', $actual['auth_slug']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('model_auth_2@email.com');

        // it returns a boolean so we're expecting it to be true because
        // the slug provided is saved in the database
        $this->assertTrue($actual);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::remove
     */
    public function testRemove()
    {
        $actual = $this->object->remove(2);

        $this->assertEquals(2, $actual['auth_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::linkProfile
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
     * @covers Cradle\Module\Oauth\Auth\Service\SqlService::unlinkProfile
     */
    public function testUnlinkProfile()
    {
        $actual = $this->object->unlinkProfile(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['auth_id']);
        $this->assertEquals(999, $actual['profile_id']);
    }
}
