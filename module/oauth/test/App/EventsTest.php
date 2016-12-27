<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * Event test
 *
 * Columns
 * - app_name        string   REQUIRED
 * - app_domain      string   REQUIRED
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
 * Relations
 * - profile*
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_App_EventsTest extends PHPUnit_Framework_TestCase
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
     * app-create
     *
     * @covers Cradle\Module\Oauth\App\Validator::getCreateErrors
     * @covers Cradle\Module\Oauth\App\Validator::getOptionalErrors
     * @covers Cradle\Module\Oauth\App\Service\SqlService::create
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::create
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function testAppCreate()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('app-create', $this->request, $this->response);

        $this->assertEquals('Cannot be empty', $this->response->getValidation('app_name'));
        $this->assertEquals('Cannot be empty', $this->response->getValidation('app_permissions'));
        $this->assertEquals('Cannot be empty', $this->response->getValidation('app_domain'));
        $this->assertEquals('Invalid ID', $this->response->getValidation('profile_id'));

        $this->setUp();

        //success
        $this->request->setStage([
            'app_name' => 'Job App 1',
            'app_permissions' => [
                'public_profile',
                'personal_profile',
                'personal_profile'
            ],
            'app_domain' => 'foobar',
            'profile_id' => 1
        ]);

        cradle()->trigger('app-create', $this->request, $this->response);
        $this->assertEquals('Job App 1', $this->response->getResults('app_name'));
        $this->assertEquals(3, $this->response->getResults('app_id'));
    }

    /**
     * app-detail
     *
     * @covers Cradle\Module\Oauth\App\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     */
    public function testAppDetail()
    {
        $this->request->setStage('app_id', 1);

        cradle()->trigger('app-detail', $this->request, $this->response);
        $this->assertEquals('Cradle App 1', $this->response->getResults('app_name'));

        $this->request->setStage('app_token', '87d02468a934cb717cc15fe48a244f43');

        cradle()->trigger('app-detail', $this->request, $this->response);
        $this->assertEquals('Cradle App 1', $this->response->getResults('app_name'));
    }

    /**
     * app-refresh
     *
     * @covers Cradle\Module\Oauth\App\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Oauth\App\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testAppRefresh()
    {
        $this->request->setStage('app_id', 3);

        cradle()->trigger('app-detail', $this->request, $this->response);

        $token = $this->response->getResults('app_token');
        $secret = $this->response->getResults('app_secret');

        cradle()->trigger('app-refresh', $this->request, $this->response);

        $this->assertTrue($token !== $this->response->getResults('app_token'));
        $this->assertTrue($secret !== $this->response->getResults('app_secret'));
    }

    /**
     * app-remove
     *
     * @covers Cradle\Module\Oauth\App\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Oauth\App\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testAppRemove()
    {
        $this->request->setStage('app_id', 3);

        cradle()->trigger('app-remove', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('app_id'));
        $this->assertEquals(0, $this->response->getResults('app_active'));
    }

    /**
     * app-restore
     *
     * @covers Cradle\Module\Oauth\App\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Oauth\App\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testAppRestore()
    {
        $this->request->setStage('app_id', 3);

        cradle()->trigger('app-restore', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('app_id'));
        $this->assertEquals(1, $this->response->getResults('app_active'));
    }

    /**
     * app-search
     *
     * @covers Cradle\Module\Oauth\App\Service\SqlService::search
     * @covers Cradle\Module\Oauth\App\Service\ElasticService::search
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     */
    public function testAppSearch()
    {
        cradle()->trigger('app-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'app_id'));

        //keyword
        $this->request->setStage('q', 'Cradle');
        cradle()->trigger('app-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'app_id'));

        //filter
        $this->request->setStage('filter', 'profile_id', 1);
        cradle()->trigger('app-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'app_id'));
    }

    /**
     * app-update
     *
     * @covers Cradle\Module\Oauth\App\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Oauth\App\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testAppUpdate()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('app-update', $this->request, $this->response);
        $this->assertEquals('Invalid ID', $this->response->getMessage());

        $this->setUp();

        //success
        $this->request->setStage([
            'app_id' => 3,
            'app_name' => 'Job App 2'
        ]);

        cradle()->trigger('app-update', $this->request, $this->response);
        $this->assertEquals('Job App 2', $this->response->getResults('app_name'));
        $this->assertEquals(3, $this->response->getResults('app_id'));
    }
}
