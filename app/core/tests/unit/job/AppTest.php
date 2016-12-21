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
 * App Job Test
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
 * @vendor   Salaaap
 * @package  Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Cradle_App_Core_Job_App_Test extends \Codeception\Test\Unit
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
     * @covers Cradle\App\Core\Model\App::getCreateErrors
     * @covers Cradle\App\Core\Model\App::getOptionalErrors
     * @covers Cradle\App\Core\Model\App::databaseCreate
     * @covers Cradle\App\Core\Model\App::indexCreate
     * @covers Cradle\App\Core\Model\App::cacheCreateDetail
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
     * @covers Cradle\App\Core\Model\App::databaseDetail
     * @covers Cradle\App\Core\Model\App::indexDetail
     * @covers Cradle\App\Core\Model\App::cacheDetail
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
     * @covers Cradle\App\Core\Model\App::databaseDetail
     * @covers Cradle\App\Core\Model\App::indexDetail
     * @covers Cradle\App\Core\Model\App::cacheDetail
     * @covers Cradle\App\Core\Model\App::databaseUpdate
     * @covers Cradle\App\Core\Model\App::indexUpdate
     * @covers Cradle\App\Core\Model\App::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\App::cacheRemoveSearch
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
     * @covers Cradle\App\Core\Model\App::databaseDetail
     * @covers Cradle\App\Core\Model\App::indexDetail
     * @covers Cradle\App\Core\Model\App::cacheDetail
     * @covers Cradle\App\Core\Model\App::databaseUpdate
     * @covers Cradle\App\Core\Model\App::indexUpdate
     * @covers Cradle\App\Core\Model\App::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\App::cacheRemoveSearch
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
     * @covers Cradle\App\Core\Model\App::databaseDetail
     * @covers Cradle\App\Core\Model\App::indexDetail
     * @covers Cradle\App\Core\Model\App::cacheDetail
     * @covers Cradle\App\Core\Model\App::databaseUpdate
     * @covers Cradle\App\Core\Model\App::indexUpdate
     * @covers Cradle\App\Core\Model\App::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\App::cacheRemoveSearch
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
     * @covers Cradle\App\Core\Model\App::databaseSearch
     * @covers Cradle\App\Core\Model\App::indexSearch
     * @covers Cradle\App\Core\Model\App::cacheSearch
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
     * @covers Cradle\App\Core\Model\App::databaseDetail
     * @covers Cradle\App\Core\Model\App::indexDetail
     * @covers Cradle\App\Core\Model\App::cacheDetail
     * @covers Cradle\App\Core\Model\App::databaseUpdate
     * @covers Cradle\App\Core\Model\App::indexUpdate
     * @covers Cradle\App\Core\Model\App::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\App::cacheRemoveSearch
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
