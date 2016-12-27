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
    {
        //validations
        {{#each property}}{{#each validation.create~}}
        {{#when type '===' 'required'}}
        if(!isset($data['{{../name}}']) || empty($data['{{../name}}'])) {
            $errors['{{../name}}'] = 'Required';
        }
        {{~/when}}{{#when type '===' 'empty'}}
        if(isset($data['{{../name}}']) && empty($data['{{../name}}'])) {
            $errors['{{../name}}'] = 'Cannot be empty';
        }
        {{~/when}}{{#when type '===' 'email'}}
        if(isset($data['{{../name}}'])
            && preg_match('/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]'.
            '\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]'.
            '\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62'.
            '}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|'.
            '[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})'.
            '(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]'.
            '+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25'.
            '[0-5])){3}\])$/', $data['{{../name}}'])
        )
        {
            $errors['{{../name}}'] = 'Must be a valid email address';
        }
        {{~/when}}{{#when type '===' 'password'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) < 8) {
            $errors['{{../name}}'] = 'Must be at least 8 characters';
        }
        {{~/when}}{{#when type '===' 'url'}}
        if(isset($data['{{../name}}'])
            && preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
            '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $data['{{../name}}'])
        )
        {
            $errors['{{../name}}'] = 'Must be a valid URL';
        }
        {{~/when}}{{#when type '===' 'host'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+)$/i',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid host name';
        }
        {{~/when}}{{#when type '===' 'date'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}(\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}){0,1}$/is',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid date format';
        }
        {{~/when}}{{#when type '===' 'time'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/is',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid time format';
        }
        {{~/when}}{{#when type '===' 'hex'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^(#){0,1}[0-9a-fA-F]{6}$/is',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid hexidecimal format';
        }
        {{~/when}}{{#when type '===' 'number'}}
        if(isset($data['{{../name}}']) && !is_numeric($data['{{../name}}'])) {
            $errors['{{../name}}'] = 'Must be a valid number';
        }
        {{~/when}}{{#when type '===' 'image'}}
        $validImages = ['jpg', 'gif', 'png', 'jpeg', 'svg'];
        if(isset($data['{{../name}}']) && !in_array($data['{{../name}}'], $validImages)) {
        $errors['{{../name}}'] = 'Must be a valid image';
        }
        {{~/when}}{{#when type '===' 'textarea'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) < 10) {
            $errors['{{../name}}'] = 'Must be at least 10 characters';
        }
        {{~/when}}{{#when type '===' 'slug'}}
        if(isset($data['{{../name}}'])
            && preg_match('/^[a-z0-9\-\_]+$/is', $data['{{../name}}'])
        )
        {
            $errors['{{../name}}'] = 'Must be a valid slug';
        }
        {{~/when}}{{#when type '===' 'small'}}
        if(isset($data['{{../name}}'])
        && (
            strlen($data['{{../name}}']) < 0
            || strlen($data['{{../name}}']) > 9
        )
        ) {
        $errors['{{../name}}'] = 'Must be between 0 and 9';
        }
        {{~/when}}{{#when type '===' 'bool'}}
        if(isset($data['{{../name}}'])
            && (
                strlen($data['{{../name}}']) < 0
                || strlen($data['{{../name}}']) > 1
            )
        ) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'gt'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] <= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'gte'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] < {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'lt'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] >= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'lte'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] > {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'sgt'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) <= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'sgte'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) < {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'slt'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) >= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'slte'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) > {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}
        {{~/each}}{{/each}}
        return $errors;
    }

    /**
     * Returns Product Update Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public static function getUpdateErrors(array $data, array $errors = [])
    {
        //validations
        {{#each property}}{{#each validation.update~}}
        {{#when type '===' 'required'}}
        if(!isset($data['{{../name}}']) || empty($data['{{../name}}'])) {
            $errors['{{../name}}'] = 'Required';
        }
        {{~/when}}{{#when type '===' 'empty'}}
        if(isset($data['{{../name}}']) && empty($data['{{../name}}'])) {
            $errors['{{../name}}'] = 'Cannot be empty';
        }
        {{~/when}}{{#when type '===' 'email'}}
        if(isset($data['{{../name}}'])
            && preg_match('/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]'.
            '\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]'.
            '\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62'.
            '}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|'.
            '[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})'.
            '(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]'.
            '+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25'.
            '[0-5])){3}\])$/', $data['{{../name}}'])
        )
        {
            $errors['{{../name}}'] = 'Must be a valid email address';
        }
        {{~/when}}{{#when type '===' 'password'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) < 8) {
            $errors['{{../name}}'] = 'Must be at least 8 characters';
        }
        {{~/when}}{{#when type '===' 'url'}}
        if(isset($data['{{../name}}'])
            && preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
            '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $data['{{../name}}'])
        )
        {
            $errors['{{../name}}'] = 'Must be a valid URL';
        }
        {{~/when}}{{#when type '===' 'host'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+)$/i',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid host name';
        }
        {{~/when}}{{#when type '===' 'date'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}(\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}){0,1}$/is',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid date format';
        }
        {{~/when}}{{#when type '===' 'time'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/is',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid time format';
        }
        {{~/when}}{{#when type '===' 'hex'}}
        if(isset($data['{{../name}}'])
            && preg_match(
                '/^(#){0,1}[0-9a-fA-F]{6}$/is',
                $data['{{../name}}']
            )
        )
        {
            $errors['{{../name}}'] = 'Must be a valid hexidecimal format';
        }
        {{~/when}}{{#when type '===' 'number'}}
        if(isset($data['{{../name}}']) && !is_numeric($data['{{../name}}'])) {
            $errors['{{../name}}'] = 'Must be a valid number';
        }
        {{~/when}}{{#when type '===' 'image'}}
        $validImages = ['jpg', 'gif', 'png', 'jpeg', 'svg'];
        if(isset($data['{{../name}}']) && !in_array($data['{{../name}}'], $validImages)) {
        $errors['{{../name}}'] = 'Must be a valid image';
        }
        {{~/when}}{{#when type '===' 'textarea'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) < 10) {
            $errors['{{../name}}'] = 'Must be at least 10 characters';
        }
        {{~/when}}{{#when type '===' 'slug'}}
        if(isset($data['{{../name}}'])
            && preg_match('/^[a-z0-9\-\_]+$/is', $data['{{../name}}'])
        )
        {
            $errors['{{../name}}'] = 'Must be a valid slug';
        }
        {{~/when}}{{#when type '===' 'small'}}
        if(isset($data['{{../name}}'])
        && (
            strlen($data['{{../name}}']) < 0
            || strlen($data['{{../name}}']) > 9
        )
        ) {
        $errors['{{../name}}'] = 'Must be between 0 and 9';
        }
        {{~/when}}{{#when type '===' 'bool'}}
        if(isset($data['{{../name}}'])
            && (
                strlen($data['{{../name}}']) < 0
                || strlen($data['{{../name}}']) > 1
            )
        ) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'gt'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] <= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'gte'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] < {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'lt'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] >= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'lte'}}
        if(isset($data['{{../name}}']) && $data['{{../name}}'] > {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'sgt'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) <= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'sgte'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) < {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'slt'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) >= {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}{{#when type '===' 'slte'}}
        if(isset($data['{{../name}}']) && strlen($data['{{../name}}']) > {{options.length}}) {
            $errors['{{../name}}'] = 'Must be either 0 or 1';
        }
        {{~/when}}
        {{~/each}}{{/each}}
        return $errors;
    }
}
