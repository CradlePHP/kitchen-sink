<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
namespace Cradle\Module\{{camel name 1}};

use Cradle\Module\{{camel name 1}}\Service as {{camel name 1}}Service;

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
            {{~#each validation}}
                {{~#when method '===' 'required'}}
        if(!isset($data['{{../@key}}']) || empty($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = '{{message}}';
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
    {
        {{#if primary~}}
        if(!isset($data['{{primary}}']) || !is_numeric($data['{{primary}}'])) {
            $errors['{{primary}}'] = 'Invalid ID';
        }

        {{/if}}
        {{~#each fields}}
            {{~#each validation}}
                {{~#when method '===' 'required'}}
        if(isset($data['{{../@key}}']) && empty($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = '{{message}}';
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
            {{~#each validation}}
                {{~#when method '===' 'empty'}}
        if(isset($data['{{../@key}}']) && empty($data['{{../@key}}'])) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'one'}}
        $choices = array({{implode parameters ', '}});
        if (isset($data['{{../@key}}']) && !in_array($data['{{../@key}}'], $choices)) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'regexp'}}
        if (isset($data['{{../@key}}']) && !preg_match('{{parameters}}', $data['{{../@key}}'])) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'gt'}}
        if(isset($data['{{../@key}}']) && $data['{{../@key}}'] <= {{parameters}}) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'lt'}}
        if(isset($data['{{../@key}}']) && $data['{{../@key}}'] >= {{parameters}}) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'char_gt'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) <= {{parameters}}) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'char_lt'}}
        if(isset($data['{{../@key}}']) && strlen($data['{{../@key}}']) >= {{parameters}}) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'word_gt'}}
        if(isset($data['{{../@key}}']) && str_word_count($data['{{../@key}}']) <= {{parameters}}) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'word_lt'}}
        if(isset($data['{{../@key}}']) && str_word_count($data['{{../@key}}']) >= {{parameters}}) {
            $errors['{{../@key}}'] = '{{message}}';
        }
                {{/when}}

                {{~#when method '===' 'unique'}}
        if(isset($data['{{../@key}}'])) {
            $search = {{camel name 1}}Service::get('sql')
                ->getResource()
                ->search('{{../../name}}')
                ->filterBy{{camel ../@key 1}}($data['{{../@key}}']);

            if(isset($data['{{../../primary}}'])) {
                $search->addFilter('{{../../primary}} != %s', $data['{{../../primary}}']);
            }

            if($search->getTotal()) {
                $errors['{{../@key}}'] = '{{message}}';
            }
        }
                {{/when}}
            {{~/each}}
        {{~/each}}
        return $errors;
    }
}
