<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Utility\Service;

/**
 * ElasticSearch map interface
 *
 * @vendor   Salaaap
 * @package  Utility
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
interface ElasticServiceInterface
{
    /**
     * Create in index
     *
     * @param *int $id
     *
     * @return array
     */
    public function create($id);

    /**
     * Get detail from index
     *
     * @param *int|string $id
     *
     * @return array
     */
    public function get($id);

    /**
     * Remove from index
     *
     * @param *int $id
     */
    public function remove($id);

    /**
     * Search in index
     *
     * @param array $data
     *
     * @return array
     */
    public function search(array $data = []);

    /**
     * Update to index
     *
     * @param *int $id
     *
     * @return array
     */
    public function update($id);
}
