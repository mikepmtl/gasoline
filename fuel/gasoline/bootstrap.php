<?php

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

/**
 * This is the bootstrap file for Gasoline which registers the namespace "Gasoline"
 * and also adds Gasoline to the finder search paths, amongst other things
 */

// Add the namespace for the gasoline-package as a core namespace to the autoloader.
//  The second argument allows the namespace to be added as the first core namespace.
Autoloader::add_core_namespace('Gasoline', true);

// Also add the whole namespace
Autoloader::add_namespace('Gasoline', GASPATH . 'classes/');

// This is needed because these classes would actually reside under namespace Gasoline
//  but since they need to be globally accessible, we add them here as classes
Autoloader::add_classes(array(
    'Auth_Acl_Gasauth'      => __DIR__ . '/classes/auth/acl/gasacl.php',
    'Auth_Group_Gasauth'    => __DIR__ . '/classes/auth/group/gasgroup.php',
    'Auth_Login_Gasauth'    => __DIR__ . '/classes/auth/login/gasauth.php',
));

// We also need to add the GASPATH to the finder-instance so we can load config
//  and other stuff from there
Finder::instance()->add_path(GASPATH, 1);

// Register a function to the 'shutdown'-event to store the currently accessed page
//  to a session-variable (for redirecting back and forth). This will only happen,
//  if there's been a change between pages so we won't set the last_page to the
//  current page if the user hits reload
Event::register('shutdown', function() {
    ( Uri::current() != Session::get('last_page') ) && Session::set('last_page', Uri::current());
});

/* End of file bootstrap.php */
/* Location: ./fuel/gasoline/bootstrap.php */
