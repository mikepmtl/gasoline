<?php namespace Message;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline\Message
 * @version     1.0-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

use \ArrayAccess;

class Container extends \Gasoline\DataContainer implements ArrayAccess {
    
    /**
     * Keeps all container instances
     * 
     * @static
     * @access  protected
     * @var     array
     */
    protected static $instances = array();
    
    /**
     * Keeps the current instance
     * 
     * @static
     * @access  protected
     * @var     \Message\Container
     */
    protected static $instance = null;
    
    
    /**
     * Keeps the message storage driver (a session driver)
     * 
     * @static
     * @access  protected
     * @var     \Fuel\Core\Session
     */
    protected static $storage = null;
    
    
    
    
    
    /**
     * Class initialization method
     * 
     * @static
     * @access  public
     * 
     * @return  void
     */
    public static function _init()
    {
        // Load the config
        \Config::load('message', true);
        
        // Create a new storage driver for the messages
        try
        {
            static::$storage = \Session::forge(\Config::get('message.storage', array(
                'driver'    => 'cookie',
                'cookie'    => array(
                    'cookie_name' => 'msg',
                ),
                'encrypt_cookie'            => true,
                'expire_on_close'           => true,
                'flash_auto_expire'         => true,
                'flash_expire_after_get'    => true,
                'expiration_time'           => 60*60*24*1,
            )));
        }
        catch ( \Exception $e )
        {
            throw new \RuntimeException('Could not forge a storage driver for package [message]');
        }
        
        // And since we allow using flash messages that are available on the
        //  next request, we will register a 'shutdown' event to store the
        //  messages after request was processed but before it's send to the
        //  browser
        \Event::register('shutdown', 'Message\\Container::shutdown_event');
    }
    
    
    /**
     * Return the named instance or forge a new one if it doesn't exist
     * 
     * @static
     * @access  public
     * @param   string  $name   Name of the instance to grab or forge
     * 
     * @return \Message\Container
     */
    public static function instance($name = '_default_')
    {
        if ( ! isset(static::$instances[$name]) )
        {
            static::$instances[$name] = static::forge($name);
        }
        
        return static::$instance =& static::$instances[$name];
    }
    
    
    /**
     * Get the active message instance
     * 
     * @static
     * @access  public
     * 
     * @return  \Message\Container
     */
    public static function active()
    {
        return static::$instance;
    }
    
    
    /**
     * Forge a new instance and store it as the default instance
     * 
     * @static
     * @access  public
     * 
     * @return  Message\Container
     */
    public static function forge($name = '_default_')
    {
        return static::$instance = new static($name);
    }
    
    
    /**
     * Static shutdown that loops through all containers and tells them to shut down
     * 
     * @static
     * @access  public
     * 
     * @return  void
     */
    public static function shutdown_event()
    {
        if ( count(static::$instances) )
        {
            foreach ( static::$instances as &$instance )
            {
                $instance->shutdown();
            }
        }
    }
    
    
    /**
     * Magic __callStatic to call all methods of the container on the current
     *  instance if now particular instance is requested
     * 
     * @static
     * @access  public
     * @param   string  $name       The name of the method to call
     * @param   array   $arguments  Array of arguments to pass to method call
     * 
     * @return  mixed               Result of the method call
     */
    public static function __callStatic($name, $arguments)
    {
        if ( ! ( $active = static::active() ) )
        {
            throw new \RuntimeException('No default message instance found to call [' . $name . '] on.');
        }
        
        return call_fuel_func_array(array($active, $name), $arguments);
    }
    
    
    /**
     * The name of the container
     * 
     * Since every container knows just about the messages of itself, the object
     * must also know about its name
     * 
     * @access  protected
     * @var     string
     */
    protected $name;
    
    /**
     * Storage for regular messages and flash messages read on construct
     * 
     * @access  protected
     * @var     array
     */
    protected $messages = array();
    
    /**
     * Keeps new flash messages that will be stored on shutdown
     * 
     * @access  protected
     * @var     array
     */
    protected $flash = array();
    
    
    /**
     * The class constructor
     * 
     * @access  public
     * @param   string  $name       Name of the container (the same as used on ::forge())
     * @param   array   $data       Initial set of items to set
     * @param   boolean $readonly   Whether the container is read-only or not
     * 
     * @return  void
     */
    public function __construct($name, array $data = array(), $readonly = false)
    {
        // Set a unique name so we don't override another container's messages
        $this->name = $name;
        
        // Get the items from our storage driver
        $flash = static::$storage->get_flash($this->name, array());
        
        // Loop over all flash messages
        foreach ( $flash as $message )
        {
            // And convert them to message item objects, with these defaults
            $message = \Arr::merge(array('type' => null, 'message' => null, 'heading' => null, 'attributes' => array()), $message);
            if ( $message['type'] && $messsage['message'] )
            {
                $this->messages[] = new \Message\Item($message['type'], $message['message'], $message['heading'], $message['attributes']);
            }
        }
        
        // Finally, call the parent's constructor with the items as the container's data
        parent::__construct($data, $readonly);
    }
    
    /**
     * Render the container by rendering all messages one by one
     * 
     * @access  public
     * @param   string  $tag        A tag to wrap the result in. Only applicable
     *                              if theme support is disabled
     * @param   array  $attributes  Attributes to set on the tag
     * 
     * @return  string              Returns parsed string of messages
     */
    public function render($tag = null, $attributes = array())
    {
        // Get a theme instance
        $theme = \Theme::instance();
        
        // That'll keep the parsed messages
        $parsed = '';
        
        // Merge the already set attributes with the provided attributes
        $attributes = \Arr::merge($this->get_data(), $attributes);
        
        // Get the messages of the given type
        if ( $this->messages )
        {
            // Use views?
            if ( \Config::get('message.render_html', false) === false )
            {
                // Then pass messages to the theme to find the view
                $parsed = $theme->view(
                    \Config::get('message.view_file', '_templates/messages'),
                    array(
                        'messages'      => $this->messages,
                        'attributes'    => $attributes,
                    ),
                    false
                );
            }
            // No views, pure HTML! 4tw
            else
            {
                // Html to use for the container and message
                $html_container = \Config::get('message.html.container', '<div id="messages">:messages</div>');
                $html_item      = \Config::get('message.html.item', '<div class="message :type">{<h5>:heading</h5>}<p>:message</p></div>');
                
                // Loop over all messages
                foreach ( $this->messages as $message )
                {
                    // We will do some regex to see if the heading is requested
                    // in the message HTML. If so, get the tags and replace just
                    // :heading with the heading of the message
                    $msg    = preg_replace('/{(.*)?(:heading)(.*)?}(.*)/', ( ( $heading = $message->get_heading() ) ? '$1' . $heading . '$3$4' : '$4' ), $html_item);
                    
                    // And parse the message
                    $parsed .= str_replace(array(':message', ':type'), array($message->get_message(), $message->get_type()), $msg);
                }
                
                // Use html_container to parse the type and all messages
                $parsed = str_replace(':messages', $parsed, $html_container);
            }
            
            // And unset the messages that we have just parsed
            \Config::get('message.unset_after_render', true) === true && $this->messages = array();
        }
        
        return $tag ? html_tag($tag, $attributes, $parsed) : $parsed;
    }
    
    
    /**
     * Shutdown event that will store all messages
     * 
     * @access  public
     * @return  void
     */
    public function shutdown()
    {
        if ( ! static::$storage->get_flash($this->name, false) )
        {
            $to_store = array();
            
            foreach ( $this->flash as $item )
            {
                $to_store[] = $item->to_array();
            }
            
            static::$storage->set_flash($this->name, $to_store);
        }
    }
    
    
    /**
     * Setter to set either container attributes or messages
     * 
     * Messages can be added like to $container[] = \Message\Item::forge() or
     * $container[] = $message_item
     * 
     * @access  public
     * @param   mixed   $key    The key to set the property to
     * @param   mixed   $value  Value for $key to set to. If it's an instance of
     *                          \Message\Item, it will automatically push the message
     *                          onto the stack of messages
     * 
     * @return  \Message\Container
     */
    public function set($key, $value = null)
    {
        // Pushing a message item to the container?
        if ( $value instanceof Item )
        {
            // Flash messages are stored somewhere else
            if ( $value->is_flash() )
            {
                $this->flash[] =& $value;
            }
            // than non-flash messages
            else
            {
                $this->messages[] =& $value;
            }
            
            return $this;
        }
        // No, just something else
        else
        {
            return parent::set($key, $value);
        }
    }
    
    /**
     * To magically echo the container!
     * 
     * @access  public
     * @return  string              Returns parsed string of messages
     */
    public function __toString()
    {
        return $this->render();
    }
    
}

/* End of file container.php */
/* Location: ./fuel/packages/message/classes/container.php */
