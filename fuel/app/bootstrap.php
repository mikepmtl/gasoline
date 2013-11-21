<?php

// Load in the Autoloader
require COREPATH.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

// Bootstrap the framework DO NOT edit this
require COREPATH.'bootstrap.php';

// Define the constant GASPATH pointing to our gasoline-package
define('GASPATH', realpath(__DIR__ . '/../gasoline/') . DIRECTORY_SEPARATOR);

Autoloader::add_classes(array(
    // Overwriting some classes from \Fuel\Core
    'Asset_Instance'    => __DIR__ . '/classes/asset/instance.php',
    'Html'              => __DIR__ . '/classes/html.php',
    'Lang'              => __DIR__ . '/classes/lang.php',
    'Str'               => __DIR__ . '/classes/str.php',
    'Validation'        => __DIR__ . '/classes/validation.php',
    'View'              => __DIR__ . '/classes/view.php',
    'ViewModel'         => __DIR__ . '/classes/viewmodel.php',
    
    // Gasauth related models aliased to the fuel app namespace so that they're
    //  overwriting the same files from package 'auth'
    'Model\\Auth_Group'             => __DIR__.'/classes/model/auth/group.php',
    'Model\\Auth_Grouppermission'   => __DIR__.'/classes/model/auth/group/permission.php',
    'Model\\Auth_Group_Permission'  => __DIR__.'/classes/model/auth/group/permission.php',
    'Model\\Auth_Metadata'          => __DIR__.'/classes/model/auth/metadata.php',
    'Model\\Auth_Permission'        => __DIR__.'/classes/model/auth/permission.php',
    'Model\\Auth_Role'              => __DIR__.'/classes/model/auth/role.php',
    'Model\\Auth_Rolepermission'    => __DIR__.'/classes/model/auth/role/permission.php',
    'Model\\Auth_Role_Permission'   => __DIR__.'/classes/model/auth/role/permission.php',
    'Model\\Auth_User'              => __DIR__.'/classes/model/auth/user.php',
    'Model\\Auth_Userpermission'    => __DIR__.'/classes/model/auth/user/permission.php',
    'Model\\Auth_User_Permission'   => __DIR__.'/classes/model/auth/user/permission.php',
    
    // Overwriting some classes from the ORM package
    'Orm\\Observer_Auditor' => __DIR__ . '/classes/orm/observer/auditor.php',
    'Orm\\Observer_Id'      => __DIR__ . '/classes/orm/observer/id.php',
    'Orm\\Observer_Slug'    => __DIR__ . '/classes/orm/observer/slug.php',
));

// Register the autoloader
Autoloader::register();

// And require the bootstrap (which does some namespace-adding, etc.)
require(GASPATH . 'bootstrap.php');

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
Fuel::$env = (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::DEVELOPMENT);

// Initialize the framework with the config file.
Fuel::init('config.php');
