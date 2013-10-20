<?php

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(
    
    /**
     * General configuration of your app
     */
    'general'   => array(
        'formats' => array(
            'date'  => array(
                'short'     => '',
                'long'      => '',
            ),
            
            /**
             * Default timeformat for the app. Can be any valid format that works
             * for \Fuel\Core\Date::format
             */
            'time' => '%H:%M',
        ),
        
        /**
         * Section for links from, to, and on your app
         */
        'links' => array(
            /**
             * Create external links with rel="nofollow". This will only be applied
             * if no previous rel was set on the link
             * 
             * @var boolean
             */
            'external_no_follow' => true,
        ),
    ),
    
    /**
     * Configuration related to authentication and authorization
     */
    'auth'  => array(
        /**
         * Set the status of registration here. It can be either a boolean of
         * true of false meaning that the registration is allowed or not allowed,
         * respectively. Otherwise, can also be an integer, with 0 or 1 having the
         * same meaning as false or true, respectively. Setting this to 2 means
         * that users can register but need to be activated before they can user
         * their account.
         * 
         * @var boolean
         */
        'registration' => false,
        
        /**
         * Options for password and username recovery
         */
        'recovery'   => array(
            /**
             * Method to use for password recovery. Can be either 'prompt', or
             * 'email'.
             * In both cases a password recovery link (valid until 'auth.login.recovery.within')
             * is sent to the user. With 'prompt', the user can then choose a new
             * password, with 'email' a new password will be sent to the user.
             */
            'method'    => false,
            
            /**
             * If a password recovery is requested, how long will the recovery
             * link be valid?
             * Can be either an integer in seconds that is added to time(), or
             * it's a string that can be used for strtotime().
             * 
             * @var string|integer
             */
            'within'    => '+1 week',
        ),
        
        /**
         * Options regarding login like locking an account after several unsuccessful
         * attempts, etc.
         */
        'login' => array(
            /**
             * Whether accounts will be locked after a set number of unsuccessful
             * attempts.
             * 
             * @var boolean
             */
            'lockable'  => true,
            
            /**
             * Number of attempts that can be unsuccessful, before the account is
             * logged
             * 
             * @var integer
             */
            'max_attempts'  => 5,
            
            /**
             * Unlock strategy. This can be either 'time', 'email', or 'both'. With
             * time, the configuration option 'auth.login.unlock_within', given
             * below is used to determine the earliest time possible when the login
             * count is decreased.
             * If set to 'email', an email will be sent to the user with a link to
             * unlock the account.
             * In case 'both' is chosen, an email will be sent with a link that
             * is only valid after the amount of 'auth.login.unlock_within'
             * 
             * @var string
             */
            'unlock_strategy'   => 'time',
            
            /**
             * Time, after which locked out accounts are either activated by default
             * (if 'auth.login.unlock_strategy' == 'time') or after which unlock
             * links will be valid (only applicable if 'auth.login.unlock_strategy' == ( 'email' OR 'both' ) )
             * Can be an integer to be added to time() or a string valid for strtotime()
             * 
             * @var string|integer
             */
            'unlock_within'  => '+1 day',
        ),
    ),
);

/* End of file app.php */
/* Location: ./fuel/gasoline/config/app.php */
