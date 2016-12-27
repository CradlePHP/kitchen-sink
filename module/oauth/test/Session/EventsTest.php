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
class Cradle_Module_Oauth_Session_EventsTest extends PHPUnit_Framework_TestCase
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
     * session-access
     */
    public function testSessionAccess()
    {
    }

    /**
     * session-create
     *
     * @covers Cradle\Module\Oauth\Session\Validator::getCreateErrors
     * @covers Cradle\Module\Oauth\Session\Validator::getOptionalErrors
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::create
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::create
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function testSessionCreate()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('session-create', $this->request, $this->response);

        $this->assertEquals('Cannot be empty', $this->response->getValidation('session_permissions'));
        $this->assertEquals('Invalid ID', $this->response->getValidation('auth_id'));
        $this->assertEquals('Invalid ID', $this->response->getValidation('app_id'));

        $this->setUp();

        //success
        $this->request->setStage([
            'session_permissions' => [
                'public_profile',
                'personal_profile',
                'personal_profile'
            ],
            'auth_id' => 1,
            'app_id' => 1
        ]);

        cradle()->trigger('session-create', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('session_id'));
    }

    /**
     * session-detail
     *
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     */
    public function testSessionDetail()
    {
        $this->request->setStage('session_id', 3);

        cradle()->trigger('session-detail', $this->request, $this->response);
        $this->assertEquals('PENDING', $this->response->getResults('session_status'));
        $token = $this->response->getResults('session_token');

        $this->setup();

        $this->request->setStage('session_token', $token);

        cradle()->trigger('session-detail', $this->request, $this->response);
        $this->assertEquals('PENDING', $this->response->getResults('session_status'));
    }

    /**
     * session-login
     */
    public function testSessionLogin()
    {
    }

    /**
     * session-refresh
     *
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testSessionRefresh()
    {
        $this->request->setStage('session_id', 3);

        cradle()->trigger('session-detail', $this->request, $this->response);

        $token = $this->response->getResults('session_token');
        $secret = $this->response->getResults('session_secret');

        cradle()->trigger('session-refresh', $this->request, $this->response);

        $this->assertTrue($token !== $this->response->getResults('session_token'));
        $this->assertTrue($secret !== $this->response->getResults('session_secret'));
    }

    /**
     * session-search
     *
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::search
     * @covers Cradle\Module\Oauth\Session\Service\ElasticService::search
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     */
    public function testSessionSearch()
    {
        cradle()->trigger('session-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'session_id'));
    }

    /**
     * session-update
     *
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testSessionUpdate()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('session-update', $this->request, $this->response);
        $this->assertEquals('Invalid ID', $this->response->getMessage());

        $this->setUp();

        //success
        $this->request->setStage([
            'session_id' => 3,
            'session_status' => 'ACCESS',
        ]);

        cradle()->trigger('session-update', $this->request, $this->response);
        $this->assertEquals('ACCESS', $this->response->getResults('session_status'));
        $this->assertEquals(3, $this->response->getResults('session_id'));
    }

    /**
     * session-remove
     *
     * @covers Cradle\Module\Oauth\Session\Service\SqlService::remove
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::remove
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testSessionRemove()
    {
        $this->request->setStage('session_id', 3);

        cradle()->trigger('session-remove', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('session_id'));
    }
}
