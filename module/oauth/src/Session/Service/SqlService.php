<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Oauth\Session\Service;

use PDO as Resource;
use Cradle\Sql\SqlFactory;

use Cradle\Module\Utility\Service\SqlServiceInterface;
use Cradle\Module\Utility\Service\AbstractSqlService;

/**
 * Session SQL Service
 *
 * @vendor   Acme
 * @package  Session
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class SqlService extends AbstractSqlService implements SqlServiceInterface
{
    /**
     * @const TABLE_NAME
     */
    const TABLE_NAME = 'session';

    /**
     * Registers the resource for use
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = SqlFactory::load($resource);
    }

    /**
     * Create in database
     *
     * @param array $data
     *
     * @return array
     */
    public function create(array $data)
    {
        return $this->resource
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
    public function get($id)
    {
        $search = $this->resource
            ->search('session')
            ->setColumns(
                'session.*',
                'app.*',
                'profile.*',
                'auth_id'
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

        $results = $search->getRow();

        if (!$results) {
            return $results;
        }

        //session_permissions
        if ($results['session_permissions']) {
            $results['session_permissions'] = json_decode($results['session_permissions'], true);
        } else {
            $results['session_permissions'] = [];
        }

        //app_permissions
        if ($results['app_permissions']) {
            $results['app_permissions'] = json_decode($results['app_permissions'], true);
        } else {
            $results['app_permissions'] = [];
        }

        return $results;
    }

    /**
     * Link to app
     *
     * @param *int $sessionId
     * @param *int $appId
     */
    public function linkApp($sessionId, $appId)
    {
        return $this->resource
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
        return $this->resource
            ->model()
            ->setSessionId($sessionId)
            ->setAuthId($authId)
            ->insert('session_auth');
    }

    /**
     * Remove from database
     * PLEASE BECAREFUL USING THIS !!!
     * It's here for clean up scripts
     *
     * @param *int $id
     */
    public function remove($id)
    {
        //please rely on SQL CASCADING ON DELETE
        return $this->resource
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
    public function search(array $data = [])
    {
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

        $search = $this->resource
            ->search('session')
            ->setColumns(
                'session.*',
                'app.*',
                'profile.*',
                'auth_id'
            )
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

        $rows = $search->getRows();
        foreach ($rows as $i => $row) {
            //session_permissions
            if ($row['session_permissions']) {
                $rows[$i]['session_permissions'] = json_decode($row['session_permissions'], true);
            } else {
                $rows[$i]['session_permissions'] = [];
            }

            //app_permissions
            if ($row['app_permissions']) {
                $rows[$i]['app_permissions'] = json_decode($row['app_permissions'], true);
            } else {
                $rows[$i]['app_permissions'] = [];
            }
        }

        //return response format
        return [
            'rows' => $rows,
            'total' => $search->getTotal()
        ];
    }

    /**
     * Unlink app
     *
     * @param *int $sessionId
     * @param *int $appId
     */
    public function unlinkApp($sessionId, $appId)
    {
        return $this->resource
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
        return $this->resource
            ->model()
            ->setSessionId($sessionId)
            ->setAuthId($authId)
            ->remove('session_auth');
    }

    /**
     * Update to database
     *
     * @param array $data
     *
     * @return array
     */
    public function update(array $data)
    {
        return $this->resource
            ->model($data)
            ->setSessionUpdated(date('Y-m-d H:i:s'))
            ->save('session')
            ->get();
    }
}
