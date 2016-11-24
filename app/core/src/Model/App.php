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
 * App Model
 *
 * @vendor   Custom
 * @package  Core
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class App extends AbstractModel
{
    /**
     * @const CACHE_SEARCH Cache search key
     */
    const CACHE_SEARCH = 'core-app-search';

    /**
     * @const CACHE_DETAIL Cache detail key
     */
    const CACHE_DETAIL = 'core-app-detail';

    /**
     * @const INDEX_TYPE Index type name aka collection name
     */
    const INDEX_TYPE = 'app';

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
            ->setAppToken(md5(uniqid()))
            ->setAppSecret(md5(uniqid()))
            ->setAppCreated(date('Y-m-d H:i:s'))
            ->setAppUpdated(date('Y-m-d H:i:s'))
            ->save('app')
            ->get();
    }

    /**
     * Get detail from database
     *
     * @param *int|string $id
     *
     * @return array
     */
    public function databaseDetail($id)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        $search = $service
            ->search('app')
            ->innerJoinUsing('app_profile', 'app_id')
            ->innerJoinUsing('profile', 'profile_id');

        if (is_numeric($id)) {
            $search->filterByAppId($id);
        } else {
            $search->filterByAppToken($id);
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
            ->setAppId($id)
            ->remove('app')
            ->get();
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

        if (isset($data['q'])) {
            if (!is_array($data['q'])) {
                $data['q'] = [$data['q']];
            }

            $keywords = $data['q'];
        }

        if (!isset($filter['app_active'])) {
            $filter['app_active'] = 1;
        }

        $search = $service
            ->search('app')
            ->innerJoinUsing('app_profile', 'app_id')
            ->innerJoinUsing('profile', 'profile_id')
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

                $where[] = 'LOWER(app_name) LIKE %s';
                $where[] = 'LOWER(app_website) LIKE %s';
                $where[] = 'LOWER(app_domain) LIKE %s';
                $or[] = '%' . strtolower($keyword) . '%';
                $or[] = '%' . strtolower($keyword) . '%';
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
            ->setAppUpdated(date('Y-m-d H:i:s'))
            ->save('app')
            ->get();
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
                'fields' => ['app_name', 'app_domain', 'app_website'],
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
            'rows'  => $rows,
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
        if (!isset($data['profile_id'])) {
            $errors['profile_id'] = 'Invalid ID';
        }

        //also add optional errors
        return $this->getOptionalErrors($data, $errors);
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
        // app_id - required
        if (!isset($data['app_id']) || empty($data['app_id'])) {
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
        return $this->getOptionalErrors($data, $errors);
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
        // app_website - url
        if (isset($data['app_website']) && !Validator::isUrl($data['app_website'])) {
            $errors['app_website'] = 'Should be a valid URL';
        }

        // app_flag - small
        if (isset($data['app_flag']) && !Validator::isSmall($data['app_flag'])) {
            $errors['app_flag'] = 'Should be between 0 and 9';
        }

        return $errors;
    }

    /**
     * Links profile
     *
     * @param *int $appId
     * @param *int $profileId
     */
    public function linkProfile($appId, $profileId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setAppId($appId)
            ->setProfileId($profileId)
            ->insert('app_profile');
    }

    /**
     * Unlinks profile
     *
     * @param *int $appId
     * @param *int $profileId
     */
    public function unlinkProfile($appId, $profileId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setAppId($appId)
            ->setProfileId($profileId)
            ->remove('app_profile');
    }
}
