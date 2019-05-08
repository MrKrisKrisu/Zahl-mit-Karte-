<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', realpath(dirname(dirname(__FILE__))));


Initializer::includeAll(BASE_PATH . '/includes');
Initializer::initialize();

/**
 * @author Kristian Stöckel https://github.com/MrKrisKrisu
 */
class Initializer {

    public static function initialize() {
        if (!isset($_SERVER['SERVER_NAME']) || $_SERVER['SERVER_NAME'] == '')
            return;

        new PageManager();
    }

    public static function includeAll($dir) {
        foreach (glob($dir . '/*.php') as $filename)
            require_once $filename;
        foreach (scandir($dir) as $ff) {
            if ($ff != '.' && $ff != '..' && is_dir($dir . '/' . $ff)) {
                foreach (glob($dir . '/' . $ff . '/*.php') as $filename) {
                    require_once $filename;
                }
                foreach (scandir($dir . '/' . $ff) as $ff2) {
                    if (is_dir($dir . '/' . $ff . '/' . $ff2)) {
                        foreach (glob($dir . '/' . $ff . '/' . $ff2 . '/*.php') as $filename) {
                            require_once $filename;
                        }
                    }
                }
            }
        }
    }

}
