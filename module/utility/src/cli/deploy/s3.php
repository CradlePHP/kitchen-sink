<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use Cradle\Module\Utility\File;

use Aws\S3\S3Client;

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $cdn = cradle('global')->service('s3-main');

    if (!$cdn) {
        CommandLine::warning('CDN is not setup. Check config/services.php. Aborting.');
        return;
    }

    // load s3
    $s3 = S3Client::factory([
        'version' => 'latest',
        'region'  => $cdn['region'], //example ap-southeast-1
        'credentials' => [
            'key'    => $cdn['token'],
            'secret' => $cdn['secret'],
        ]
    ]);

    //get the public path
    $public = cradle('global')->path('public');

    //get all the files
    $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($public));

    foreach ($paths as $path) {
        //if it's a directory
        if ($path->isDir()) {
            continue;
        }

        //get the file string
        $file = $path->getPathname();

        //there's no point pushing these things
        if (preg_match('/(\.htaccess)|(\.php)|(DS_Store)|(bower_components)/', $file)) {
            continue;
        }

        // if /foo/bar/repo/public/path/to/file, then /path/to/file
        $path = substr($file, strlen($public) + 1);

        //there's no better way to get a mime
        $mime = File::getMimeFromLink($file);

        //open a pipe
        $pipe = fopen($file, 'r');

        print sprintf("\033[36m%s\033[0m", '[cradle] * pushing '.$path);
        print PHP_EOL;

        $s3->putObject(array(
            'Bucket'        => $cdn['bucket'],
            'ACL'           => 'public-read',
            'ContentType'   => $mime,
            'Key'           => 'web/'.$path,
            'Body'          => $pipe,
            'CacheControl'  => 'max-age=43200'
        ));

        if (is_resource($pipe)) {
            fclose($pipe);
        }
    }
};
