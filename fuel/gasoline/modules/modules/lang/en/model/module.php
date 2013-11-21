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
    'id'            => 'Module ID',
    'name'          => 'Name',
    'slug'          => 'Slug',
    'author'        => 'Author',
    'version'       => 'Version',
    'status'        => 'Status',
    'scope'         => 'Scope',
    'protected'     => 'Protected',
    'user_id'       => 'Auditor',
    'created_at'    => 'Created',
    'updated_at'    => 'Updated',
    
    'options'   => array(
        'status'   => array(
            0   => 'Disabled',
            1   => 'Enabled',
        ),
        'scope' => array(
            0 => 'Internal',
            1 => 'Public only',
            2 => 'Admin only',
            3 => 'Public and Admin',
        ),
        'protected' => array(
            0 => 'Unprotected',
            1 => 'Protected',
        ),
    ),
    
    'description'   => array(
        'short' => 'Summary',
        'long'  => 'Description'
    ),
);

/* End of file module.php */
/* Location: ./fuel/gasoline/modules/modules/lang/en/model/module.php */
