<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\{{capital name}}\Validator;

/**
 * Validator layer test
 *
 * @vendor   Acme
 * @package  {{capital name}}
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_{{capital name}}_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\{{capital name}}\Validator::getCreateErrors
     */
    public function testGetCreateErrors()
    {
        $actual = Validator::getCreateErrors([]);
        {{~#each fields}}{{~#each validation}}
        {{~#when method '===' 'required'}}
        $this->assertEquals('Required', $actual['{{../@key}}']);
        {{~/when}}{{/each}}{{/each}}
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Validator::getUpdateErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = Validator::getUpdateErrors([]);

        $this->assertEquals('Required', $actual['{{primary}}']);
    }
}
