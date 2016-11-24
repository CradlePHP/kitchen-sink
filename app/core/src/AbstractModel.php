<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Core;

/**
 * Adds common methods to models
 *
 * @vendor   Custom
 * @package  Core
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
abstract class AbstractModel
{
    /**
     * @var Service $service
     */
    protected $service = null;

    /**
     * Registers the service for use
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Cache a detail set
     *
     * @param *scalar   $id
     * @param array|int $data
     *
     * @return array
     */
    public function cacheCreateDetail($id, $data)
    {
        $service = $this->service->cache();

        if(!$service || !$id) {
            return false;
        }

        //if an id was passed
        if(is_numeric($data)) {
            //get it from index
            $data = $this->indexDetail($data);

            //if no index
            if(!$data) {
                //get it from database
                $data = $this->databaseDetail($data);
            }
        }

        return $service->hSet(static::CACHE_DETAIL, $id, $data);
    }

    /**
     * Cache a search set
     *
     * @param *array     $parameters
     * @param array|null $data
     *
     * @return array
     */
    public function cacheCreateSearch(array $parameters, array $data = null)
    {
        $service = $this->service->cache();

        if(!$service) {
            return false;
        }

        $id = md5(json_encode($parameters));

        //if no data
        if(is_null($data)) {
            //get it from index
            $data = $this->indexSearch($parameters);

            //if no index
            if(!$data) {
                //get it from database
                $data = $this->databaseSearch($parameters);
            }
        }

        return $service->hSet(static::CACHE_SEARCH, $id, $data);
    }

    /**
     * Returns a cached detail
     *
     * @param *int $id
     *
     * @return array
     */
    public function cacheDetail($id)
    {
        $service = $this->service->cache();

        if(!$service || !$id) {
            return false;
        }

        if(!$this->cacheDetailExists($id)) {
            return false;
        }

        return $service->hGet(static::CACHE_DETAIL, $id);
    }


    /**
     * Returns true if a cached detail exists
     *
     * @param *int $id
     *
     * @return array
     */
    public function cacheDetailExists($id)
    {
        $service = $this->service->cache();

        if(!$service || !$id) {
            return false;
        }

        return $service->hExists(static::CACHE_DETAIL, $id);
    }

    /**
     * Remove a cache detail
     *
     * @param int|null $id
     *
     * @return array
     */
    public function cacheRemoveDetail($id = null)
    {
        $service = $this->service->cache();

        if(!$service) {
            return false;
        }

        if(is_null($id)) {
            return $service->del(static::CACHE_DETAIL);
        }

        if(!$id) {
            return false;
        }

        return $service->hDel(static::CACHE_DETAIL, $id);
    }

    /**
     * Removes a cache search
     *
     * @param array|null $data
     *
     * @return array
     */
    public function cacheRemoveSearch(array $parameters = null)
    {
        $service = $this->service->cache();

        if(!$service) {
            return false;
        }

        if(is_null($parameters)) {
            return $service->del(static::CACHE_SEARCH);
        }

        $id = md5(json_encode($parameters));
        return $service->hDel(static::CACHE_SEARCH, $id);
    }

    /**
     * Returns a cached search
     *
     * @param array $parameters
     *
     * @return array
     */
    public function cacheSearch(array $parameters)
    {
        $service = $this->service->cache();

        if(!$service) {
            return false;
        }

        if(!$this->cacheSearchExists($parameters)) {
            return false;
        }

        $id = md5(json_encode($parameters));
        return $service->hGet(static::CACHE_SEARCH, $id);
    }

    /**
     * Returns true if a cached search exists
     *
     * @param *array $data
     *
     * @return array
     */
    public function cacheSearchExists(array $parameters)
    {
        $service = $this->service->cache();

        if(!$service) {
            return false;
        }

        $id = md5(json_encode($parameters));
        return $service->hExists(static::CACHE_SEARCH, $id);
    }

    /**
     * Create in database
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function databaseCreate(array $data);

    /**
     * Get detail from database
     *
     * @param *int|string $id
     *
     * @return array
     */
    abstract public function databaseDetail($id);

    /**
     * Remove from database
     * PLEASE BECAREFUL USING THIS !!!
     * It's here for clean up scripts
     *
     * @param *int $id
     */
    abstract public function databaseRemove($id);

    /**
     * Search in database
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function databaseSearch(array $data = []);

    /**
     * Update to database
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function databaseUpdate(array $data);

    /**
     * Create in index
     *
     * @param *int $id
     *
     * @return array
     */
    public function indexCreate($id)
    {
        $service = $this->service->index();

        if(!$service) {
            return false;
        }

        return $service->index([
            'index' => 'main',
            'type' => static::INDEX_TYPE,
            'id' => $id,
            'body' => $this->databaseDetail($id)
        ]);
    }

    /**
     * Get detail from index
     *
     * @param *int|string $id
     *
     * @return array
     */
    public function indexDetail($id)
    {
        $service = $this->service->index();

        if(!$service) {
            return false;
        }

        $results = $service->get([
            'index' => 'main',
            'type' => static::INDEX_TYPE,
            'id' => $id
        ]);

        return $results['_source'];
    }

    /**
     * Remove from index
     *
     * @param *int $id
     */
    public function indexRemove($id)
    {
        $service = $this->service->index();

        if(!$service) {
            return false;
        }

        return $service->delete([
            'index' => 'main',
            'type' => static::INDEX_TYPE,
            'id' => $id
        ]);
    }

    /**
     * Search in index
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function indexSearch(array $data = []);

    /**
     * Update to index
     *
     * @param *int $id
     *
     * @return array
     */
    public function indexUpdate($id)
    {
        $service = $this->service->index();

        if(!$service) {
            return false;
        }

        return $service->update(
            [
                'index' => 'main',
                'type' => static::INDEX_TYPE,
                'id' => $id,
                'body' => [
                    'doc' => $this->databaseDetail($id)
                ]
            ]
        );
    }
}
