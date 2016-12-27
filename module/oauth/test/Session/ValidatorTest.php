<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Session\Validator;

/**
 * Validator layer test
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_Session_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\Oauth\Session\Validator::getAccessErrors
     */
    public function testGetAccessErrors()
    {
        $actual = Validator::getAccessErrors(array());

        $this->assertEquals('Cannot be empty', $actual['code']);
        $this->assertEquals('Cannot be empty', $actual['client_id']);
        $this->assertEquals('Cannot be empty', $actual['client_secret']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Validator::getCreateErrors
     */
    public function testGetCreateErrors()
    {
        $actual = Validator::getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['session_permissions']);
        $this->assertEquals('Invalid ID', $actual['auth_id']);
        $this->assertEquals('Invalid ID', $actual['app_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Session\Validator::getUpdateErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = Validator::getUpdateErrors(array());

        $this->assertEquals('Invalid ID', $actual['session_id']);
    }
}
