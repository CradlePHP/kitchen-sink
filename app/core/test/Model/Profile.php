<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

class Cradle_App_Core_Model_Profile_Test extends PHPUnit_Framework_TestCase
{
    protected $object;

    /**
     * @covers Cradle\App\Core\Model\Profile::__construct
     */
    protected function setUp()
    {
        $service = new Cradle\App\Core\Service(cradle());
        $this->object = new Cradle\App\Core\Model\Profile($service);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseCreate
     */
    public function testDatabaseCreate()
    {
        $actual = $this->object->databaseCreate(array(
            'profile_email'     => 'foobar@email.com',
            'profile_name'      => 'Foo Bar',
            'profile_location'  => 'foobar'
        ));

        $this->assertTrue(is_numeric($actual['profile_id']));
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseCreateGuest
     */
    public function testDatabaseCreateGuest()
    {
        $actual = $this->object->databaseCreateGuest(
            'foobar@email.com',
            123456789,
            'Foo Bar Location'
        );

        $this->assertTrue(is_numeric($actual['profile_id']));
        $this->assertEquals('foobar@email.com', $actual['profile_email']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseDetail
     */
    public function testDatabaseDetail()
    {
        $actual = $this->object->databaseDetail(1);

        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseRemove
     */
    public function testDatabaseRemove()
    {
        $actual = $this->object->databaseRemove(1);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(1, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseSearch
     */
    public function testDatabaseSearch()
    {
        $actual = $this->object->databaseSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertTrue(is_numeric($actual['rows'][0]['profile_id']));
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::databaseUpdate
     */
    public function testDatabaseUpdate()
    {
        $actual = $this->object->databaseUpdate(array(
            'profile_id' => 1,
            'profile_name' => 'Foo Bar Name'
        ));

        $this->assertTrue(is_numeric($actual['profile_id']));
        $this->assertEquals(1, $actual['profile_id']);
        $this->assertEquals('Foo Bar Name', $actual['profile_name']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::exists
     */
    public function testExists()
    {
        $actual = $this->object->exists('foobar@email.com');

        $this->assertTrue(!empty($actual));
        $this->assertEquals('foobar@email.com', $actual['profile_email']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexCreate
     */
    public function testIndexCreate()
    {
        $actual = $this->object->indexCreate(2);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('profile', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexDetail
     */
    public function testIndexDetail()
    {
        $actual = $this->object->indexDetail(2);

        $this->assertEquals(2, $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexRemove
     */
    public function testIndexRemove()
    {
        $actual = $this->object->indexRemove(2);

        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('profile', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
        $this->assertEquals('deleted', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexSearch
     */
    public function testIndexSearch()
    {
        $actual = $this->object->indexSearch();

        $this->assertArrayHasKey('rows', $actual);
        $this->assertArrayHasKey('total', $actual);
        $this->assertEquals(2, $actual['rows'][0]['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::indexUpdate
     */
    public function testIndexUpdate()
    {
        $this->object->indexCreate(2);

        $actual = $this->object->indexUpdate(2);

        // now, test it
        $this->assertEquals('main', $actual['_index']);
        $this->assertEquals('profile', $actual['_type']);
        $this->assertEquals(2, $actual['_id']);
        $this->assertEquals('noop', $actual['result']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::getCreateErrors
     * @covers Cradle\App\Core\Model\Profile::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = $this->object->getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['profile_name']);
        $this->assertEquals('Cannot be empty', $actual['profile_location']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::getUpdateErrors
     * @covers Cradle\App\Core\Model\Profile::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = $this->object->getUpdateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['profile_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::linkComment
     */
    public function testLinkComment()
    {
        $actual = $this->object->linkComment(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['profile_id']);
        $this->assertEquals(999, $actual['comment_id']);
    }

    /**
     * @covers Cradle\App\Core\Model\Profile::unlinkComment
     */
    public function testUnlinkComment()
    {
        $actual = $this->object->unlinkComment(999, 999);

        $this->assertTrue(!empty($actual));
        $this->assertEquals(999, $actual['profile_id']);
        $this->assertEquals(999, $actual['comment_id']);
    }
}
