<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * Session Job Test
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
class Cradle_App_Core_Job_Session_Test extends \Codeception\Test\Unit
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
     * @covers Cradle\App\Core\Model\Session::getCreateErrors
     * @covers Cradle\App\Core\Model\Session::getOptionalErrors
     * @covers Cradle\App\Core\Model\Session::databaseCreate
     * @covers Cradle\App\Core\Model\Session::indexCreate
     * @covers Cradle\App\Core\Model\Session::cacheCreateDetail
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
     * @covers Cradle\App\Core\Model\Session::databaseDetail
     * @covers Cradle\App\Core\Model\Session::indexDetail
     * @covers Cradle\App\Core\Model\Session::cacheDetail
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
     * @covers Cradle\App\Core\Model\Session::databaseDetail
     * @covers Cradle\App\Core\Model\Session::indexDetail
     * @covers Cradle\App\Core\Model\Session::cacheDetail
     * @covers Cradle\App\Core\Model\Session::databaseUpdate
     * @covers Cradle\App\Core\Model\Session::indexUpdate
     * @covers Cradle\App\Core\Model\Session::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Session::cacheRemoveSearch
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
     * @covers Cradle\App\Core\Model\Session::databaseSearch
     * @covers Cradle\App\Core\Model\Session::indexSearch
     * @covers Cradle\App\Core\Model\Session::cacheSearch
     */
    public function testSessionSearch()
    {
        cradle()->trigger('session-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'session_id'));
    }

    /**
     * session-update
     *
     * @covers Cradle\App\Core\Model\Session::databaseDetail
     * @covers Cradle\App\Core\Model\Session::indexDetail
     * @covers Cradle\App\Core\Model\Session::cacheDetail
     * @covers Cradle\App\Core\Model\Session::databaseUpdate
     * @covers Cradle\App\Core\Model\Session::indexUpdate
     * @covers Cradle\App\Core\Model\Session::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Session::cacheRemoveSearch
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
     * @covers Cradle\App\Core\Model\Session::databaseRemove
     * @covers Cradle\App\Core\Model\Session::indexRemove
     * @covers Cradle\App\Core\Model\Session::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Session::cacheRemoveSearch
     */
    public function testSessionRemove()
    {
        $this->request->setStage('session_id', 3);

        cradle()->trigger('session-remove', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('session_id'));
    }
}
