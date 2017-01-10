<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * Event test
 *
 * @vendor   Acme
 * @package  Profile
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Profile_EventsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var Request $response
     */
    protected $response;

    /**
     * @var int $id
     */
    protected static $id;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->request = new Request();
        $this->response = new Response();

        $this->request->load();
        $this->response->load();
    }

    /**
     * profile-create
     *
     * @covers Cradle\Module\Profile\Validator::getCreateErrors
     * @covers Cradle\Module\Profile\Validator::getOptionalErrors
     * @covers Cradle\Module\Profile\Service\SqlService::create
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::create
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function testProfileCreate()
    {
        $this->request->setStage([
            'profile_name' => 'John Doe',
        ]);

        cradle()->trigger('profile-create', $this->request, $this->response);
        $this->assertEquals('John Doe', $this->response->getResults('profile_name'));
        self::$id = $this->response->getResults('profile_id');
        $this->assertTrue(is_numeric(self::$id));
    }

    /**
     * profile-detail
     *
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     */
    public function testProfileDetail()
    {
        $this->request->setStage('profile_id', 1);

        cradle()->trigger('profile-detail', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('profile_id'));
    }

    /**
     * profile-remove
     *
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Profile\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testProfileRemove()
    {
        $this->request->setStage('profile_id', self::$id);

        cradle()->trigger('profile-remove', $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('profile_id'));
    }

    /**
     * profile-restore
     *
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Profile\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testProfileRestore()
    {
        $this->request->setStage('profile_id', 581);

        cradle()->trigger('profile-restore', $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('profile_id'));
        $this->assertEquals(1, $this->response->getResults('profile_active'));
    }

    /**
     * profile-search
     *
     * @covers Cradle\Module\Profile\Service\SqlService::search
     * @covers Cradle\Module\Profile\Service\ElasticService::search
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     */
    public function testProfileSearch()
    {
        cradle()->trigger('profile-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'profile_id'));
    }

    /**
     * profile-update
     *
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Profile\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testProfileUpdate()
    {
        $this->request->setStage([
            'profile_id' => self::$id,
            'profile_name' => 'John Doe',
        ]);

        cradle()->trigger('profile-update', $this->request, $this->response);
        $this->assertEquals('John Doe', $this->response->getResults('profile_name'));
        $this->assertEquals(self::$id, $this->response->getResults('profile_id'));
    }
}
