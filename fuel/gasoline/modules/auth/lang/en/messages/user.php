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
            'heading'   => 'User created!',
            'message'   => 'Successfully created user :username.',
        ),
        'failure'   => array(
            'heading'   => 'Creation failed!',
            'message'   => 'There was an unexpected error creating the user.',
        ),
    ),
    
    'update'    => array(
        'success'   => array(
            'heading'   => 'User updated!',
            'message'   => 'Successfully updated user :username.',
        ),
        'failure'   => array(
            'heading'   => 'Updating failed!',
            'message'   => 'There was an unexpected error updating the user.',
        ),
    ),
    
    'delete'    => array(
        'success' => array(
            'heading'   => 'User deleted!',
            'message'   => 'Successfully deleted user :username.',
        ),
        'failure'   => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting user :username.',
        ),
        'unconfirmed' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'User :username was not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'delete_batch'  => array(
        'success' => array(
            'heading'   => 'User(s) delete!',
            'message'   => 'Successfully deleted the following users: :usernames.',
        ),
        'failure'   => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting the following users: :usernames.',
        ),
        'unconfirmed' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Users :usernames were not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'validation_failed'   => array(
        'heading'   => 'Validation failed!',
        'message'   => 'Submitted data could not be validated successfully. Please see the form for more information.',
    ),
    
);

/* End of file user.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/messages/user.php */
