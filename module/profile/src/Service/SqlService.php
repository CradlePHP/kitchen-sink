<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Profile\Service;

use PDO as Resource;
use Cradle\Sql\SqlFactory;

use Cradle\Module\Utility\Service\SqlServiceInterface;
use Cradle\Module\Utility\Service\AbstractSqlService;

/**
 * Profile SQL Service
 *
 * @vendor   Acme
 * @package  Profile
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class SqlService extends AbstractSqlService implements SqlServiceInterface
{
    /**
     * @const TABLE_NAME
     */
    const TABLE_NAME = 'profile';

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
            ->setProfileCreated(date('Y-m-d H:i:s'))
            ->setProfileUpdated(date('Y-m-d H:i:s'))
            ->save('profile')
            ->get();
    }

    /**
     * Checks to see if email or phone already exists
     *
     * @param *string $email
     * @param *string $phone
     * @param *string $type
     *
     * @return bool
     */
    public function exists($email = null, $phone = null, $type = null)
    {
        $search = $this->resource->search('profile');

        if ($type) {
            $search->filterByProfileType($type);
        }

        if ($email && $phone) {
            $search->addFilter(
                '(profile_email = %s OR profile_phone = %s)',
                $email,
                $phone
            );
        } else if ($email) {
            $search->filterByProfileEmail($email);
        //profile phone
        } else {
            $search->filterByProfilePhone($phone);
        }

        return $search->getRow();
    }

    /**
     * Get detail from database
     *
     * @param *int $id
     *
     * @return array
     */
    public function get($id)
    {
        $search = $this->resource->search('profile');

        if (!is_numeric($id) && $id) {
            $search->filterByProfileSlug($id);
        } else {
            $search->filterByProfileId($id);
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
    public function remove($id)
    {
        //please rely on SQL CASCADING ON DELETE
        return $this->resource
            ->model()
            ->setProfileId($id)
            ->remove('profile');
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

        if (!isset($filter['profile_active'])) {
            $filter['profile_active'] = 1;
        }

        $search = $this->resource
            ->search('profile')
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
    public function update(array $data)
    {
        return $this->resource
            ->model($data)
            ->setProfileUpdated(date('Y-m-d H:i:s'))
            ->save('profile')
            ->get();
    }
}
