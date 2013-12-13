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
    'id'            => 'Module ID',
    'name'          => 'Name',
    'slug'          => 'Slug',
    'author'        => 'Author',
    'website'       => 'Website',
    'version'       => 'Version',
    'status'        => 'Status',
    'scope'         => 'Scope',
    'protected'     => 'Protected',
    'user_id'       => 'Auditor',
    'created_at'    => 'Created',
    'updated_at'    => 'Updated',
    
    /**
     * Options for some properties
     */
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
    
    /**
     * Custom data
     */
    'description'   => array(
        'short' => 'Summary',
        'long'  => 'Description'
    ),
    
    /**
     * Form help
     */
    'help' => array(
        'name'      => 'Name of the module. Must be a unique name',
        'slug'      => 'Slug of the module which is both the folder name as well as the trigger in the URI to point to the module. Must be the same as the folder of the module and must be unique',
        'author'    => 'Author of the module. If defined like so "Author <email@example.com>" the email will be parsed a link automatically',
        'website'   => 'Website where there is more information on the module',
        'version'   => 'Installed version of the module',
        'status'    => 'Status of the module can be either disabled or enabled. Disabled are not publicly accessible but classes can be internally used',
        'protected' => 'Protection status of the module. Protected modules cannot be deleted via the web interface',
    ),
);

/* End of file module.php */
/* Location: ./fuel/gasoline/modules/modules/lang/en/model/module.php */
