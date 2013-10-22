<?php namespace Message;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline\Message
 * @version     0.1-dev
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
                // 'driver'    => 'db',
                'cookie'    => array(
                    'cookie_name' => 'msg', # \Config::get('message.cookie_name', 'msg'),
                ),
                // 'db'        => array(
                //     'cookie_name'   => 'msg',
                //     'table'         => 'sessions',
                // ),
                'encrypt_cookie'            => true,
                'expire_on_close'           => true,
                'flash_auto_expire'         => true,
                'flash_expire_after_get'    => true,
                'expiration_time'           => \Config::get('message.expiration', 60*60*24*1),
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
        $items = static::$storage->get_flash($this->name, array());
        $messages = array();
        
        foreach ( $items as $type => $items )
        {
            isset($messages[$type]) OR $messages[$type] = array();
            
            foreach ( $items as $item )
            {
                $item = \Arr::merge(array('type' => null, 'message' => null, 'heading' => null, 'attributes' => array()), $item);
                
                if ( $item['type'] && $item['message'] )
                {
                    $messages[$type][] = new \Message\Item($item['type'], $item['message'], $item['heading'], $item['attributes']);
                }
            }
        }
        
        // And call the parent's constructor with the items as the container's data
        parent::__construct($messages, $readonly);
    }
    
    
    public function render($type, $tag = null, $attributes = array())
    {
        // Don't allow rendering flash messages
        if ( $type == 'flash' )
        {
            logger(\Fuel::L_DEBUG, 'Message container will not render flash messages');
            
            return '';
        }
        
        // Get a theme instance
        try
        {
            $theme = \Theme::instance();
        }
        // Or fail...
        catch ( \Exception $e )
        {
            // ... with a log
            logger(\Fuel::L_DEBUG, 'Message container [' . $this->name . '] was unable to get the instance of the theme class');
            
            return '';
        }
        
        // That'll keep the parsed messages
        $parsed = '';
        
        // Get the messages of the given type
        if ( $messages = $this->get($type) )
        {
            if ( \Config::get('message.use_views', true) === true )
            {
                $parsed = $theme->view(\Config::get('message.view_path', '_templates/messages/') . $type, array('messages' => $messages), false);
            }
            else
            {
                $html_container = \Config::get('message.html.container', '<div class="message :type"><dl>:messages</dl></div>');
                $html_message   = \Config::get('message.html.message', '{<dt>:heading</dt>}<dd>:message</dd>');
                
                foreach ( $messages as $message )
                {
                    // We will do some regex to see if the heading is requested
                    // in the message HTML. If so, get the tags and replace just
                    // :heading with the heading of the message
                    $msg    = preg_replace('/{(.*)?(:heading)(.*)?}(.*)/', ( ( $heading = $message->get_heading() ) ? '$1' . $heading . '$3$4' : '$4' ), $html_message);
                    
                    $parsed .= str_replace(':message', $message->get_message(), $msg);
                }
                
                $parsed = str_replace(array(':type', ':messages'), array($type, $parsed), $html_container);
            }
            
            // And unset the messages that we have just parsed
            $this->offsetUnset($type);
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
            $types = $this->get('flash');
            
            foreach ( $types as $type => &$items )
            {
                if ( ! $items )
                {
                    continue;
                }
                
                isset($to_store[$type]) OR $to_store[$type] = array();
                
                foreach ( $items as &$item )
                {
                    $to_store[$type][] = $item->to_array();
                }
            }
            
            static::$storage->set_flash($this->name, $to_store);
        }
    }
    
    
    /**
     * Set a specific item of the data
     * 
     * @access  public
     * @param   mixed   $key    The key to set the data to
     * @param   mixed   $value  The value to set
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  Datacontainer
     */
    public function offsetSet($offset, $value)
    {
        // If a message item is assigned to this container, we will have to change
        // the offset accordingly
        if ( $value instanceof Item )
        {
            // Automatically get the type of the message to set it as the base-offset
            $type = $value->get_type();
            $flash = $value->is_flash();
            
            // Offset is null? That happens with $container[] = $item
            if ( $offset === null )
            {
                // And then append the type's counter because we're using \Arr::set which
                // would otherwise overwrite previous entries if no key is set after the dot
                $offset = ( $flash ? 'flash.' : '' ) . $type . '.' . count($this->get($type));
            }
            // Otherwise the offset is given
            else
            {
                // See if the offset was set to be "flash:" or "flash:"
                if ( preg_match('/^flash:.*/', $offset) && $flash )
                {
                    $offset = preg_replace('/^flash:/', '', $offset);
                }
                
                // We want to allow overriding messages so we will only generate
                // a new offset, if the given offset does not contain a dot (meaning
                // it's not given as <type>.<counter>)
                strpos($offset, '.') === false && $offset = ( $flash ? 'flash.' : '' ) . $offset . '.' . count($this->get($type));
            }
        }
        
        // Preparation done, call the parent's set method
        return parent::set($offset, $value);
    }
    
}

/* End of file container.php */
/* Location: ./fuel/packages/message/classes/container.php */
