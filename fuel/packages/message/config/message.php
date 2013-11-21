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

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade gasoline without losing your custom config.
 */

return array(
    
    /**
     * Storage configuration to use for the message package. Can be any valid
     * configuration for \Fuel\Core\Session
     */
    'storage'   => array(
        'driver'    => 'cookie',
        'cookie'    => array(
            'cookie_name' => 'msg',
        ),
        'encrypt_cookie'            => true,
        'expire_on_close'           => true,
        'flash_auto_expire'         => true,
        'flash_expire_after_get'    => true,
        'expiration_time'           => 60*60*24*1,
    ),
    
    /**
     * Whether to render the messages directly as HTML (set to true) or whether
     * views shall be used for rendering (set to false)
     */
    'render_html' => false,
    
    /**
     * Configuration for html rendering
     */
    'html' => array(
        /**
         * Wrapper for the container or none if omitted. Should contain placeholder
         * ":messages" otherwise messages cannot be displayed
         */
        'container' => '<div id="messages">:messages</div>',
        
        /**
         * Wrapper for a message item. Should contain placeholder ":message", can
         * contain placeholder ":type" and might contain placeholder ":heading".
         * If, like in the default configuration, you wrap the placeholder for the
         * heading in { } (curly brackets) it will not be rendered if there's no
         * heading
         */
        'item'   => '<div class="message :type">{<h5>:heading</h5>}<p>:message</p></div>',
    ),
    
    /**
     * Path to where the view is that renders the messages. Must be relative to
     * APPPATH/views or themes/<theme> respectively
     */
    'view_file' => '_templates/messages',
    
    /**
     * Whether to unset messages after they have rendered
     */
    'unset_after_render' => false,
);

/* End of file message.php */
/* Location: ./fuel/packages/message/config/message.php */
