<?php

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */



/**
 * Format:
 * area =>
 *     permission => 
 *         action_1 => Human readable 1,
 *         action_2 => Human readable 2
 */
return array(
    'users' => array(
        'admin' => array(
            'create'    => 'Create user',
            'read'      => 'Show user infos',
            'update'    => 'Update user',
            'delete'    => 'Delete user',
            'list'      => 'List users',
        ),
    ),
    
    'roles' => array(
        'admin' => array(
            'create'    => 'Create role',
            'read'      => 'Show role infos',
            'update'    => 'Update role',
            'delete'    => 'Delete role',
            'list'      => 'List roles',
        ),
    ),
    
    'groups' => array(
        'admin' => array(
            'create'    => 'Create group',
            'read'      => 'Show group infos',
            'update'    => 'Update group',
            'delete'    => 'Delete group',
            'list'      => 'List groups',
        ),
    ),
    
    'permissions' => array(
        'admin' => array(
            'user'          => 'Manage user permissions',
            'user_create'   => 'Allow permission for user',
            'user_update'   => 'Change permissions of user',
            'user_delete'   => 'Withdraw permission of user',
            
            'role'          => 'Manage role permissions',
            'role_create'   => 'Allow permission for role',
            'role_update'   => 'Change permissions of role',
            'role_delete'   => 'Withdraw permission of role',
            
            'group'         => 'Manage group permissions',
            'group_create'  => 'Allow permission for group',
            'group_update'  => 'Change permissions of group',
            'group_delete'  => 'Withdraw permission of group',
        ),
    ),
);

/* End of file permissions.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/permissions.php */
