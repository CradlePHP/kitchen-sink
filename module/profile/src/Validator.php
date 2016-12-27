<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
namespace Cradle\Module\Profile;

use Cradle\Module\Utility\Validator as UtilityValidator;

/**
 * Validator layer
 *
 * @vendor   Acme
 * @package  Profile
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
        // profile_name - required
        if (!isset($data['profile_name']) || empty($data['profile_name'])) {
            $errors['profile_name'] = 'Cannot be empty';
        }

        // profile_locale - required
        if (!isset($data['profile_locale']) || empty($data['profile_locale'])) {
            $errors['profile_locale'] = 'Cannot be empty';
        }

        //also add optional errors
        return self::getOptionalErrors($data, $errors);
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
        // profile_id            Required
        if (!isset($data['profile_id']) || empty($data['profile_id'])) {
            $errors['profile_id'] = 'Cannot be empty';
        }

        //profile_name        Required
        if (isset($data['profile_name']) && empty($data['profile_name'])) {
            $errors['profile_name'] = 'Cannot be empty, if set';
        }

        //profile_locale        Required
        if (isset($data['profile_locale']) && empty($data['profile_locale'])) {
            $errors['profile_locale'] = 'Cannot be empty, if set';
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
        // profile_gender - one of
        $choices = ['male', 'female'];
        if (isset($data['profile_gender']) && !in_array($data['profile_gender'], $choices)) {
            $errors['profile_gender'] = sprintf('Should be one of %s', implode(',', $choices));
        }

        // profile_birth - date
        if (isset($data['profile_birth']) && !UtilityValidator::isUrl($data['profile_birth'])) {
            $errors['profile_birth'] = 'Must be a valid date YYYY-MM-DD';
        }

        // profile_facebook - url
        if (isset($data['profile_facebook']) && !UtilityValidator::isUrl($data['profile_facebook'])) {
            $errors['profile_facebook'] = 'Should be a valid URL';
        }

        // profile_linkedin - url
        if (isset($data['profile_linkedin']) && !UtilityValidator::isUrl($data['profile_linkedin'])) {
            $errors['profile_linkedin'] = 'Should be a valid URL';
        }

        // profile_twitter - url
        if (isset($data['profile_twitter']) && !UtilityValidator::isUrl($data['profile_twitter'])) {
            $errors['profile_twitter'] = 'Should be a valid URL';
        }

        // profile_google - url
        if (isset($data['profile_google']) && !UtilityValidator::isUrl($data['profile_google'])) {
            $errors['profile_google'] = 'Should be a valid URL';
        }

        // profile_rating - small
        if (isset($data['profile_rating']) && !UtilityValidator::isSmall($data['profile_rating'])) {
            $errors['profile_rating'] = 'Should be between 0 and 9';
        }

        // profile_experience - int
        if (isset($data['profile_experience']) && !UtilityValidator::isInt($data['profile_experience'])) {
            $errors['profile_experience'] = 'Must be a valid integrer';
        }

        if (isset($data['profile_email']) && !UtilityValidator::isEmail($data['profile_email'])) {
            $errors['profile_email'] = 'Must be a valid e-mail address';
        //mailinator
        } else if (isset($data['profile_email']) &&
            strpos(strtolower($data['profile_email']), 'mailinator') !== false) {
            $errors['profile_email'] = 'This email has been blocked';
        }

        if (isset($data['profile_phone']) && preg_match('/[a-zA-Z]/i', $data['profile_phone'])) {
            $errors['profile_phone'] = 'Must be a valid phone number';
        }

        // profile_flag - small
        if (isset($data['profile_flag']) && !UtilityValidator::isSmall($data['profile_flag'])) {
            $errors['profile_flag'] = 'Should be between 0 and 9';
        }

        return $errors;
    }
}
