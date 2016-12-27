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
 * @author   Christian Blanquera <cblanquera@openovate.com>
 */
class Cradle_Module_{{capital name}}_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Cradle\Module\{{capital name}}\Validator::getCreateErrors
     */
    public function testGetCreateErrors()
    {
        $actual = Validator::getCreateErrors([]);

        {{#each property}}{{#each validation.create~}}
        {{#when type '===' 'required'}}
        $this->assertEquals('Required', $data['{{../name}}']);
        {{~/when}}
        {{/each}}{{/each}}
    }

    /**
     * @covers Cradle\Module\{{capital name}}\Validator::getUpdateErrors
     */
    public function testGetUpdateErrors()
    {
        $actual = Validator::getUpdateErrors([]);

        {{#each property}}{{#each validation.update~}}
        {{#when type '===' 'required'}}
        $this->assertEquals('Required', $data['{{../name}}']);
        {{~/when}}
        {{/each}}{{/each}}
    }
}
