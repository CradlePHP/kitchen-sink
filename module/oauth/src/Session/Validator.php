<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
namespace Cradle\Module\Oauth\Session;

use Cradle\Module\Oauth\Session\Service;

use Cradle\Module\Utility\Validator as UtilityValidator;

/**
 * Validator layer
 *
 * @vendor   Acme
 * @package  Session
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Validator
{
    /**
     * Returns Access Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public static function getAccessErrors(array $data, array $errors = [])
    {
        //code        Required
        if (!isset($data['code']) || empty($data['code'])) {
            $errors['code'] = 'Cannot be empty';
        }

        //client_id        Required
        if (!isset($data['client_id']) || empty($data['client_id'])) {
            $errors['client_id'] = 'Cannot be empty';
        }

        //client_secret     Required
        if (!isset($data['client_secret']) || empty($data['client_secret'])) {
            $errors['client_secret'] = 'Cannot be empty';
        }

        if (empty($errors)) {
            $results = Service::get('sql')->get($data['code']);

            if (!$results) {
                $errors['code'] = 'Invalid Token';
            } else {
                if ($data['client_id'] !== $results['app_token']) {
                    $errors['client_id'] = 'Invalid Token';
                }

                if ($data['client_secret'] !== $results['app_secret']) {
                    $errors['client_secret'] = 'Invalid Token';
                }
            }
        }

        return $errors;
    }

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
        //session_permissions        Required
        if (!isset($data['session_permissions']) || empty($data['session_permissions'])) {
            $errors['session_permissions'] = 'Cannot be empty';
        }

        // auth_id - required
        if (!isset($data['auth_id'])) {
            $errors['auth_id'] = 'Invalid ID';
        }

        // app_id - required
        if (!isset($data['app_id'])) {
            $errors['app_id'] = 'Invalid ID';
        }

        return $errors;
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
        //session_id        Required
        if (!isset($data['session_id']) || empty($data['session_id'])) {
            $errors['session_id'] = 'Invalid ID';
        }

        //session_permissions        Required
        if (isset($data['session_permissions']) && empty($data['session_permissions'])) {
            $errors['session_permissions'] = 'Cannot be empty';
        }

        return $errors;
    }
}
