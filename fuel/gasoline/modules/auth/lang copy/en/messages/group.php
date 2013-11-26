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
            'heading'   => 'Group created!',
            'message'   => 'Successfully created group :name.',
        ),
        'udpate'    => array(
            'heading'   => 'Group updated!',
            'message'   => 'Successfully updated group :name.',
        ),
        'delete_single'    => array(
            'heading'   => 'Group deleted!',
            'message'   => 'Successfully deleted group :name.',
        ),
        'delete_batch'    => array(
            'heading'   => 'Groups delete!',
            'message'   => 'Successfully deleted the following groups: :names.',
        ),
    ),
    
    'failure'   => array(
        'create'    => array(
            'heading'   => 'Creation failed!',
            'message'   => 'There was an unexpected error creating the group.',
        ),
        'udpate'    => array(
            'heading'   => 'Updating failed!',
            'message'   => 'There was an unexpected error updating the group.',
        ),
        'delete_single'    => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting group :name.',
        ),
        'delete_batch'    => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting the following groups: :names.',
        ),
    ),
    
    'warning'   => array(
        'delete'    => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Group :name was not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'validation_failed'   => array(
        'heading'   => 'Validation failed!',
        'message'   => 'Submitted data could not be validated successfully. Please see the form for more information.',
    ),
    
);

/* End of file group.php */
/* Location: ./fuel/gasoline/modules/auth/lang/en/messages/group.php */
