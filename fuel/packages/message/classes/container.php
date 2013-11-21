<?php namespace Message;

class Container {
    
    /**
     * Keeps all container instances
     * 
     * @static
     * @access  protected
     * @var     array
     */
    protected static $containers = array();
    
    /**
     * Keeps the current instance
     * 
     * @static
     * @access  protected
     * @var     \Message\Container
     */
    protected static $active = null;
    
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
     * Forge a new container instance
     * 
     * @static
     * @access  public
     * 
     * @return  Message\Container_Instance
     */
    public static function forge($name = '_default_')
    {
        return new Container_Instance($name);
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
        if ( ! isset(static::$containers[$name]) )
        {
            static::$containers[$name] = static::forge($name);
        }
        
        if ( static::$active = null )
        {
            static::$active = static::$containers[$name];
        }
        
        return static::$containers[$name];
    }
    
    
    public static function container($name = '_default_')
    {
        return static::instance($name);
    }
    
    
    /**
     * Accessor method to the storage driver
     * 
     * @static
     * @access  public
     * 
     * @return  \Fuel\Core\Session
     */
    public static function storage()
    {
        return static::$storage;
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
        if ( count(static::$containers) )
        {
            foreach ( static::$containers as &$container )
            {
                $container->shutdown();
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
    public static function __callStatic($method, $arguments)
    {
        return call_fuel_func_array(array(static::instance(), $method), $arguments);
    }
    
}
