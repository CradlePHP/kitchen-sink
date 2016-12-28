<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\{{capital name}}\Service;

/**
 * SQL service test
 * {{capital name}} Model Test
 *
 * @vendor   Acme
 * @package  {{capital name}}
 * @author   John Doe <john@acme.com>
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
        $actual = $this->object->create([
            {{~#each fields}}{{#each valid}}
            {{~#when this.0 '===' 'required'}}
            '{{../@key}}' => {{../sample}},
            {{~/when}}{{/each}}{{/each}}
        ]);

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
        $actual = $this->object->update([
            {{~#each fields}}{{#each valid}}
            {{~#when this.0 '===' 'required'}}
            '{{../@key}}' => {{../sample}},
            {{~/when}}{{/each}}{{/each}}
        ]);

        $this->assertEquals(2, $actual['{{primary}}']);
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
