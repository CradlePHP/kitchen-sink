<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license johnrmation can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\{{capital name}}\Service;

/**
 * SQL service test
  * {{capital name}} Model Test
  *
  * Columns
  * - {{name}}_name         string    REQUIRED
  * - {{name}}_slug         string
  * - {{name}}_email        string
  * - {{name}}_phone        string
  * - {{name}}_detail       float
  * - {{name}}_image        string
  * - {{name}}_company      string
  * - {{name}}_job          string
  * - {{name}}_gender       string
  * - {{name}}_birth        date
  * - {{name}}_website      string
  * - {{name}}_facebook     string
  * - {{name}}_linkedin     string
  * - {{name}}_twitter      string
  * - {{name}}_google       string
  * - {{active}}       bool     1
  * - {{name}}_type         string
  * - {{name}}_flag         small    0
  * - {{name}}_created      datetime generated
  * - {{name}}_updated      datetime generated
 *
 * @vendor   Acme
 * @package  {{capital name}}
 * @author   Christian Blanquera <cblanquera@openovate.com>
 */
class Cradle_Module_{{capital name}}_Service_SqlServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SqlService $object
     */
    protected $object;

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::__construct
     */
    protected function setUp()
    {
        $this->object = Service::get('sql');
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::create
     */
    public function testCreate()
    {
        $actual = $this->object->create(array(
            '{{name}}_email'     => 'model_{{name}}_1@email.com',
            '{{name}}_name'      => 'Model {{capital name}} 1',
            '{{name}}_locale'  => 'philippines'
        ));

        $this->assertEquals(2, $actual['{{primary}}']);
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::get
     */
    public function testGet()
    {
        $actual = $this->object->get(1);

        $this->assertEquals(1, $actual['{{primary}}']);
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::search
     */
    public function testSearch()
    {
        $actual = $this->object->search();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(1, $actual['rows'][0]['{{primary}}']);
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::update
     */
    public function testUpdate()
    {
        $actual = $this->object->update(array(
            '{{primary}}' => 2,
            '{{name}}_name' => 'Model {{capital name}} 3'
        ));

        $this->assertTrue(is_numeric($actual['{{primary}}']));
        $this->assertEquals(2, $actual['{{primary}}']);
        $this->assertEquals('Model {{capital name}} 3', $actual['{{name}}_name']);
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('john@doe.com');

        $this->assertTrue(!empty($actual));
        $this->assertEquals('john@doe.com', $actual['{{name}}_email']);
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Service\SqlService::remove
     */
    public function testRemove()
    {
        $actual = $this->object->remove(2);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(2, $actual['{{primary}}']);
    }
}
