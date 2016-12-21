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
 * Auth Job Test
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
class Cradle_App_Core_Job_Auth_Test extends \Codeception\Test\Unit
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
     * auth-create
     *
     * @covers Cradle\App\Core\Model\Auth::getCreateErrors
     * @covers Cradle\App\Core\Model\Auth::getOptionalErrors
     * @covers Cradle\App\Core\Model\Auth::databaseCreate
     * @covers Cradle\App\Core\Model\Auth::indexCreate
     * @covers Cradle\App\Core\Model\Auth::cacheCreateDetail
     * @covers Cradle\App\Core\Model\Profile::getCreateErrors
     * @covers Cradle\App\Core\Model\Profile::getOptionalErrors
     * @covers Cradle\App\Core\Model\Profile::databaseCreate
     * @covers Cradle\App\Core\Model\Profile::indexCreate
     * @covers Cradle\App\Core\Model\Profile::cacheCreateDetail
     */
    public function testAuthCreate()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('auth-create', $this->request, $this->response);

        $this->assertEquals('Cannot be empty', $this->response->getValidation('auth_slug'));
        $this->assertEquals('Cannot be empty', $this->response->getValidation('auth_permissions'));
        $this->assertEquals('Cannot be empty', $this->response->getValidation('auth_password'));
        $this->assertEquals('Cannot be empty', $this->response->getValidation('confirm'));

        $this->setUp();

        //success
        $this->request->setStage([
            'profile_name' => 'Job Auth 1',
            'profile_email' => 'job_auth_1@email.com',
            'profile_locale' => 'philippines',
            'auth_password' => 'foobar',
            'confirm' => 'foobar',
            'auth_permissions' => [
                'public_profile',
                'personal_profile',
                'personal_profile'
            ],
        ]);

        cradle()->trigger('auth-create', $this->request, $this->response);

        $this->assertEquals('job_auth_1@email.com', $this->response->getResults('profile_email'));
        $this->assertEquals('job_auth_1@email.com', $this->response->getResults('auth_slug'));
        $this->assertEquals(3, $this->response->getResults('auth_id'));
        $this->assertEquals(3, $this->response->getResults('profile_id'));
    }

    /**
     * auth-detail
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     */
    public function testAuthDetail()
    {
        $this->request->setStage('auth_id', 1);

        cradle()->trigger('auth-detail', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('auth_id'));
        $this->assertEquals('john@doe.com', $this->response->getResults('auth_slug'));

        $this->request->setStage('auth_slug', 'john@doe.com');

        cradle()->trigger('app-detail', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('auth_id'));
        $this->assertEquals('john@doe.com', $this->response->getResults('auth_slug'));
    }

    /**
     * auth-forgot
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     */
    public function testAuthForgot()
    {
    }

    /**
     * auth-forgot-mail
     */
    public function testAuthForgotMail()
    {
    }

    /**
     * auth-login
     *
     * @covers Cradle\App\Core\Model\Auth::getLoginErrors
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     */
    public function testAuthLogin()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('auth-login', $this->request, $this->response);

        $this->assertEquals('Cannot be empty', $this->response->getValidation('auth_slug'));
        $this->assertEquals('Cannot be empty', $this->response->getValidation('auth_password'));

        //success
        $this->request->setStage([
            'auth_slug' => 'job_auth_1@email.com',
            'auth_password' => 'foobar'
        ]);

        cradle()->trigger('auth-login', $this->request, $this->response);
        $this->assertEquals('job_auth_1@email.com', $this->response->getResults('auth_slug'));
        $this->assertEquals(3, $this->response->getResults('auth_id'));
        $this->assertEquals(3, $this->response->getResults('profile_id'));
    }

    /**
     * auth-recover
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveSearch
     */
    public function testAuthRecover()
    {
    }

    /**
     * auth-refresh
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveSearch
     */
    public function testAuthRefresh()
    {
        $this->request->setStage('auth_id', 3);

        cradle()->trigger('auth-detail', $this->request, $this->response);

        $token = $this->response->getResults('auth_token');
        $secret = $this->response->getResults('auth_secret');

        cradle()->trigger('auth-refresh', $this->request, $this->response);

        $this->assertTrue($token !== $this->response->getResults('auth_token'));
        $this->assertTrue($secret !== $this->response->getResults('auth_secret'));
    }

    /**
     * auth-remove
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveSearch
     */
    public function testAuthRemove()
    {
        $this->request->setStage('auth_id', 3);

        cradle()->trigger('auth-remove', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('auth_id'));
        $this->assertEquals(0, $this->response->getResults('auth_active'));
    }

    /**
     * auth-restore
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveSearch
     */
    public function testAuthRestore()
    {
        $this->request->setStage('auth_id', 3);

        cradle()->trigger('auth-restore', $this->request, $this->response);
        $this->assertEquals(3, $this->response->getResults('auth_id'));
        $this->assertEquals(1, $this->response->getResults('auth_active'));
    }

    /**
     * auth-search
     *
     * @covers Cradle\App\Core\Model\Auth::databaseSearch
     * @covers Cradle\App\Core\Model\Auth::indexSearch
     * @covers Cradle\App\Core\Model\Auth::cacheSearch
     */
    public function testAuthSearch()
    {
        cradle()->trigger('auth-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'auth_id'));

        //keyword
        $this->request->setStage('q', 'john');
        cradle()->trigger('auth-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'auth_id'));

        //filter
        $this->request->setStage('filter', 'auth_id', 1);
        cradle()->trigger('auth-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getResults('rows', 0, 'auth_id'));
    }

    /**
     * auth-update
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     * @covers Cradle\App\Core\Model\Auth::databaseUpdate
     * @covers Cradle\App\Core\Model\Auth::indexUpdate
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Auth::cacheRemoveSearch
     */
    public function testAuthUpdate()
    {
        //fail
        $this->request->setStage([]);
        cradle()->trigger('auth-update', $this->request, $this->response);
        $this->assertEquals('Invalid ID', $this->response->getMessage());

        $this->setUp();

        //success
        $this->request->setStage([
            'auth_id' => 3,
            'auth_slug' => 'job_auth_2@email.com'
        ]);

        cradle()->trigger('auth-update', $this->request, $this->response);

        $this->assertEquals('job_auth_2@email.com', $this->response->getResults('auth_slug'));
        $this->assertEquals(3, $this->response->getResults('auth_id'));
    }

    /**
     * auth-verify
     *
     * @covers Cradle\App\Core\Model\Auth::databaseDetail
     * @covers Cradle\App\Core\Model\Auth::indexDetail
     * @covers Cradle\App\Core\Model\Auth::cacheDetail
     */
    public function testAuthVerify()
    {
    }

    /**
     * auth-verify-mail
     */
    public function testAuthVerifyMail()
    {
    }
}
