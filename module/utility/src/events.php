<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * CLI project starting point
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('project', include __DIR__ . '/cli/project.php');

/**
 * CLI help menu
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('project-help', include __DIR__ . '/cli/help.php');

/**
 * CLI queue - bin/cradle project queue auth-verify auth_slug=<email>
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('project-queue', include __DIR__ . '/cli/queue.php');

/**
 * CLI starts worker
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-work', include __DIR__ . '/cli/work.php');

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-deploy-production', include __DIR__ . '/cli/deploy/production.php');

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-deploy-s3', include __DIR__ . '/cli/deploy/s3.php');

/**
 * CLI production connect
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-connect-to', include __DIR__ . '/cli/deploy/connect.php');

/**
 * CLI clear cache
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-flush-redis', include __DIR__ . '/cli/redis/flush.php');

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-flush-elastic', include __DIR__ . '/cli/elastic/flush.php');

/**
 * CLI map index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-map-elastic', include __DIR__ . '/cli/elastic/map.php');

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-populate-elastic', include __DIR__ . '/cli/elastic/populate.php');

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-flush-sql', include __DIR__ . '/cli/sql/flush.php');

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-build-sql', include __DIR__ . '/cli/sql/build.php');

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-populate-sql', include __DIR__ . '/cli/sql/populate.php');

/**
 * CLI project installation
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-install', include __DIR__ . '/cli/install.php');

/**
 * CLI project update
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-update', include __DIR__ . '/cli/update.php');

/**
 * CLI project server
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-server', include __DIR__ . '/cli/server.php');

/**
 * CLI app generate
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-generate-app', include __DIR__ . '/cli/generate/app.php');

/**
 * CLI module generate
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-generate-module', include __DIR__ . '/cli/generate/module.php');

/**
 * CLI view generate
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-generate-view', include __DIR__ . '/cli/generate/view.php');

/**
 * CLI SQL generate
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-generate-sql', include __DIR__ . '/cli/generate/sql.php');
