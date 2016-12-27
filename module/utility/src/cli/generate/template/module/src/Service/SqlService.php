<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\{{camel name 1}}\Service;

use PDO as Resource;
use Cradle\Sql\SqlFactory;

use Cradle\Module\Utility\Service\SqlServiceInterface;
use Cradle\Module\Utility\Service\AbstractSqlService;

/**
 * {{capital name}} SQL Service
 *
 * @vendor   Acme
 * @package  {{name}}
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class SqlService extends AbstractSqlService implements SqlServiceInterface
{
    /**
     * @const TABLE_NAME
     */
    const TABLE_NAME = '{{name}}';

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
            {{#if created}}
            ->set{{camel created 1}}(date('Y-m-d H:i:s'))
            {{/if~}}
            {{#if updated}}
            ->set{{camel updated 1}}(date('Y-m-d H:i:s'))
            {{/if~}}
            ->save('{{name}}')
            ->get();
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
        $search = $this->resource->search('{{name}}');

        {{#if one-to-one}}
        {{~#each one-to-one}}
        $search->innerJoinUsing('{{../name}}_{{@key}}', '{{../primary}}');
        {{/each~}}
        {{/if}}
        {{#if unique~}}
        if (is_numeric($id)) {
            $search->filterBy{{camel primary 1}}($id);
        {{~#each unique}}
        } else if (isset($data['{{this}}'])) {
            $search->filterBy{{camel this 1}}($id);
        {{/each~}}
        }
        {{~else~}}
        $search->filterBy{{camel primary 1}}($id);
        {{~/if}}

        $results = $search->getRow();

        if(!$results) {
            return $results;
        }

        {{#if json}}
        {{~#each json}}
        if($results['{{this}}']) {
            $results['{{this}}'] = json_decode($results['{{this}}'], true);
        } else {
            $results['{{this}}'] = [];
        }
        {{/each~}}
        {{/if}}

        return $results;
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
            ->set{{camel primary 1}}($id)
            ->remove('{{name}}');
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
        {{#if searchable}}
        $keywords = null;
        {{/if}}
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

        {{#if searchable}}
        if (isset($data['q']) && is_array($data['q'])) {
            $keywords = $data['q'];
        }
        {{/if}}

        {{#if active}}
        if (!isset($filter['{{active}}'])) {
            $filter['{{active}}'] = 1;
        }
        {{/if}}

        $search = $this->resource
            ->search('{{name}}')
            ->setStart($start)
            ->setRange($range);

        {{#if one-to-one}}
        //relations
        {{~#each one-to-one}}
        $search->innerJoinUsing('{{../name}}_{{@key}}', '{{../primary}}');
        {{/each~}}
        {{/if}}

        //add filters
        foreach ($filter as $column => $value) {
            if (preg_match('/^[a-zA-Z0-9-_]+$/', $column)) {
                $search->addFilter($column . ' = %s', $value);
            }
        }

        {{#if searchable}}
        //keyword?
        if (isset($keywords)) {
            foreach ($keywords as $keyword) {
                $or = [];
                $where = [];
                {{#each searchable~}}
                $where[] = 'LOWER({{this}}) LIKE %s';
                $or[] = '%' . strtolower($keyword) . '%';
                {{~/each}}
                array_unshift($or, '(' . implode(' OR ', $where) . ')');

                call_user_func([$search, 'addFilter'], ...$or);
            }
        }
        {{/if}}

        //add sorting
        foreach ($order as $sort => $direction) {
            $search->addSort($sort, $direction);
        }

        $rows = $search->getRows();

        foreach($rows as $i => $results) {
            {{#if json}}
            {{~#each json}}
            if($results['{{this}}']) {
                $rows[$i]['{{this}}'] = json_decode($results['{{this}}'], true);
            } else {
                $rows[$i]['{{this}}'] = [];
            }
            {{/each~}}
            {{/if}}
            $rows[$i] = $results;
        }

        //return response format
        return [
            'rows' => $results,
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
            {{#if updated}}
            ->set{{camel updated 1}}(date('Y-m-d H:i:s'))
            {{/if~}}
            ->save('{{name}}')
            ->get();
    }
    {{#each relations~}}
    /**
     * Links {{@key}}
     *
     * @param *int ${{../name}}Primary
     * @param *int ${{@key}}Primary
     */
    public function link{{capital @key}}(${{../name}}Primary, ${{@key}}Primary)
    {
        return $this->resource
            ->model()
            ->set{{camel ../primary 1}}(${{../name}}Primary)
            ->set{{camel primary 1}}(${{@key}}Primary)
            ->insert('{{../name}}_{{@key}}');
    }

    /**
     * Unlinks {{@key}}
     *
     * @param *int ${{../name}}Primary
     * @param *int ${{@key}}Primary
     */
    public function unlink{{capital @key}}(${{../name}}Primary, ${{@key}}Primary)
    {
        return $this->resource
            ->model()
            ->set{{camel ../primary 1}}(${{../name}}Primary)
            ->set{{camel primary 1}}(${{@key}}Primary)
            ->remove('{{../name}}_{{@key}}');
    }
    {{/each}}
}
