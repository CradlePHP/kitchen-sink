<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Oauth\Auth\Service;

use PDO as Resource;
use Cradle\Sql\SqlFactory;

use Cradle\Module\Utility\Service\SqlServiceInterface;
use Cradle\Module\Utility\Service\AbstractSqlService;

/**
 * Auth SQL Service
 *
 * @vendor   Acme
 * @package  Auth
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class SqlService extends AbstractSqlService implements SqlServiceInterface
{
    /**
     * @const TABLE_NAME
     */
    const TABLE_NAME = 'auth';

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
            ->setAuthToken(md5(uniqid()))
            ->setAuthSecret(md5(uniqid()))
            ->setAuthCreated(date('Y-m-d H:i:s'))
            ->setAuthUpdated(date('Y-m-d H:i:s'))
            ->save('auth')
            ->get();
    }

    /**
     * Checks to see if the slug already exists
     *
     * @param *string      $slug
     * @param string|false $password
     *
     * @return bool
     */
    public function exists($slug, $password = false)
    {
        $search = $this->resource
            ->search('auth')
            ->filterByAuthSlug($slug);

        if ($password) {
            $search->filterByAuthPassword(md5($password));
        }

        return !!$search->getRow();
    }

    /**
     * Get detail from database
     *
     * @param *int|string $id
     *
     * @return array
     */
    public function get($id, $all = false)
    {
        $search = $this->resource
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
                'auth_active',
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

        $results = $search->getRow();

        if (!$results) {
            return $results;
        }

        //auth_permissions
        if ($results['auth_permissions']) {
            $results['auth_permissions'] = json_decode($results['auth_permissions'], true);
        } else {
            $results['auth_permissions'] = [];
        }

        return $results;
    }

    /**
     * Links product to profile
     *
     * @param *int $authId
     * @param *int $profileId
     */
    public function linkProfile($authId, $profileId)
    {
        return $this->resource
            ->model()
            ->setAuthId($authId)
            ->setProfileId($profileId)
            ->insert('auth_profile');
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

        if (!isset($filter['auth_active'])) {
            $filter['auth_active'] = 1;
        }

        $search = $this->resource
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

        $rows = $search->getRows();
        foreach ($rows as $i => $row) {
            //auth_permissions
            if ($row['auth_permissions']) {
                $rows[$i]['auth_permissions'] = json_decode($row['auth_permissions'], true);
            } else {
                $rows[$i]['auth_permissions'] = [];
            }

            //dont show this
            unset($rows[$i]['auth_password']);
        }

        //return response format
        return [
            'rows' => $rows,
            'total' => $search->getTotal()
        ];
    }

    /**
     * Unlinks product to profile
     *
     * @param *int $authId
     * @param *int $profileId
     */
    public function unlinkProfile($authId, $profileId)
    {
        return $this->resource
            ->model()
            ->setAuthId($authId)
            ->setProfileId($profileId)
            ->remove('auth_profile');
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
            ->setAuthUpdated(date('Y-m-d H:i:s'))
            ->save('auth')
            ->get();
    }
}
