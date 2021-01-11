<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 14.11.2018
 * Time: 16:36
 */

namespace chw;

define('PATH', realpath(dirname(__file__)) . '/classes') . '/';
define('DS', DIRECTORY_SEPARATOR);

class autoloader
{
    private static $__loader;


    private function __construct()
    {
        spl_autoload_register(array($this, 'autoLoad'));
    }


    public static function init()
    {
        if (self::$__loader == null) {
            self::$__loader = new self();
        }

        return self::$__loader;
    }


    public function autoLoad($class)
    {
        $exts = array('.class.php');

        $newDirectories = ['/path/to/a', '/path/to/b'];
        $path = get_include_path().PATH_SEPARATOR;
        $path .= implode(PATH_SEPARATOR, $newDirectories);
        set_include_path($path);

        spl_autoload_extensions("'" . implode(',', $exts) . "'");
        set_include_path(get_include_path() . PATH_SEPARATOR . PATH);

        foreach ($exts as $ext) {
            if (is_readable($path = BASE . strtolower($class . $ext))) {
                require_once $path;
                return true;
            }
        }
        self::recursiveAutoLoad($class, PATH);
    }

    private static function recursiveAutoLoad($class, $path)
    {
        if (is_dir($path)) {
            if (($handle = opendir($path)) !== false) {
                while (($resource = readdir($handle)) !== false) {
                    if (($resource == '..') or ($resource == '.')) {
                        continue;
                    }

                    if (is_dir($dir = $path . DS . $resource)) {
                        continue;
                    } else
                        if (is_readable($file = $path . DS . $resource)) {
                            require_once $file;
                        }
                }
                closedir($handle);
            }
        }
    }
}
