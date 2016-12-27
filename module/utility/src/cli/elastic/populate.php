<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use Cradle\Module\Utility\ServiceFactory;

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $service = $this->package('global')->service('elastic-main');

    if (!$service) {
        CommandLine::error('ElasticSearch is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Building ElasticSearch...');

    $objects = array_keys(ServiceFactory::get('elastic'));

    foreach ($objects as $object) {
        CommandLine::info('Indexing ' . $object . '...');

        $sql = ServiceFactory::get($object, 'sql');
        $elastic = ServiceFactory::get($object, 'elastic');

        $i = 0;
        do {
            CommandLine::info('  - Indexing ' . $object . ': ' . $i . '-' . ($i + 100));

            $results = $sql->search([
                'start' => $i,
                'range' => 100
            ]);

            $rows = $results['rows'];
            $total = $results['total'];

            foreach ($rows as $row) {
                $primary = $object . '_id';

                if ($object === 'review') {
                    $primary = 'comment_id';
                }

                if ($elastic->create($row[$primary]) === false) {
                    //because there is no reason to continue;
                    CommandLine::warning('No index server found. Aborting...');
                    return;
                }
            }

            $i += 100;
        } while ($i < $total);
    }
};
