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
    'name'          => 'Name',
    'slug'          => 'Slug',
    'user_id'       => 'Updater ID',
    'created_at'    => 'Created',
    'updated_at'    => 'Updated',
    
    /**
     * Relational data
     */
    'auditor'           => 'Auditor',
    'grouppermissions'  => 'Group permissions',
    'users'             => 'Users',
    'roles'             => 'Roles',
    'permissions'       => 'Permissions',
    
    /**
     * Form help lines
     */
    'help'  => array(
        'name'      => 'A unique name for the group',
        'slug'      => 'A short, search-engine friendly version of the group\'s name',
        'roles'     => 'Every group can have roles assigned to it. Choose them wisely',
    ),
);

/* End of file role.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/auth/model/role.php */
