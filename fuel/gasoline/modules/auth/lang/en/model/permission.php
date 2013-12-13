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
    'id'            => 'ID',
    'area'          => 'Area',
    'permission'    => 'Permission',
    'actions'       => 'Actions',
    'user_id'       => 'Updated by',
    'created_at'    => 'Created',
    'updated_at'    => 'updated',
    
    /**
     * Form help lines
     */
    'help'  => array(
        'area'          => 'Area of the permission. Usually the module it is provided with',
        'permission'    => 'Effective permission name like "admin" for administration, "public" for public interactions, &hellip;',
        'actions'       => 'List of actions that are available for the created permission',
    ),
);

/* End of file permission.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/auth/model/permission.php */
