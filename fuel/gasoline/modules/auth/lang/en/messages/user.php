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
            'heading'   => 'User created!',
            'message'   => 'Successfully created user :username.',
        ),
        'udpate'    => array(
            'heading'   => 'User updated!',
            'message'   => 'Successfully updated user :username.',
        ),
        'delete'    => array(
            'heading'   => 'User deleted!',
            'message'   => 'Successfully deleted user :username.',
        ),
    ),
    
    'failure'   => array(
        'create'    => array(
            'heading'   => 'Creation failed!',
            'message'   => 'There was an unexpected error creating the user.',
        ),
        'udpate'    => array(
            'heading'   => 'Updating failed!',
            'message'   => 'There was an unexpected error updating the user.',
        ),
        'delete'    => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting the user.',
        ),
    ),
    
    'warning'   => array(
        'delete'    => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'User :username was not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'validation_failed'   => array(
        'heading'   => 'Validation failed!',
        'message'   => 'Submitted data could not be validated successfully. Please see the form for more information.',
    ),
    
);

/* End of file user.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/messages/user.php */
