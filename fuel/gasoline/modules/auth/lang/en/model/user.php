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
    /**
     * Properties of the object and table fields
     */
    'id'                => 'User ID',
    'username'          => 'Username',
    'email'             => 'Email address',
    'group_id'          => 'Group',
    'password'          => 'Password',
    'last_login'        => 'Last login at',
    'previous_login'    => 'Previous login at',
    'login_hash'        => 'Login hash',
    'user_id'           => 'Updated by',
    'created_at'        => 'Created',
    'updated_at'        => 'Updated',
    
    /**
     * Relational data
     */
    'roles'             => 'Roles',
    
    /**
     * Metadata
     */
    'metadata'  => array(
        'fullname'  => 'Fullname',
        'foo'       => 'Foo',
    ),
    
    /**
     * Other data
     */
    'password_repeat'   => 'Password (repeat)',
);

/* End of file user.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/auth/model/user.php */
