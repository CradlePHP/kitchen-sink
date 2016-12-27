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
 * @package  {{capital name}}
 * @author   Christian Blanquera <cblanquera@openovate.com>
 */
class Cradle_Module_{{capital name}}_EventsTest extends PHPUnit_Framework_TestCase
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
     * {{name}}-add-achievement
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function test{{capital name}}Achievement()
    {
    }

    /**
     * {{name}}-add-experience
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::addExperience
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function test{{capital name}}AddExperience()
    {
    }

    /**
     * {{name}}-create
     *
     * @covers Cradle\Module\{{capital name}}\Validator::getCreateErrors
     * @covers Cradle\Module\{{capital name}}\Validator::getOptionalErrors
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::create
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::create
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::createDetail
     */
    public function test{{capital name}}Create()
    {
    }

    /**
     * {{name}}-detail
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     */
    public function test{{capital name}}Detail()
    {
    }

    /**
     * {{name}}-image-base64-upload
     */
    public function test{{capital name}}ImageBase64Upload()
    {
    }

    /**
     * {{name}}-image-base64-cdn
     */
    public function test{{capital name}}ImageBase64Cdn()
    {
    }

    /**
     * {{name}}-image-client-cdn
     */
    public function test{{capital name}}ImageClientCdn()
    {
    }

    /**
     * {{name}}-remove
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function test{{capital name}}Remove()
    {
    }

    /**
     * {{name}}-restore
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function test{{capital name}}Restore()
    {
    }

    /**
     * {{name}}-search
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::search
     * @covers Cradle\Module\{{capital name}}\Service\ElasticService::search
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     */
    public function test{{capital name}}Search()
    {
    }

    /**
     * {{name}}-update
     *
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function test{{capital name}}Update()
    {
    }

    /**
     * {{name}}-update-rating
     *
     * @covers Cradle\Module\Market\Review\Service\SqlService::search
     * @covers Cradle\Module\{{capital name}}\Service\ElasticService::search
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getSearch
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::get
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::getDetail
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::update
     * @covers Cradle\Module\Utility\Service\AbstractElasticService::update
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeDetail
     * @covers Cradle\Module\Utility\Service\AbstractRedisService::removeSearch
     */
    public function test{{capital name}}UpdateRating()
    {
    }
}
