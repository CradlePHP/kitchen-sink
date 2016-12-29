<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Sample field:
 * [
 *      'sql' => [
 *         'type' => 'varchar',
 *         'length' => 255,
 *         'attributes' => 'unsigned',
 *         'default' => 'Foobar',
 *         'comment' => 'foobar',
 *         'required' => true,
 *         'key' => true,
 *         'unique' => true,
 *         'primary' => true,
 *         'encoding' => false
 *     ],
 *     'elastic' => [
 *         'type' => 'string',
 *         'fields' => [
 *             'keyword' => [
 *                 'type' => 'keyword'
 *             ]
 *         ]
 *     ],
 *     'form' => [
 *         'label' => 'Text Example',
 *         'type' => false,
 *         'default' => 'foobar',
 *         'attributes' => [
 *             'placeholder' => 'Sample Text',
 *         ],
 *         'options' => [
 *             '' => 'Choose one',
 *             'choice1' => 'Choice 1',
 *             'choice2' => 'Choice 2',
 *         ],
 *         'scripts' => []
 *     ],
 *     'list' => [
 *         'label' => 'Text',
 *         'searchable' => true,
 *         'sortable' => true,
 *         'filterable' => true,
 *         'format' => length,
 *         'parameters' => 255
 *     ],
 *     'detail' => [
 *         'label' => 'Text',
 *         'format' => 'date',
 *         'parameters' => 'Y-m-d H:i:s'
 *     ],
 *     'validation' => [
 *         [
 *             'method' => 'required',
 *             'message' => 'Is required',
 *             'parameters' => []
 *         ]
 *     ],
 *     'test' => [
 *         'pass' => 'foo',
 *         'fail' => 'bar'
 *     ]
 * ],
 *
 * SQL Encoding Options
 * - md5
 * - sha1
 * - uuid
 * - datetime
 * - date
 * - time
 * - json
 * - small
 * - bool
 * - int
 * - price
 * - [inline]
 *
 * Form Type Options
 * - input
 * - select
 * - textarea
 * - radio
 * - radios
 * - checkbox
 * - checkboxes
 * - button
 * - [inline]
 *
 * List and Detail Format Options
 * - date
 * - length
 * - words
 * - link
 * - image
 * - email
 * - phone
 * - capital
 * - implode
 * - upper
 * - lower
 * - [inline]
 *
 * Validation Method Options
 * - required
 * - empty
 * - one
 * - gt
 * - lt
 * - char_gt
 * - char_lt
 * - word_gt
 * - word_lt
 * - regexp
 * - unique
 * - [inline]
 */
return function($field) {
    //auto set the encoding
    if(isset($field['sql']['type']) && !isset($field['sql']['encoding'])) {
        switch($field['sql']['type']) {
            case 'datetime':
            case 'date':
            case 'time':
            case 'json':
            case 'bool':
                $field['sql']['encoding'] = $field['sql']['type'];
                break;
            case 'int':
                if(isset($field['sql']['length']) && $field['sql']['length'] === 1) {
                    $field['sql']['encoding'] = 'small';
                }
                break;
        }
    }

    if(isset($field['form']['type'])) {
        switch($field['form']['type']) {
            case 'image':
                $field['form']['type'] = 'file';
                $field['form']['attributes']['accept'] = 'image/*';
            case 'file':
            case 'hidden': //sometimes used for JS
            case 'color':
            case 'date':
            case 'email':
            case 'month':
            case 'number':
            case 'password':
            case 'range':
            case 'search':
            case 'tel':
            case 'text':
            case 'time':
            case 'url':
            case 'week':
                $field['form']['attributes']['type'] = $field['form']['type'];
                $field['form']['type'] = 'input';
                break;
        }

        //add bootstrap class
        if(in_array(
            $field['form']['type'],
                [
                    'input',
                    'select',
                    'textarea'
                ]
            )
        )
        {
            if(isset($field['form']['attributes']['class'])) {
                $field['form']['attributes']['class'] .= ' form-control';
            } else {
                $field['form']['attributes']['class'] = 'form-control';
            }
        }

        //tag
        if($field['form']['type'] === 'tag-field') {
            $code = file_get_contents(__DIR__ . '/../template/fields/tags.html');
            $code = str_replace('{NAME}', $field['name'], $code);
            $field['form']['type'] = 'inline';
            $field['form']['code'] = trim($code);
        }

        //image
        if($field['form']['type'] === 'image-field') {
            $code = file_get_contents(__DIR__ . '/../template/fields/image.html');
            $code = str_replace('{NAME}', $field['name'], $code);
            $field['form']['type'] = 'inline';
            $field['form']['code'] = trim($code);
        }

        //images
        if($field['form']['type'] === 'images-field') {
            $code = file_get_contents(__DIR__ . '/../template/fields/images.html');
            $code = str_replace('{NAME}', $field['name'], $code);
            $field['form']['type'] = 'inline';
            $field['form']['code'] = trim($code);
        }

        //attributes
        if($field['form']['type'] === 'meta-field') {
            $code = file_get_contents(__DIR__ . '/../template/fields/meta.html');
            $code = str_replace('{NAME}', $field['name'], $code);
            $field['form']['type'] = 'inline';
            $field['form']['code'] = trim($code);
        }

        //these are all the possible form types
        if(!in_array(
            $field['form']['type'],
                [
                    'input',
                    'select',
                    'textarea',
                    'radio',
                    'radios',
                    'checkbox',
                    'checkboxes',
                    'button',
                    'inline'
                ]
            )
        )
        {
            //if not then its inline
            $field['form']['code'] = $field['form']['type'];
            $field['form']['type'] = 'inline';
        }
    }

    //noop to prevent nested if
    if(isset($field['list']) && !isset($field['list']['format'])) {
        $field['list']['format'] = 'noop';
    }

    //these are all the possible list formats
    if(isset($field['list']['format']) && !in_array(
        $field['list']['format'],
            [
                'date',
                'length',
                'words',
                'link',
                'image',
                'email',
                'phone',
                'capital',
                'implode',
                'upper',
                'lower',
                'noop',
                'inline'
            ]
        )
    )
    {
        //if not then its inline
        $field['list']['code'] = $field['list']['format'];
        $field['list']['format'] = 'inline';
    }

    //noop to prevent nested if
    if(isset($field['detail']) && !isset($field['detail']['format'])) {
        $field['detail']['format'] = 'noop';
    }

    //these are all the possible detail formats
    if(isset($field['detail']['format']) && !in_array(
        $field['detail']['format'],
            [
                'date',
                'length',
                'words',
                'link',
                'image',
                'email',
                'phone',
                'capital',
                'implode',
                'upper',
                'lower',
                'noop',
                'inline'
            ]
        )
    )
    {
        //if not then its inline
        $field['detail']['code'] = $field['list']['format'];
        $field['detail']['format'] = 'inline';
    }

    if(isset($field['validation'])) {
        foreach($field['validation'] as $validation) {
            if($validation['method'] === 'required') {
                $field['required'] = true;
                break;
            }
        }
    }

    return $field;
};
