<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
namespace Cradle\Module\Oauth\App;

use Cradle\Module\Utility\Validator as UtilityValidator;

/**
 * Validator layer
 *
 * @vendor   Acme
 * @package  App
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
        // app_name - required
        if (!isset($data['app_name']) || empty($data['app_name'])) {
            $errors['app_name'] = 'Cannot be empty';
        }

        // app_domain - required
        if (!isset($data['app_domain']) || empty($data['app_domain'])) {
            $errors['app_domain'] = 'Cannot be empty';
        }

        // app_permissions - required
        if (!isset($data['app_permissions']) || empty($data['app_permissions'])) {
            $errors['app_permissions'] = 'Cannot be empty';
        }

        // profile_id - required
        if (!isset($data['profile_id']) || !is_numeric($data['profile_id'])) {
            $errors['profile_id'] = 'Invalid ID';
        }

        //also add optional errors
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
        // app_id - required
        if (!isset($data['app_id']) || !is_numeric($data['app_id'])) {
            $errors['app_id'] = 'Invalid ID';
        }

        // app_name - required
        if (isset($data['app_name']) && empty($data['app_name'])) {
            $errors['app_name'] = 'Cannot be empty';
        }

        // app_domain - required
        if (isset($data['app_domain']) && empty($data['app_domain'])) {
            $errors['app_domain'] = 'Cannot be empty';
        }

        // app_permissions - required
        if (isset($data['app_permissions']) && empty($data['app_permissions'])) {
            $errors['app_permissions'] = 'Cannot be empty';
        }

        //also add optional errors
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
        // app_website - url
        if (isset($data['app_website']) && !UtilityValidator::isUrl($data['app_website'])) {
            $errors['app_website'] = 'Should be a valid URL';
        }

        // app_flag - small
        if (isset($data['app_flag']) && !UtilityValidator::isSmall($data['app_flag'])) {
            $errors['app_flag'] = 'Should be between 0 and 9';
        }

        return $errors;
    }
}
