<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;

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

    CommandLine::system('Flushing ElasticSearch...');

    try {
        $service->indices()->delete(['index' => '*']);
    } catch (Missing404Exception $e) {
    } catch (NoNodesAvailableException $e) {
        //because there is no reason to continue
        CommandLine::warning('No index server found. Aborting...');
    }
};
