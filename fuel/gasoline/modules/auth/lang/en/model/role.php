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
    'name'          => 'Name',
    'slug'          => 'Slug',
    'filter'        => 'Permissions filter',
    'user_id'       => 'Updated by',
    'created_at'    => 'Created',
    'updated_at'    => 'Updated',
    
    'options'   => array(
        'filter'    => array(
            ''  => 'None',
            'A' => 'Allow all access',
            'D' => 'Deny all access',
            'R' => 'Revoke assigned permissions',
        ),
    ),
    
    'help'  => array(
        'name'      => 'A unique name for a role',
        'filter'    => 'Each role can have a filter applied which will be checked before checking the role\'s permissions',
    ),
);

/* End of file role.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/auth/model/role.php */
