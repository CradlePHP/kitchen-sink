<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Module\Utility;

use Closure;

/**
 * Installer
 *
 * @vendor   Acme
 * @package  Utility
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Installer
{
    /**
     * Checks if a path exists
     *
     * @param *string $path
     */
    public static function getNextVersion($module)
    {
        //module root
        $root = cradle('global')->path('module');

        $install = $root . '/' . $module . '/install';

        //if there is no install
        if(!is_dir($install)) {
            return '0.0.1';
        }

        //collect and organize all the versions
        $versions = [];
        $files = scandir($install, 0);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || is_dir($install . '/' . $file)) {
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

            $versions[] = $version;
        }

        if(empty($versions)) {
            return '0.0.1';
        }

        //sort versions
        usort($versions, 'version_compare');

        $current = array_pop($versions);
        $revisions = explode('.', $current);
        $revisions = array_reverse($revisions);

        $found = false;
        foreach($revisions as $i => $revision) {
            if(!is_numeric($revision)) {
                continue;
            }

            $revisions[$i]++;
            $found = true;
            break;
        }

        if(!$found) {
            return $current . '.1';
        }

        $revisions = array_reverse($revisions);
        return implode('.', $revisions);
    }

    /**
     * Performs an install
     *
     * @return string The current version
     */
    public static function install($module = null)
    {
        //module root
        $root = cradle('global')->path('module');

        //if no module specified
        if(is_null($module)) {
            $versions = [];
            //get all the modules
            $modules = scandir($root, 0);

            //loop
            foreach($modules as $module) {
                //skip modules without an install directory
                if(!is_dir($root . '/' . $module . '/install')) {
                    continue;
                }

                //run it individually
                $versions[$module] = static::install($module);
            }

            return $versions;
        }

        $install = $root . '/' . $module . '/install';

        //if there is no install
        if(!is_dir($install)) {
            //this is the nada version
            return '0.0.0';
        }

        //collect and organize all the versions
        $versions = [];
        $files = scandir($install, 0);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || is_dir($install . '/' . $file)) {
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
                'script' => $install . '/' . $file,
                'mode' => $extension
            ];
        }

        //sort versions
        uksort($versions, 'version_compare');

        //get the current version
        $current = cradle('global')->config('version');
        if(!isset($current[$module])) {
            $current[$module] = '0.0.0';
        }

        //now run the scripts in order of version
        foreach ($versions as $version => $files) {
            //if 0.0.0 >= 0.0.1
            if (version_compare($current[$module], $version, '>=')) {
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
        if (version_compare($current[$module], $version, '<')) {
            $current[$module] = $version;
        }

        $file = cradle('global')->path('config') . '/version.php';

        $contents = '<?php return ' . var_export($current, true) . ';';
        file_put_contents($file, $contents);

        return $current[$module];
    }
}
