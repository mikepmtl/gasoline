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
    
    'create'    => array(
        'success'   => array(
            'heading'   => 'Role created!',
            'message'   => 'Successfully created role :name.',
        ),
        'failure'   => array(
            'heading'   => 'Creation failed!',
            'message'   => 'There was an unexpected error creating the role.',
        ),
    ),
    
    'update'    => array(
        'success'   => array(
            'heading'   => 'Role updated!',
            'message'   => 'Successfully updated role :name.',
        ),
        'failure'   => array(
            'heading'   => 'Updating failed!',
            'message'   => 'There was an unexpected error updating the role.',
        ),
    ),
    
    'delete'    => array(
        'success' => array(
            'heading'   => 'Role deleted!',
            'message'   => 'Successfully deleted role :name.',
        ),
        'failure'   => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting role :name.',
        ),
        'unconfirmed' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Role :name was not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'delete_batch'  => array(
        'success' => array(
            'heading'   => 'Role(s) delete!',
            'message'   => 'Successfully deleted the following roles: :names.',
        ),
        'failure'   => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting the following roles: :names.',
        ),
        'unconfirmed' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Roles :names were not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'validation_failed'   => array(
        'heading'   => 'Validation failed!',
        'message'   => 'Submitted data could not be validated successfully. Please see the form for more information.',
    ),
    
);

/* End of file role.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/messages/role.php */
