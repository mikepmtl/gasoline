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

return array(
    'users' => array(
        'admin' => array(
            'create'    => 'Create users',
            'read'      => 'Show a user\'s  details',
            'update'    => 'Update user',
            'delete'    => 'Delete user',
        ),
    ),
    
    'roles' => array(
        'admin' => array(
            'create'    => 'Create roles',
            'read'      => 'Show a role\'s details',
            'update'    => 'Update role',
            'delete'    => 'Delete role',
        ),
    ),
    
    'groups' => array(
        'admin' => array(
            'create'    => 'Create Group',
            'read'      => 'Show a group\'s details',
            'update'    => 'Update group',
            'delete'    => 'Delete group',
        ),
    ),
    
    'permissions' => array(
        'admin' => array(
            'create'        => 'Set permission',
            'read'          => 'Show any permission',
            'delete'        => 'Withdraw permission',
            'group_create'  => 'Set any group\'s permissions',
            'user_create'   => 'Set any user\'s permissions',
            'role_create'   => 'Set any role\'s permissions',
            'group_read'    => 'Show any group\'s permissions',
            'user_read'     => 'Show any user\'s permissions',
            'role_read'     => 'Show any role\'s permissions',
            'group_delete'  => 'Withdraw any group\'s permissions',
            'user_delete'   => 'Withdraw any user\'s permissions',
            'role_delete'   => 'Withdraw any role\'s permissions',
        ),
    ),
);

/* End of file permissions.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/permissions.php */
