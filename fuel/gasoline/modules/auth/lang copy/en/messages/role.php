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
    
    'success'   => array(
        'create'    => array(
            'heading'   => 'Role created!',
            'message'   => 'Successfully created role :name.',
        ),
        'udpate'    => array(
            'heading'   => 'Role updated!',
            'message'   => 'Successfully updated role :name.',
        ),
        'delete_single'    => array(
            'heading'   => 'Role deleted!',
            'message'   => 'Successfully deleted role :name.',
        ),
        'delete_batch'    => array(
            'heading'   => 'Roles delete!',
            'message'   => 'Successfully deleted the following roles: :names.',
        ),
    ),
    
    'failure'   => array(
        'create'    => array(
            'heading'   => 'Creation failed!',
            'message'   => 'There was an unexpected error creating the role.',
        ),
        'udpate'    => array(
            'heading'   => 'Updating failed!',
            'message'   => 'There was an unexpected error updating the role.',
        ),
        'delete_single'    => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting role :name.',
        ),
        'delete_batch'    => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting the following roles: :names.',
        ),
    ),
    
    'warning'   => array(
        'delete'    => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Role :name was not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'validation_failed'   => array(
        'heading'   => 'Validation failed!',
        'message'   => 'Submitted data could not be validated successfully. Please see the form for more information.',
    ),
    
);

/* End of file role.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/messages/role.php */
