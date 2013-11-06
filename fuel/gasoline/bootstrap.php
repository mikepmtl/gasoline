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
    // Classes for the auth package cannot be added with the gasoline namespace
    // as they must be within the global namespace to be accessible
    'Auth_Acl_Gasacl'       => GASPATH . '/classes/auth/acl/gasacl.php',
    'Auth_Group_Gasgroup'   => GASPATH . '/classes/auth/group/gasgroup.php',
    'Auth_Login_Gasauth'    => GASPATH . '/classes/auth/login/gasauth.php',
    
    'Gasoline\\DataContainer'   => GASPATH . '/classes/datacontainer.php',
    'Gasoline\\Helpers'         => GASPATH . '/classes/helpers.php',
    'Gasoline\\Html'            => GASPATH . '/classes/html.php',
    'Gasoline\\Lang'            => GASPATH . '/classes/lang.php',
    'Gasoline\\Str'             => GASPATH . '/classes/str.php',
    'Gasoline\\Validation'      => GASPATH . '/classes/validation.php',
    'Gasoline\\ViewModel'       => GASPATH . '/classes/viewmodel.php',
    
    'Gasoline\\Model\\Base'     => GASPATH . '/classes/model/base.php',
    
    'Gasoline\\Model\\Auth_Group'               => GASPATH . '/classes/model/auth/group.php',
    'Gasoline\\Model\\Auth_Group_Permission'    => GASPATH . '/classes/model/auth/group/permission.php',
    'Gasoline\\Model\\Auth_Metadata'            => GASPATH . '/classes/model/auth/metadata.php',
    'Gasoline\\Model\\Auth_Permission'          => GASPATH . '/classes/model/auth/permission.php',
    'Gasoline\\Model\\Auth_Role'                => GASPATH . '/classes/model/auth/role.php',
    'Gasoline\\Model\\Auth_Role_Permission'     => GASPATH . '/classes/model/auth/role/permission.php',
    'Gasoline\\Model\\Auth_User'                => GASPATH . '/classes/model/auth/user.php',
    'Gasoline\\Model\\Auth_User_Permission'     => GASPATH . '/classes/model/auth/user/permission.php',
    
    'Gasoline\\Orm\\Observer_Slug'  => GASPATH . '/classes/orm/observer/slug.php',
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
