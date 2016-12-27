<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Auth\Validator;

/**
 * Validator layer test
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_Auth_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\Oauth\Auth\Validator::getCreateErrors
     * @covers Cradle\Module\Oauth\Auth\Validator::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = Validator::getCreateErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = Validator::getCreateErrors([
            'auth_slug' => '',
            'auth_permissions' => '',
            'auth_password' => '',
            'confirm' => ''
        ]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = Validator::getCreateErrors([
            'auth_slug' => 'john@doe.com',
            'auth_password' => '321',
            'confirm' => '123'
        ]);

        $this->assertEquals('User Exists', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_permissions']);
        $this->assertEquals('Passwords do not match', $actual['confirm']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Validator::getForgotErrors
     */
    public function testGetForgotErrors()
    {
        $actual = Validator::getForgotErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Validator::getLoginErrors
     */
    public function testGetLoginErrors()
    {
        $actual = Validator::getLoginErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
        $this->assertEquals('Cannot be empty', $actual['auth_password']);

        $actual = Validator::getLoginErrors([
            'auth_slug' => 'not a real user',
            'auth_password' => 'not a real password'
        ]);

        $this->assertEquals('User does not exist', $actual['auth_slug']);

        $actual = Validator::getLoginErrors([
            'auth_slug' => 'john@doe.com',
            'auth_password' => 'not a real password'
        ]);

        $this->assertEquals('Password is incorrect', $actual['auth_password']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Validator::getRecoverErrors
     */
    public function testGetRecoverErrors()
    {
        $actual = Validator::getRecoverErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = Validator::getRecoverErrors([
            'auth_password' => '',
            'confirm' => ''
        ]);

        $this->assertEquals('Cannot be empty', $actual['auth_password']);
        $this->assertEquals('Cannot be empty', $actual['confirm']);

        $actual = Validator::getRecoverErrors([
            'auth_password' => '321',
            'confirm' => '123'
        ]);

        $this->assertEquals('Passwords do not match', $actual['confirm']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Validator::getUpdateErrors
     * @covers Cradle\Module\Oauth\Auth\Validator::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = Validator::getUpdateErrors(array());

        $this->assertEquals('Invalid ID', $actual['auth_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\Auth\Validator::getVerifyErrors
     */
    public function testGetVerifyErrors()
    {
        $actual = Validator::getVerifyErrors([]);

        $this->assertEquals('Cannot be empty', $actual['auth_slug']);
    }
}
