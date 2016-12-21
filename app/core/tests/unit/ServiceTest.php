<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

class Cradle_App_Core_Service_Test extends \Codeception\Test\Unit
{
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Cradle\App\Core\Service(cradle());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\App\Core\Service::cache
     */
    public function testCache()
    {
        $actual = $this->object->cache();

        //if it's false, it's not enabled
        if($actual === null) {
            return;
        }

        $this->assertInstanceOf('Predis\Client', $actual);
    }

    /**
     * @covers Cradle\App\Core\Service::database
     */
    public function testDatabase()
    {
        $actual = $this->object->database();
        $this->assertInstanceOf('Cradle\Sql\Mysql', $actual);
    }

    /**
     * @covers Cradle\App\Core\Service::index
     */
    public function testIndex()
    {
        $actual = $this->object->index();

        //if it's false, it's not enabled
        if($actual === null) {
            return;
        }

        $this->assertInstanceOf('Elasticsearch\Client', $actual);
    }

    /**
     * @covers Cradle\App\Core\Service::custom
     */
    public function testCustom()
    {
        $actual = $this->object->custom('sql-main');
        $this->assertInstanceOf('PDO', $actual);
    }
}
