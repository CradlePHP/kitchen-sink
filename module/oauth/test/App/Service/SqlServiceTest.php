<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\App\Service;

/**
 * SQL service test
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
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_App_Service_SqlServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SqlService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('sql');
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::create
     */
    public function testCreate()
    {
        //success
        $app = $this->object->create([
            'app_name' => 'Model App 1',
            'app_permissions' => '["public_profile", "public_product", "personal_profile", "personal_product", "personal_comment", "personal_review", "user_profile", "user_product", "user_comment", "user_review"]'
        ]);

        $this->assertEquals(2, $app['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::get
     */
    public function testGet()
    {
        $actual = $this->object->get(1);
        $this->assertEquals('Cradle App 1', $actual['app_name']);
        $this->assertEquals('John Doe', $actual['profile_name']);

        $actual = $this->object->get('87d02468a934cb717cc15fe48a244f43');
        $this->assertEquals('Cradle App 1', $actual['app_name']);
        $this->assertEquals('John Doe', $actual['profile_name']);

        $actual = $this->object->get(9999);
        $this->assertNull($actual);

        $actual = $this->object->get('foobar');
        $this->assertNull($actual);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::search
     */
    public function testSearch()
    {
        $actual = $this->object->search();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);

        //keyword
        $actual = $this->object->search(['q' => 'Cradle']);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);

        //filter
        $actual = $this->object->search(['filter' => ['profile_id' => 1]]);
        $this->assertEquals(1, $actual['rows'][0]['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::update
     */
    public function testUpdate()
    {
        $actual = $this->object->update([
            'app_id' => 2,
            'app_name' => 'Model App 2'
        ]);

        $this->assertEquals(2, $actual['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::remove
     */
    public function testRemove()
    {
        $actual = $this->object->remove(2);
        $this->assertEquals(2, $actual['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Service\SqlService::linkProfile
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
     * @covers Cradle\Module\Oauth\App\Service\SqlService::unlinkProfile
     */
    public function testUnlinkProfile()
    {
        $actual = $this->object->unlinkProfile(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['app_id']);
        $this->assertEquals(999, $actual['profile_id']);
    }
}
