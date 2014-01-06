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
        'admin' => array(
            'users[list]'   => 'List ',
            'users[create]' => 'Create new user',
            'users[read]'   => 'Show details of a user',
            'users[update]' => 'Update a user',
            'users[delete]' => 'Delete a user',
            
            
            'roles[list]'   => 'List ',
            'roles[create]' => 'Create new role',
            'roles[read]'   => 'Show details of a role',
            'roles[update]' => 'Update a role',
            'roles[delete]' => 'Delete a role',
            
            
            'groups[list]'      => 'List ',
            'groups[create]'    => 'Create new group',
            'groups[read]'      => 'Show details of a group',
            'groups[update]'    => 'Update a group',
            'groups[delete]'    => 'Delete a group',
            
            
            'permissions[user]'             => 'Manger any user\'s permissions',
            'permissions[user[create]]'     => 'Allow permission for a user',
            'permissions[user[update]]'     => 'Change permissions for a user',
            'permissions[user[delete]]'     => 'Withdraw permissions for auser',
            
            'permissions[roles]'            => 'Manage any role\'s permissions',
            'permissions[roles[create]]'    => 'Allow permission for a role',
            'permissions[roles[update]]'    => 'Change permissions for a role',
            'permissions[roles[delete]]'    => 'Withdraw permissions for arole',
            
            'permissions[groups]'           => 'Manage any group\'s permissions',
            'permissions[groups[create]]'   => 'Allow permission for a group',
            'permissions[groups[update]]'   => 'Change permissions for a group',
            'permissions[groups[delete]]'   => 'Withdraw permissions for agroup',
        ),
    ),
);

/* End of file permissions.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/permissions.php */
