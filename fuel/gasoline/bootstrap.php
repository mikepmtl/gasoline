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
Autoloader::add_namespace('Gasoline', __DIR__ . 'classes/');

// This is needed because these classes would actually reside under namespace Gasoline
//  but since they need to be globally accessible, we add them here as classes
Autoloader::add_classes(array(
    // Classes for the auth package cannot be added with the gasoline namespace
    // as they must be within the global namespace to be accessible
    'Auth_Acl_Gasacl'       => __DIR__ . '/classes/auth/acl/gasacl.php',
    'Auth_Group_Gasgroup'   => __DIR__ . '/classes/auth/group/gasgroup.php',
    'Auth_Login_Gasauth'    => __DIR__ . '/classes/auth/login/gasauth.php',
    
    'Gasoline\\Asset_Instance'  => __DIR__ . '/classes/asset/instance.php',
    'Gasoline\\DataContainer'   => __DIR__ . '/classes/datacontainer.php',
    'Gasoline\\Helpers'         => __DIR__ . '/classes/helpers.php',
    'Gasoline\\Html'            => __DIR__ . '/classes/html.php',
    'Gasoline\\Lang'            => __DIR__ . '/classes/lang.php',
    'Gasoline\\Str'             => __DIR__ . '/classes/str.php',
    'Gasoline\\Validation'      => __DIR__ . '/classes/validation.php',
    'Gasoline\\View'            => __DIR__ . '/classes/view.php',
    'Gasoline\\ViewModel'       => __DIR__ . '/classes/viewmodel.php',
    
    'Gasoline\\Controller\\Admin'           => __DIR__ . '/classes/controller/admin.php',
    'Gasoline\\Controller\\Authenticated'   => __DIR__ . '/classes/controller/authenticated.php',
    'Gasoline\\Controller\\Base'            => __DIR__ . '/classes/controller/base.php',
    'Gasoline\\Controller\\Public'          => __DIR__ . '/classes/controller/public.php',
    'Gasoline\\Controller\\Widget'          => __DIR__ . '/classes/controller/widget.php',
    
    'Gasoline\\Model\\Base'     => __DIR__ . '/classes/model/base.php',
    
    // 'Gasoline\\Model\\Auth_Group'               => __DIR__ . '/classes/model/auth/group.php',
    // 'Gasoline\\Model\\Auth_Group_Permission'    => __DIR__ . '/classes/model/auth/group/permission.php',
    // 'Gasoline\\Model\\Auth_Metadata'            => __DIR__ . '/classes/model/auth/metadata.php',
    // 'Gasoline\\Model\\Auth_Permission'          => __DIR__ . '/classes/model/auth/permission.php',
    // 'Gasoline\\Model\\Auth_Role'                => __DIR__ . '/classes/model/auth/role.php',
    // 'Gasoline\\Model\\Auth_Role_Permission'     => __DIR__ . '/classes/model/auth/role/permission.php',
    // 'Gasoline\\Model\\Auth_User'                => __DIR__ . '/classes/model/auth/user.php',
    // 'Gasoline\\Model\\Auth_User_Permission'     => __DIR__ . '/classes/model/auth/user/permission.php',
    
    'Gasoline\\Orm\\Interface_Delete'   => __DIR__ . '/classes/orm/interface/delete.php',
    'Gasoline\\Orm\\Interface_Protect'  => __DIR__ . '/classes/orm/interface/protected.php',
    'Gasoline\\Orm\\Interface_State'    => __DIR__ . '/classes/orm/interface/stable.php',
    
    'Gasoline\\Orm\\Observer_Auditor'   => __DIR__ . '/classes/orm/observer/auditor.php',
    'Gasoline\\Orm\\Observer_Id'        => __DIR__ . '/classes/orm/observer/id.php',
    'Gasoline\\Orm\\Observer_Slug'      => __DIR__ . '/classes/orm/observer/slug.php',
));

// We also need to add the __DIR__ to the finder-instance so we can load config
//  and other stuff from there
Finder::instance()->add_path(__DIR__, 1);

// Register a function to the 'shutdown'-event to store the currently accessed page
//  to a session-variable (for redirecting back and forth). This will only happen,
//  if there's been a change between pages so we won't set the last_page to the
//  current page if the user hits reload
Event::register('shutdown', function() {
    if ( ! Request::is_hmvc() )
    {
        Uri::current() == Session::get('last_page') OR Session::set('last_page', Uri::current());
    }
});

/* End of file bootstrap.php */
/* Location: ./fuel/gasoline/bootstrap.php */
