<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Utility;

use Closure;

/**
 * Installer
 *
 * @vendor   Salaaap
 * @package  Utility
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Installer
{
    /**
     * @var array $paths
     */
    protected static $paths = [];

    /**
     * Registers an installer directory
     *
     * @param *string $path
     */
    public static function register($path)
    {
        self::$paths[] = $path;
    }

    /**
     * Performs an install
     *
     * @return string The current version
     */
    public static function install()
    {
        $versions = [];

        //get all the scripts
        foreach (self::$paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $files = scandir($path, 0);

            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || is_dir($path . '/' . $file)) {
                    continue;
                }

                //get extension
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                if ($extension !== 'php'
                    && $extension !== 'sh'
                    && $extension !== 'sql'
                ) {
                    continue;
                }

                //get base as version
                $version = pathinfo($file, PATHINFO_FILENAME);

                //validate version
                if (!(version_compare($version, '0.0.1', '>=') >= 0)) {
                    continue;
                }

                $versions[$version][] = [
                    'script' => $path . '/' . $file,
                    'mode' => $extension
                ];
            }
        }

        //sort versions
        uksort($versions, 'version_compare');

        //get the current version
        $current = cradle('global')->config('version');

        foreach ($versions as $version => $files) {
            //if 0.0.0 >= 0.0.1
            if (version_compare($current, $version, '>=')) {
                continue;
            }

            //run the scripts
            foreach ($files as $file) {
                switch ($file['mode']) {
                    case 'php':
                        include $file['script'];
                        break;
                    case 'sql':
                        $query = file_get_contents($file['script']);
                        cradle('global')
                            ->service('sql-main')
                            ->query($query);
                        break;
                    case 'sh':
                        exec($file['script']);
                        break;
                }
            }
        }

        //if 0.0.0 < 0.0.1
        if (version_compare($current, $version, '<')) {
            $current = $version;
        }

        $file = cradle('global')->path('config') . '/version.php';
        file_put_contents($file, '<?php return \'' . $current . '\';');

        return $current;
    }
}
