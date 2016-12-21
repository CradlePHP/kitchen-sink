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
 * Profile Job Test
 *
 * Columns
 * - profile_name         string    REQUIRED
 * - profile_slug         string
 * - profile_email        string
 * - profile_phone        string
 * - profile_detail       float
 * - profile_image        string
 * - profile_company      string
 * - profile_job          string
 * - profile_gender       string
 * - profile_birth        date
 * - profile_achievements JSON
 * - profile_website      string
 * - profile_facebook     string
 * - profile_linkedin     string
 * - profile_twitter      string
 * - profile_google       string
 * - profile_reference    string
 * - profile_cover        url
 * - profile_rating       small    0
 * - profile_experience   int      1
 * - profile_locale     string   REQUIRED
 * - profile_active       bool     1
 * - profile_type         string
 * - profile_flag         small    0
 * - profile_created      datetime generated
 * - profile_updated      datetime generated
 *
 * @vendor   Salaaap
 * @package  Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Cradle_App_Core_Job_Profile_Test extends \Codeception\Test\Unit
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
     * profile-create
     *
     * @covers Cradle\App\Core\Model\Profile::getCreateErrors
     * @covers Cradle\App\Core\Model\Profile::getOptionalErrors
     * @covers Cradle\App\Core\Model\Profile::databaseCreate
     * @covers Cradle\App\Core\Model\Profile::indexCreate
     * @covers Cradle\App\Core\Model\Profile::cacheCreateDetail
     */
    public function testProfileCreate()
    {
    }

    /**
     * profile-detail
     *
     * @covers Cradle\App\Core\Model\Profile::databaseDetail
     * @covers Cradle\App\Core\Model\Profile::indexDetail
     * @covers Cradle\App\Core\Model\Profile::cacheDetail
     */
    public function testProfileDetail()
    {
    }

    /**
     * profile-image-base64-upload
     */
    public function testProfileImageBase64Upload()
    {
    }

    /**
     * profile-image-base64-cdn
     */
    public function testProfileImageBase64Cdn()
    {
    }

    /**
     * profile-image-client-cdn
     */
    public function testProfileImageClientCdn()
    {
    }

    /**
     * profile-remove
     *
     * @covers Cradle\App\Core\Model\Profile::databaseDetail
     * @covers Cradle\App\Core\Model\Profile::indexDetail
     * @covers Cradle\App\Core\Model\Profile::cacheDetail
     * @covers Cradle\App\Core\Model\Profile::databaseUpdate
     * @covers Cradle\App\Core\Model\Profile::indexUpdate
     * @covers Cradle\App\Core\Model\Profile::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Profile::cacheRemoveSearch
     */
    public function testProfileRemove()
    {
    }

    /**
     * profile-restore
     *
     * @covers Cradle\App\Core\Model\Profile::databaseDetail
     * @covers Cradle\App\Core\Model\Profile::indexDetail
     * @covers Cradle\App\Core\Model\Profile::cacheDetail
     * @covers Cradle\App\Core\Model\Profile::databaseUpdate
     * @covers Cradle\App\Core\Model\Profile::indexUpdate
     * @covers Cradle\App\Core\Model\Profile::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Profile::cacheRemoveSearch
     */
    public function testProfileRestore()
    {
    }

    /**
     * profile-search
     *
     * @covers Cradle\App\Core\Model\Profile::databaseSearch
     * @covers Cradle\App\Core\Model\Profile::indexSearch
     * @covers Cradle\App\Core\Model\Profile::cacheSearch
     */
    public function testProfileSearch()
    {
    }

    /**
     * profile-update
     *
     * @covers Cradle\App\Core\Model\Profile::databaseDetail
     * @covers Cradle\App\Core\Model\Profile::indexDetail
     * @covers Cradle\App\Core\Model\Profile::cacheDetail
     * @covers Cradle\App\Core\Model\Profile::databaseUpdate
     * @covers Cradle\App\Core\Model\Profile::indexUpdate
     * @covers Cradle\App\Core\Model\Profile::cacheRemoveDetail
     * @covers Cradle\App\Core\Model\Profile::cacheRemoveSearch
     */
    public function testProfileUpdate()
    {
    }
}
