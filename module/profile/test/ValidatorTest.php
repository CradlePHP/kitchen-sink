<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Profile\Validator;

/**
 * Validator layer test
 *
 * @vendor   Salaaap
 * @package  Profile
 * @author   Christian Blanquera <cblanquera@openovate.com>
 */
class Cradle_Module_Profile_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\Profile\Validator::getCreateErrors
     * @covers Cradle\Module\Profile\Validator::getOptionalErrors
     */
    public function testGetCreateErrors()
    {
        $actual = Validator::getCreateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['profile_name']);
        $this->assertEquals('Cannot be empty', $actual['profile_locale']);
    }

    /**
     * @covers Cradle\Module\Profile\Validator::getUpdateErrors
     * @covers Cradle\Module\Profile\Validator::getOptionalErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = Validator::getUpdateErrors(array());

        $this->assertEquals('Cannot be empty', $actual['profile_id']);
    }
}
