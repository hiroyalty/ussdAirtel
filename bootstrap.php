<?php

session_start();

/**
 * Add Dorm's library and domain classes in include path
 */
set_include_path(
        dirname(__FILE__)
        . PATH_SEPARATOR . dirname(__FILE__) . '/lib'
        . PATH_SEPARATOR . get_include_path()
);

/**
 * Register autoload so we don't have to require() and include() every file
 * by hand.
 */
if ($_REQUEST['code'] == '676') {
    include_once 'App2.php';
} else {
    include_once 'App.php';
}



