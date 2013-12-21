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
            'heading'   => 'Module created!',
            'message'   => 'Successfully created module :name.',
        ),
        'failure'   => array(
            'heading'   => 'Creation failed!',
            'message'   => 'There was an unexpected error creating the module.',
        ),
    ),
    
    'update'    => array(
        'success'   => array(
            'heading'   => 'Module updated!',
            'message'   => 'Successfully updated module :name.',
        ),
        'failure'   => array(
            'heading'   => 'Updating failed!',
            'message'   => 'There was an unexpected error updating module :name.',
        ),
    ),
    
    'enable'    => array(
        'success'   => array(
            'heading'   => 'Module enabled!',
            'message'   => 'Successfully enabled module :name.',
        ),
        'failure'   => array(
            'heading'   => 'Enabling failed!',
            'message'   => 'There was an unexpected error enabling module :name.',
        ),
    ),
    
    'disable'    => array(
        'success'   => array(
            'heading'   => 'Module disabled!',
            'message'   => 'Successfully disabled module :name.',
        ),
        'failure'   => array(
            'heading'   => 'Disabling failed!',
            'message'   => 'There was an unexpected error disabling module :name.',
        ),
    ),
    
    'delete'    => array(
        'success' => array(
            'heading'   => 'Module deleted!',
            'message'   => 'Successfully deleted module :name.',
        ),
        'failure'   => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting module :name.',
        ),
        'unconfirmed' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Module :name was not deleted because deleting hasn\'t been confirmed.',
        ),
        'enabled' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Module :name was not deleted because it is still enabled. Disable module before deleting it.',
        ),
        'protected' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Module :name was not deleted because it is protected. Protected modules cannot be deleted.',
        ),
    ),
    
    'delete_batch'  => array(
        'success' => array(
            'heading'   => 'Module(s) deleted!',
            'message'   => 'Successfully deleted the following modules: :names.',
        ),
        'failure'   => array(
            'heading'   => 'Deleting failed!',
            'message'   => 'There was an unexpected error deleting the following modules: :names.',
        ),
        'unconfirmed' => array(
            'heading'   => 'Deleting not performed!',
            'message'   => 'Modules :names were not deleted because deleting hasn\'t been confirmed.',
        ),
    ),
    
    'validation_failed'   => array(
        'heading'   => 'Validation failed!',
        'message'   => 'Submitted data could not be validated successfully. Please see the form for more information.',
    ),
    
);

/* End of file messages.php */
/* Location: ./fuel/gasoline/modules/modules/lang/en/messages.php */
