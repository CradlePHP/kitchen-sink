<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Session\Service;

/**
 * SQL service test
 *
 * Columns
 * - session_token       string   generated
 * - session_secret      string   generated
 * - session_permissions JSON     REQUIRED
 * - session_status      string   REQUIRED
 * - session_active      bool     1
 * - session_type        string
 * - session_flag        small    0
 * - session_created     datetime generated
 * - session_updated     datetime generated
 *
 * Relations
 * - auth*
 * - app*
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_Session_Service_SqlServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SqlService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('sql');
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::create
     */
    public function testCreate()
    {
        // just create something
        $actual = $this->object->create(array(
            'session_permissions' => '["public_profile", "public_product", "personal_profile", "personal_product", "personal_comment", "personal_review", "user_profile", "user_product", "user_comment", "user_review"]',
            'session_status' => 'REQUEST'
        ));

        // then link to app, auth, and profile
        $this->object->linkApp($actual['session_id'], 1);
        $this->object->linkAuth($actual['session_id'], 1);

        $this->assertEquals(2, $actual['session_id']);
        $this->assertEquals('REQUEST', $actual['session_status']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::get
     */
    public function testGet()
    {
        $actual = $this->object->get(1);

        $this->assertEquals(1, $actual['session_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::search
     */
    public function testSearch()
    {
        $actual = $this->object->search();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['session_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::update
     */
    public function testUpdate()
    {
        $actual = $this->object->update(array(
            'session_id' => 2,
            'session_status' => 'ACCESS'
        ));

        $this->assertEquals(2, $actual['session_id']);
        $this->assertEquals('ACCESS', $actual['session_status']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::remove
     */
    public function testRemove()
    {
        $actual = $this->object->remove(2);

        $this->assertEquals(2, $actual['session_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::linkApp
     */
    public function testLinkApp()
    {
        $actual = $this->object->linkApp(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::linkAuth
     */
    public function testLinkAuth()
    {
        $actual = $this->object->linkAuth(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['auth_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::unlinkApp
     */
    public function testUnlinkApp()
    {
        $actual = $this->object->unlinkApp(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::unlinkAuth
     */
    public function testUnlinkAuth()
    {
        $actual = $this->object->unlinkAuth(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['session_id']);
        $this->assertEquals(999, $actual['auth_id']);
    }
}
