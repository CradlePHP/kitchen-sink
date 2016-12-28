<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
namespace Cradle\Module\{{capital name}};

use Cradle\Module\Utility\Validator as UtilityValidator;

/**
 * Validator layer
 *
 * @vendor   Acme
 * @package  {{name}}
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Validator
{
    /**
     * Returns Create Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public static function getCreateErrors(array $data, array $errors = [])
    { {{#each fields}}
            {{~#each valid}}
                {{~#when this.0 '===' 'required'}}
        if(!isset($data['{{../@key}}']) || empty($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = 'Required';
        }
                {{/when}}
            {{~/each}}
        {{~/each}}
        return self::getOptionalErrors($data, $errors);
    }

    /**
     * Returns Update Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public static function getUpdateErrors(array $data, array $errors = [])
    { {{#each fields}}
            {{~#each valid}}
                {{~#when this.0 '===' 'required'}}
        if(isset($data['{{../@key}}']) && empty($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = 'Required';
        }
                {{/when}}
            {{~/each}}
        {{~/each}}
        return self::getOptionalErrors($data, $errors);
    }

    /**
     * Returns Optional Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public static function getOptionalErrors(array $data, array $errors = [])
    {
        //validations
        {{#each fields}}
            {{~#each valid}}
                {{~#when this.0 '===' 'empty'}}
        if(isset($data['{{../@key}}']) && empty($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = 'Cannot be empty';
        }
                {{/when}}

                {{~#when value.[0] '===' 'one'}}
        $choices = array({{implode value.[1] ', '}});
        if (isset($data['{{../@key}}']) && !in_array($data['{{../@key}}'], $choices)) {
            $errors['{{../@key}}'] = sprintf('Must be one of %s', implode(',', $choices));
        }
                {{/when}}

                {{~#when this.0 '===' 'email'}}
        if(isset($data['{{../@key}}'])
            && preg_match('/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]'.
            '\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]'.
            '\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62'.
            '}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|'.
            '[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})'.
            '(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]'.
            '+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25'.
            '[0-5])){3}\])$/', $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid email address';
        }
                {{/when}}

                {{~#when this.0 '===' 'password'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) < 8) {
            $errors['{{../@key}}'] = 'Must be at least 8 characters';
        }
                {{/when}}

                {{~#when this.0 '===' 'url'}}
        if(isset($data['{{../@key}}'])
            && preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
            '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid URL';
        }
                {{/when}}

                {{~#when this.0 '===' 'host'}}
        if(isset($data['{{../@key}}'])
            && preg_match(
                '/^([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+)$/i',
                $data['{{../@key}}']
            )
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid host name';
        }
                {{/when}}

                {{~#when this.0 '===' 'date'}}
        if(isset($data['{{../@key}}'])
            && preg_match(
                '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}(\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}){0,1}$/is',
                $data['{{../@key}}']
            )
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid date format';
        }
                {{/when}}

                {{~#when this.0 '===' 'time'}}
        if(isset($data['{{../@key}}'])
            && preg_match(
                '/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/is',
                $data['{{../@key}}']
            )
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid time format';
        }
                {{/when}}

                {{~#when this.0 '===' 'hex'}}
        if(isset($data['{{../@key}}'])
            && preg_match(
                '/^(#){0,1}[0-9a-fA-F]{6}$/is',
                $data['{{../@key}}']
            )
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid hexidecimal format';
        }
                {{/when}}

                {{~#when this.0 '===' 'number'}}
        if(isset($data['{{../@key}}']) && !is_numeric($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = 'Must be a valid number';
        }
                {{/when}}

                {{~#when this.0 '===' 'int'}}
        if(isset($data['{{../@key}}'])
            && !preg_match(
                '/^[0-9]+$/',
                (string) $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid integer';
        }
                {{/when}}

                {{~#when this.0 '===' 'float'}}
        if(isset($data['{{../@key}}'])
            && !preg_match(
                '/^[0-9]*(\.[0-9]+){0,1}$/',
                (string) $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid float';
        }
                {{/when}}

                {{~#when this.0 '===' 'price'}}
        if(isset($data['{{../@key}}'])
            && !preg_match(
                '/^[0-9]+(\.[0-9]{2}){0,1}$/',
                (string) $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid price';
        }
                {{/when}}

                {{~#when this.0 '===' 'image'}}
        $validImages = ['jpg', 'gif', 'png', 'jpeg', 'svg'];
        if(isset($data['{{../@key}}']) && !in_array($data['{{../@key}}'], $validImages)) {
            $errors['{{../@key}}'] = 'Must be a valid image';
        }
                {{/when}}

                {{~#when this.0 '===' 'textarea'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) < 10) {
            $errors['{{../@key}}'] = 'Must be at least 10 characters';
        }
                {{/when}}

                {{~#when this.0 '===' 'slug'}}
        if(isset($data['{{../@key}}'])
            && preg_match('/^[a-z0-9\-\_]+$/is', $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Must be a valid slug';
        }
                {{/when}}

                {{~#when this.0 '===' 'small'}}
        if(isset($data['{{../@key}}'])
            && (
                strlen($data['{{../@key}}']) < 0
                || strlen($data['{{../@key}}']) > 9
            )
        )
        {
            $errors['{{../@key}}'] = 'Must be between 0 and 9';
        }
                {{/when}}

                {{~#when this.0 '===' 'bool'}}
        if(isset($data['{{../@key}}'])
            && (
                strlen($data['{{../@key}}']) < 0
                || strlen($data['{{../@key}}']) > 1
            )
        )
        {
            $errors['{{../@key}}'] = 'Must be either 0 or 1';
        }
                {{/when}}

                {{~#when this.0 '===' 'regex'}}
        if (isset($data['{{../@key}}'])
            && !empty($data['{{../@key}}'])
            && !preg_match('{{this.1]}}', $data['{{../@key}}'])
        )
        {
            $errors['{{../@key}}'] = 'Invalid format';
        }
                {{/when}}

                {{~#when this.0 '===' 'gt'}}
        if(isset($data['{{../@key}}']) && $data['{{../@key}}'] <= {{this.1}}) {
            $errors['{{../@key}}'] = 'Must be greater than {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'gte'}}
        if(isset($data['{{../@key}}']) && $data['{{../@key}}'] < {{this.1}}) {
            $errors['{{../@key}}'] = 'Must be greater than or equal to {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'lt'}}
        if(isset($data['{{../@key}}']) && $data['{{../@key}}'] >= {{this.1}}) {
            $errors['{{../@key}}'] = 'Must be less than {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'lte'}}
        if(isset($data['{{../@key}}']) && $data['{{../@key}}'] > {{this.1}}) {
            $errors['{{../@key}}'] = 'Must be less than or equal to {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'sgt'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) <= {{this.1}}) {
            $errors['{{../@key}}'] = 'Length must be greater than {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'sgte'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) < {{this.1}}) {
            $errors['{{../@key}}'] = 'Length must be greater than or equal to {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'slt'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) >= {{this.1}}) {
            $errors['{{../@key}}'] = 'Length must be less than {{this.1}}';
        }
                {{/when}}

                {{~#when this.0 '===' 'slte'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) > {{this.1}}) {
            $errors['{{../@key}}'] = 'Length must be less than or equal to {{this.1}}';
        }
                {{/when}}
            {{~/each}}
        {{~/each}}
        return $errors;
    }
}
