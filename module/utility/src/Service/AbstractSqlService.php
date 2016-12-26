<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Utility\Service;

use PDO as Resource;

/**
 * Abstract SQL Service
 *
 * @vendor   Salaaap
 * @package  Utility
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
abstract class AbstractSqlService
{
    /**
     * @var AbstractSql|null $resource
     */
    protected $resource = null;

    /**
     * Registers the resource for use
     *
     * @param Resource $resource
     */
    abstract public function __construct(Resource $resource);

    /**
     * Create in database
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function create(array $data);

    /**
     * Get detail from database
     *
     * @param *int $id
     *
     * @return array
     */
    abstract public function get($id);

    /**
     * Remove from database
     * PLEASE BECAREFUL USING THIS !!!
     * It's here for clean up scripts
     *
     * @param *int $id
     */
    abstract public function remove($id);

    /**
     * Search in database
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function search(array $data = []);

    /**
     * Update to database
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function update(array $data);
}
