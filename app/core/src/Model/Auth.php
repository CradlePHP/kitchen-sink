<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Core\Model;

use Cradle\App\Core\AbstractModel;
use Cradle\App\Core\Service;
use Cradle\App\Core\Validator;

/**
 * Auth Model
 *
 * @vendor   Custom
 * @package  Core
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Auth extends AbstractModel
{
    /**
     * @const CACHE_SEARCH Cache search key
     */
    const CACHE_SEARCH = 'core-auth-search';

    /**
     * @const CACHE_DETAIL Cache detail key
     */
    const CACHE_DETAIL = 'core-auth-detail';

    /**
     * @const INDEX_TYPE Index type name aka collection name
     */
    const INDEX_TYPE = 'auth';

    /**
     * Create in database
     *
     * @param array $data
     *
     * @return array
     */
    public function databaseCreate(array $data)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model($data)
            ->setAuthToken(md5(uniqid()))
            ->setAuthSecret(md5(uniqid()))
            ->setAuthCreated(date('Y-m-d H:i:s'))
            ->setAuthUpdated(date('Y-m-d H:i:s'))
            ->save('auth')
            ->get();
    }

    /**
     * Get detail from database
     *
     * @param *int|string $id
     *
     * @return array
     */
    public function databaseDetail($id, $all = false)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        $search = $service
            ->search('auth')
            ->innerJoinUsing('auth_profile', 'auth_id')
            ->innerJoinUsing('profile', 'profile_id');

        if (!$all) {
            $search->setColumns(
                'auth_id',
                'auth_slug',
                'auth_token',
                'auth_permissions',
                'auth_type',
                'auth_created',
                'auth_updated',
                'profile.*'
            );
        }

        if (is_numeric($id)) {
            $search->filterByAuthId($id);
        } else {
            $search->filterByAuthSlug($id);
        }

        return $search->getRow();
    }

    /**
     * Remove from database
     * PLEASE BECAREFUL USING THIS !!!
     * It's here for clean up scripts
     *
     * @param *int $id
     */
    public function databaseRemove($id)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        //please rely on SQL CASCADING ON DELETE
        return $service
            ->model()
            ->setAuthId($id)
            ->remove('auth');
    }

    /**
     * Search in database
     *
     * @param array $data
     *
     * @return array
     */
    public function databaseSearch(array $data = [])
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        $filter = [];
        $range = 50;
        $start = 0;
        $order = [];
        $count = 0;
        $keywords = null;

        if (isset($data['filter']) && is_array($data['filter'])) {
            $filter = $data['filter'];
        }

        if (isset($data['range']) && is_numeric($data['range'])) {
            $range = $data['range'];
        }

        if (isset($data['start']) && is_numeric($data['start'])) {
            $start = $data['start'];
        }

        if (isset($data['order']) && is_array($data['order'])) {
            $order = $data['order'];
        }

        if (isset($data['q']) && is_array($data['q'])) {
            $keywords = $data['q'];
        }

        if (!isset($filter['auth_active'])) {
            $filter['auth_active'] = 1;
        }

        $search = $service
            ->search('auth')
            ->setStart($start)
            ->setRange($range);

        //add filters
        foreach ($filter as $column => $value) {
            if (preg_match('/^[a-zA-Z0-9-_]+$/', $column)) {
                $search->addFilter($column . ' = %s', $value);
            }
        }

        //keyword?
        if (isset($keywords)) {
            foreach ($keywords as $keyword) {
                $or = [];
                $where = [];

                $where[] = 'LOWER(auth_slug) LIKE %s';
                $or[] = '%' . strtolower($keyword) . '%';

                array_unshift($or, '(' . implode(' OR ', $where) . ')');

                call_user_func([$search, 'addFilter'], ...$or);
            }
        }

        //add sorting
        foreach ($order as $sort => $direction) {
            $search->addSort($sort, $direction);
        }

        //return response format
        return [
            'rows' => $search->getRows(),
            'total' => $search->getTotal()
        ];
    }

    /**
     * Update to database
     *
     * @param array $data
     *
     * @return array
     */
    public function databaseUpdate(array $data)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model($data)
            ->setAuthUpdated(date('Y-m-d H:i:s'))
            ->save('auth')
            ->get();
    }

    /**
     * Checks to see if the slug already exists
     *
     * @param *string $slug
     *
     * @return bool
     */
    public function exists($slug)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return !!$service
            ->search('auth')
            ->filterByAuthSlug($slug)
            ->getRow();
    }

    /**
     * Search in index
     *
     * @param array $data
     *
     * @return array
     */
    public function indexSearch(array $data = [])
    {
        $service = $this->service->index();

        if(!$service) {
            return false;
        }

        //set the defaults
        $keyword = null;
        $filters = [];
        $range = 50;
        $start = 0;
        $order = [];
        $count = 0;

        //merge passed data with default data
        if (isset($data['filter']) && is_array($data['filter'])) {
            $filters = $data['filter'];
        }

        if (isset($data['range']) && is_numeric($data['range'])) {
            $range = $data['range'];
        }

        if (isset($data['start']) && is_numeric($data['start'])) {
            $start = $data['start'];
        }

        if (isset($data['order']) && is_array($data['order'])) {
            $order = $data['order'];
        }

        //prepare the search object
        $search = [];

        if (isset($data['filter']) && is_array($data['filter'])) {
            $search['query']['bool']['must'][]['match'] = $data['filter'];
        }

        if (isset($data['keyword']) && !empty($data['keyword'])) {
            $search['query']['query_string'] = [
                'query' => $data['keyword'],
                'fields' => ['auth_slug', 'auth_type'],
                'default_operator' => 'OR'
            ];
        }

        //add sorting
        foreach ($order as $sort => $direction) {
            $search['sort'] = [$sort => $direction];
        }

        $results = $service->search([
            'index' => 'main',
            'type' => static::INDEX_TYPE,
            'body' => $search,
            'size' => $range,
            'from' => $start
        ]);

        // fix it
        $rows = [];

        foreach ($results['hits']['hits'] as $item) {
            $rows[] = $item['_source'];
        }

        //return response format
        return [
            'rows' => $rows,
            'total' => $results['hits']['total']
        ];
    }

    /**
     * Returns Create Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getCreateErrors(array $data, array $errors = [])
    {
        //auth_slug        Required
        if (!isset($data['auth_slug']) || empty($data['auth_slug'])) {
            $errors['auth_slug'] = 'Cannot be empty';
        } else if ($this->exists($data['auth_slug'])) {
            $errors['auth_slug'] = 'User Exists';
        }

        // auth_permissions        Required
        if (!isset($data['auth_permissions']) || empty($data['auth_permissions'])) {
            $errors['auth_permissions'] = 'Cannot be empty';
        }

        //auth_password        Required
        if (!isset($data['auth_password']) || empty($data['auth_password'])) {
            $errors['auth_password'] = 'Cannot be empty';
        }

        //confirm        NOT IN SCHEMA
        if (!isset($data['confirm']) || empty($data['confirm'])) {
            $errors['confirm'] = 'Cannot be empty';
        } else if ($data['confirm'] !== $data['auth_password']) {
            $errors['confirm'] = 'Passwords do not match';
        }

        //also add optional errors
        return $this->getOptionalErrors($data, $errors);
    }

    /**
     * Returns Login Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getForgotErrors(array $data, array $errors = [])
    {
        //auth_slug        Required
        if (!isset($data['auth_slug']) || empty($data['auth_slug'])) {
            $errors['auth_slug'] = 'Cannot be empty';
        }

        return $errors;
    }

    /**
     * Returns Login Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getLoginErrors(array $data, array $errors = [])
    {
        //auth_slug        Required
        if (!isset($data['auth_slug']) || empty($data['auth_slug'])) {
            $errors['auth_slug'] = 'Cannot be empty';
        } else if (!$this->exists($data['auth_slug'])) {
            $errors['auth_slug'] = 'User does not exist';
        }

        //auth_password        Required
        if (!isset($data['auth_password']) || empty($data['auth_password'])) {
            $errors['auth_password'] = 'Cannot be empty';
        } else if (!isset($errors['auth_slug'])) {
            $row = $this->service
                ->database()
                ->search('auth')
                ->filterByAuthSlug($data['auth_slug'])
                ->filterByAuthPassword(md5($data['auth_password']))
                ->getRow();

            if (!$row) {
                $errors['auth_password'] = 'Password is incorrect';
            }
        }

        return $errors;
    }

    /**
     * Returns Login Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getRecoverErrors(array $data, array $errors = [])
    {
        //auth_password        Required
        if (!isset($data['auth_password']) || empty($data['auth_password'])) {
            $errors['auth_password'] = 'Cannot be empty';
        }

        //confirm        NOT IN SCHEMA
        if (!isset($data['confirm']) || empty($data['confirm'])) {
            $errors['confirm'] = 'Cannot be empty';
        } else if ($data['confirm'] !== $data['auth_password']) {
            $errors['confirm'] = 'Passwords do not match';
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
    public function getUpdateErrors(array $data, array $errors = [])
    {
        // auth_id            Required
        if (!isset($data['auth_id']) || empty($data['auth_id'])) {
            $errors['auth_id'] = 'Invalid ID';
        }

        //auth_slug        Required
        if (isset($data['auth_slug']) && empty($data['auth_slug'])) {
            $errors['auth_slug'] = 'Cannot be empty, if set';
        //if there are no auth id errors
        } else if (isset($data['auth_slug']) && !isset($errors['auth_id'])) {
            //get the auth that we are updating
            $row = $this->service
                ->database()
                ->search('auth')
                ->filterByAuthId($data['auth_id'])
                ->getRow();

            //if the auth slug is changing
            if ($row['auth_slug'] !== $data['auth_slug']) {
                //find the new auth_slug
                $row = $this->service
                    ->database()
                    ->search('auth')
                    ->filterByAuthSlug($data['auth_slug'])
                    ->getRow();

                //if it is found
                if ($row) {
                    $errors['auth_slug'] = 'Already Taken';
                }
            }
        }

        // auth_permissions        Required
        if (isset($data['auth_permissions']) && empty($data['auth_permissions'])) {
            $errors['auth_permissions'] = 'Cannot be empty';
        }

        //confirm            NOT IN SCHEMA
        if ((
                !empty($data['auth_password']) || !empty($data['confirm'])
            )
            && $data['confirm'] !== $data['auth_password']
        ) {
            $errors['confirm'] = 'Passwords do not match';
        }

        //also add optional errors
        return $this->getOptionalErrors($data, $errors);
    }

    /**
     * Returns Login Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getVerifyErrors(array $data, array $errors = [])
    {
        //auth_slug        Required
        if (!isset($data['auth_slug']) || empty($data['auth_slug'])) {
            $errors['auth_slug'] = 'Cannot be empty';
        }

        return $errors;
    }

    /**
     * Returns Optional Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getOptionalErrors(array $data, array $errors = [])
    {
        // auth_flag - small
        if (isset($data['auth_flag']) && !Validator::isSmall($data['auth_flag'])) {
            $errors['auth_flag'] = 'Should be between 0 and 9';
        }

        return $errors;
    }

    /**
     * Links product to profile
     *
     * @param *int $authId
     * @param *int $profileId
     */
    public function linkProfile($authId, $profileId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setAuthId($authId)
            ->setProfileId($profileId)
            ->insert('auth_profile');
    }

    /**
     * Unlinks product to profile
     *
     * @param *int $authId
     * @param *int $profileId
     */
    public function unlinkProfile($authId, $profileId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setAuthId($authId)
            ->setProfileId($profileId)
            ->remove('auth_profile');
    }
}
