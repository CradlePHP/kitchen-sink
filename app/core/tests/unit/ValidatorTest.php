<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\Validator;

class Cradle_App_Core_Validator_Test extends \Codeception\Test\Unit
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\App\Core\Validator::startsWithLetter
     */
    public function testStartsWithLetter()
    {
        $actual = Validator::startsWithLetter('foobar');
        $this->assertTrue(!!$actual);

        $actual = Validator::startsWithLetter('123foobar');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::alphaNumeric
     */
    public function testAlphaNumeric()
    {
        $actual = Validator::alphaNumeric('foo123bar');
        $this->assertTrue(!!$actual);

        $actual = Validator::alphaNumeric('!@#$ABC2');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::alphaNumericUnderScore
     */
    public function testAlphaNumericScore()
    {
        $actual = Validator::alphaNumericUnderScore('foo_123_bar');
        $this->assertTrue(!!$actual);

        $actual = Validator::alphaNumericUnderScore('!@#$ABC2');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::alphaNumericHyphen
     */
    public function testAlphaNumericHyphen()
    {
        $actual = Validator::alphaNumericHyphen('foo-123-bar');
        $this->assertTrue(!!$actual);

        $actual = Validator::alphaNumericHyphen('!@#$ABC2');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::alphaNumericLine
     */
    public function testAlphaNumericLine()
    {
        $actual = Validator::alphaNumericLine('foo_123-bar');
        $this->assertTrue(!!$actual);

        $actual = Validator::alphaNumericLine('!@#$ABC2');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isBool
     */
    public function testIsBool()
    {
        $actual = Validator::isBool('1');
        $this->assertTrue(!!$actual);

        $actual = Validator::isBool('9');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isCreditCard
     */
    public function testIsCreditCard()
    {
        $actual = Validator::isCreditCard('4111111111111111');
        $this->assertTrue(!!$actual);

        $actual = Validator::isCreditCard('567183904832764389');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isDate
     */
    public function testIsDate()
    {
        $actual = Validator::isDate('2016-01-01');
        $this->assertTrue(!!$actual);

        $actual = Validator::isDate('Not Sure');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isEmail
     */
    public function testIsEmail()
    {
        $actual = Validator::isEmail('test+1@test.com');
        $this->assertTrue(!!$actual);

        $actual = Validator::isEmail('test@test@.foobar');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isFloat
     */
    public function testIsFloat()
    {
        $actual = Validator::isFloat('1.34');
        $this->assertTrue(!!$actual);

        $actual = Validator::isFloat('123.fo.obar');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isInteger
     */
    public function testIsInteger()
    {
        $actual = Validator::isInteger('134');
        $this->assertTrue(!!$actual);

        $actual = Validator::isInteger('1.34');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isJson
     */
    public function testIsJson()
    {
        $actual = Validator::isJson('{"foo":"bar"}');
        $this->assertTrue(!!$actual);

        $actual = Validator::isJson('{{}}[]');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isSmall
     */
    public function testIsSmall()
    {
        $actual = Validator::isSmall('5');
        $this->assertTrue(!!$actual);

        $actual = Validator::isSmall('50');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isTime
     */
    public function testIsTime()
    {
        $actual = Validator::isTime('12:00:00');
        $this->assertTrue(!!$actual);

        $actual = Validator::isTime('12pm');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isUrl
     */
    public function testIsUrl()
    {
        $actual = Validator::isUrl('http://google.com/');
        $this->assertTrue(!!$actual);

        $actual = Validator::isUrl('foobar');
        $this->assertFalse(!!$actual);
    }

    /**
     * @covers Cradle\App\Core\Validator::isHex
     */
    public function testIsHex()
    {
        $actual = Validator::isHex('1AF2BE');
        $this->assertTrue(!!$actual);

        $actual = Validator::isHex('12345X');
        $this->assertFalse(!!$actual);
    }
}
