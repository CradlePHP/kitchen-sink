<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\App\Validator;

/**
 * Validator layer test
 *
 * @vendor   Acme
 * @package  OAuth
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_Oauth_App_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\Oauth\App\Validator::getCreateErrors
     * @covers Cradle\Module\Oauth\App\Validator::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = Validator::getCreateErrors([]);

        $this->assertEquals('Cannot be empty', $actual['app_name']);
        $this->assertEquals('Cannot be empty', $actual['app_domain']);
        $this->assertEquals('Cannot be empty', $actual['app_permissions']);
        $this->assertEquals('Invalid ID', $actual['profile_id']);

        $actual = Validator::getCreateErrors([
            'app_name' => '',
            'app_permissions' => '',
            'app_domain' => '',
            'profile_id' => 'foobar'
        ]);

        $this->assertEquals('Cannot be empty', $actual['app_name']);
        $this->assertEquals('Cannot be empty', $actual['app_domain']);
        $this->assertEquals('Cannot be empty', $actual['app_permissions']);
        $this->assertEquals('Invalid ID', $actual['profile_id']);
    }

    /**
     * @covers Cradle\Module\Oauth\App\Validator::getUpdateErrors
     * @covers Cradle\Module\Oauth\App\Validator::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = Validator::getUpdateErrors([]);

        $this->assertEquals('Invalid ID', $actual['app_id']);

        $actual = Validator::getUpdateErrors([
            'app_id' => 'foobar',
            'app_name' => '',
            'app_permissions' => '',
            'app_domain' => ''
        ]);

        $this->assertEquals('Invalid ID', $actual['app_id']);
        $this->assertEquals('Cannot be empty', $actual['app_name']);
        $this->assertEquals('Cannot be empty', $actual['app_domain']);
        $this->assertEquals('Cannot be empty', $actual['app_permissions']);
    }
}
