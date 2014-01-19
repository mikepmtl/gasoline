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
    'auth' => array(
        'admin:users' => array(
            'list'      => 'List ',
            'create'    => 'Create new user',
            'read'      => 'Show details of a user',
            'update'    => 'Update a user',
            'delete'    => 'Delete a user',
        ),
        'admin:groups' => array(
            'list'      => 'List ',
            'create'    => 'Create new group',
            'read'      => 'Show details of a group',
            'update'    => 'Update a group',
            'delete'    => 'Delete a group',
        ),
        'admin:roles'   => array(
            'list'      => 'List ',
            'create'    => 'Create new role',
            'read'      => 'Show details of a role',
            'update'    => 'Update a role',
            'delete'    => 'Delete a role',
        ),
        'admin:permissions' => array(
            'user'          => 'Manger any user permissions',
            'user:create'   => 'Allow user access',
            'user:update'   => 'Change user access',
            'user:delete'   => 'Withdraw user access',
            
            'group'         => 'Manage any group permissions',
            'group:create'  => 'Allow group access',
            'group:update'  => 'Change group access',
            'group:delete'  => 'Withdraw group access',
            
            'role'          => 'Manage any role permissions',
            'role:create'   => 'Allow role access',
            'role:update'   => 'Change role access',
            'role:delete'   => 'Withdraw role access',
        ),
    ),
);

/* End of file permissions.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/permissions.php */
