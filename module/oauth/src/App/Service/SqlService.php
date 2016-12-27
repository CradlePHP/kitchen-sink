<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Oauth\App\Service;

use PDO as Resource;
use Cradle\Sql\SqlFactory;

use Cradle\Module\Utility\Service\SqlServiceInterface;
use Cradle\Module\Utility\Service\AbstractSqlService;

/**
 * App SQL Service
 *
 * @vendor   Acme
 * @package  App
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class SqlService extends AbstractSqlService implements SqlServiceInterface
{
    /**
     * @const TABLE_NAME
     */
    const TABLE_NAME = 'app';

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
    public function get($id)
    {
        $search = $this->resource
            ->search('app')
            ->innerJoinUsing('app_profile', 'app_id')
            ->innerJoinUsing('profile', 'profile_id');

        if (is_numeric($id)) {
            $search->filterByAppId($id);
        } else {
            $search->filterByAppToken($id);
        }

        $results = $search->getRow();

        if (!$results) {
            return $results;
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
     * Links profile
     *
     * @param *int $appId
     * @param *int $profileId
     */
    public function linkProfile($appId, $profileId)
    {
        return $this->resource
            ->model()
            ->setAppId($appId)
            ->setProfileId($profileId)
            ->insert('app_profile');
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

        if (isset($data['q'])) {
            if (!is_array($data['q'])) {
                $data['q'] = [$data['q']];
            }

            $keywords = $data['q'];
        }

        if (!isset($filter['app_active'])) {
            $filter['app_active'] = 1;
        }

        $search = $this->resource
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

        $rows = $search->getRows();
        foreach ($rows as $i => $row) {
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
     * Unlinks profile
     *
     * @param *int $appId
     * @param *int $profileId
     */
    public function unlinkProfile($appId, $profileId)
    {
        return $this->resource
            ->model()
            ->setAppId($appId)
            ->setProfileId($profileId)
            ->remove('app_profile');
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
            ->setAppUpdated(date('Y-m-d H:i:s'))
            ->save('app')
            ->get();
    }
}
