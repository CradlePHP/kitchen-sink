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
 * - profile_website      string
 * - profile_facebook     string
 * - profile_linkedin     string
 * - profile_twitter      string
 * - profile_google       string
 * - profile_active       bool     1
 * - profile_type         string
 * - profile_flag         small    0
 * - profile_created      datetime generated
 * - profile_updated      datetime generated
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
     * profile-add-achievement
     *
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Profile\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function testProfileAchievement()
    {
    }

    /**
     * profile-add-experience
     *
     * @covers Cradle\Module\Profile\Service\SqlService::addExperience
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function testProfileAddExperience()
    {
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
    }

    /**
     * profile-update-rating
     *
     * @covers Cradle\Module\Market\Review\Service\SqlService::search
     * @covers Cradle\Module\Profile\Service\ElasticService::search
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     * @covers Cradle\Module\Profile\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\Profile\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function testProfileUpdateRating()
    {
    }
}
