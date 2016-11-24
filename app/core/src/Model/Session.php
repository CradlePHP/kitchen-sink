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

/**
 * Session Model
 *
 * @vendor   Custom
 * @package  Core
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Session extends AbstractModel
{
    /**
     * @const CACHE_SEARCH Cache search key
     */
    const CACHE_SEARCH = 'core-session-search';

    /**
     * @const CACHE_DETAIL Cache detail key
     */
    const CACHE_DETAIL = 'core-session-detail';

    /**
     * @const INDEX_TYPE Index type name aka collection name
     */
    const INDEX_TYPE = 'session';

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
            ->setSessionToken(md5(uniqid()))
            ->setSessionSecret(md5(uniqid()))
            ->setSessionCreated(date('Y-m-d H:i:s'))
            ->setSessionUpdated(date('Y-m-d H:i:s'))
            ->save('session')
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
            ->search('session')
            ->setColumns(
                'session.*',
                'app.*',
                'profile.*'
            )
            ->innerJoinUsing('session_auth', 'session_id')
            ->innerJoinUsing('session_app', 'session_id')
            ->innerJoinUsing('app', 'app_id')
            ->innerJoinUsing('auth_profile', 'auth_id')
            ->innerJoinUsing('profile', 'profile_id');

        if (is_numeric($id)) {
            $search->filterBySessionId($id);
        } else {
            $search->filterBySessionToken($id);
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
            ->setSessionId($id)
            ->remove('session');
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

        $search = $service
            ->search('session')
            ->innerJoinUsing('session_auth', 'session_id')
            ->innerJoinUsing('auth_profile', 'auth_id')
            ->innerJoinUsing('session_app', 'session_id')
            ->innerJoinUsing('app', 'app_id')
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

                $where[] = 'LOWER(profile_name) LIKE %s';
                $where[] = 'LOWER(app_name) LIKE %s';
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
            ->setSessionUpdated(date('Y-m-d H:i:s'))
            ->save('session')
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
        $service = $this->service->database();

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
                'fields' => ['profile_name', 'app_name'],
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
        $rows = array();

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
     * Returns Access Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getAccessErrors(array $data, array $errors = [])
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
            $results = $this->databaseDetail($data['code']);

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
    public function getCreateErrors(array $data, array $errors = [])
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
    public function getUpdateErrors(array $data, array $errors = [])
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

    /**
     * Link to app
     *
     * @param *int $sessionId
     * @param *int $appId
     */
    public function linkApp($sessionId, $appId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setSessionId($sessionId)
            ->setAppId($appId)
            ->insert('session_app');
    }

    /**
     * Link to auth
     *
     * @param *int $sessionId
     * @param *int $authId
     */
    public function linkAuth($sessionId, $authId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setSessionId($sessionId)
            ->setAuthId($authId)
            ->insert('session_auth');
    }

    /**
     * Unlink app
     *
     * @param *int $sessionId
     * @param *int $appId
     */
    public function unlinkApp($sessionId, $appId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setSessionId($sessionId)
            ->setAppId($appId)
            ->remove('session_app');
    }

    /**
     * Unlink auth
     *
     * @param *int $sessionId
     * @param *int $authId
     */
    public function unlinkAuth($sessionId, $authId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $services
            ->model()
            ->setSessionId($sessionId)
            ->setAuthId($authId)
            ->remove('session_auth');
    }
}
