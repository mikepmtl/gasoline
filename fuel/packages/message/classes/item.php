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

class Item {
    
    /**
     * Class initialization
     * 
     * Just to make sure the message config is set we will load it
     * 
     * @static
     * @access  public
     * 
     * @return  void
     */
    public static function _init()
    {
        \Config::load('message', true);
    }
    
    
    /**
     * Forge a new message item object
     * 
     * @static
     * @access  public
     * @param   string  $type       Message type. Mainly used on rendering
     * @param   string  $message    Actual message to display
     * @param   string  $heading    Optionally a heading (if supported by the
     *                              rendering engine [theme or plain html])
     * @param   array  $attributes  Array of attributes to set on the rendered message
     *                              item like class, id, ...
     * 
     * @return  \Message\Item       Returns message item
     */
    public static function forge($type, $message, $heading = null, $attributes = array())
    {
        return new static($type, $message, $heading, $attributes);
    }
    
    
    
    
    
    /**
     * Array of attributes to set on rendering
     * 
     * @access  protected
     * @var     array
     */
    protected $attributes = array();
    
    /**
     * Whether the message is a flash message
     * 
     * @access  protected
     * @var     boolean
     */
    protected $flash = false;
    
    /**
     * Message heading
     * 
     * @access  protected
     * @var     string
     */
    protected $heading;
    
    /**
     * Message's message
     * 
     * @access  protected
     * @var     string
     */
    protected $message;
    
    /**
     * Type of the message like info, warning, error, success
     * 
     * @access  protected
     * @var     string
     */
    protected $type;
    
    
    
    
    
    /**
     * Create a new message item
     * 
     * @static
     * @access  public
     * @param   string  $type       Message type. Mainly used on rendering. If preceeded
     *                              with "flash:" the message will be stoed
     * @param   string  $message    Actual message to display
     * @param   string  $heading    Optionally a heading (if supported by the
     *                              rendering engine [theme or plain html])
     * @param   array  $attributes  Array of attributes to set on the rendered message
     *                              item like class, id, ...
     * 
     * @return  \Message\Item       Returns message item
     */
    public function __construct($type, $message, $heading = null, $attributes = array())
    {
        // That's how we define flash items: Prepend "flash:" before the type and
        // it will automatically be set as a flash variable. Of course, flashing
        // the item can also be done by calling set_flash(true) on the item later on
        if ( preg_match('/^flash:.*$/', $type) )
        {
            // Get the actual type by removing 'flash' from the passed $type
            $type = preg_replace('/^flash:/', '', $type);
            
            // And set the item to be flash
            $this->flash = true;
        }
        
        // We allow omitting the heading parameter and replacing it with the attributes,
        // so check here for $heading being an array and shift it to the attributes
        if ( is_array($heading) )
        {
            $attributes = $heading;
            $heading = null;
        }
        
        $this->attributes   = $attributes;
        $this->heading      = $heading;
        $this->message      = $message;
        $this->type         = $type;
    }
    
    
    /**
     * Get any property of the object
     * 
     * @access  public
     * @param   string  $property   Property name to grade
     * @throws  InvalidArgumentException    If the property does not exist
     * 
     * @return  mixed               Value of the property is returned
     */
    public function get($property)
    {
        if ( ! property_exists($this, $property) )
        {
            throw new \InvalidArgumentException('Undefined property ' . $property);
        }
        
        return $this->{$property};
    }
    
    
    /**
     * Check to see if the message is flash
     * 
     * @access  public
     * 
     * @return  boolean     True if it's a flash message, otherwise false
     */
    public function is_flash()
    {
        return $this->flash == true;
    }
    
    
    /**
     * Set any of the items property
     * 
     * @access  public
     * @param   string  $property   Name of the property to set
     * @param   mixed   $value      Value to set
     * @throws  InvalidArgumentException    If the property does not exist
     * 
     * @return  \Message\Item
     */
    public function set($property, $value = null)
    {
        if ( ! property_exists($this, $property) )
        {
            throw new \InvalidArgumentException('Undefined property ' . $property);
        }
        
        $this->{$property} = $value;
        
        return $this;
    }
    
    /**
     * Converts the object to an array
     * 
     * @access  public
     * @return  array       Associative array of item's properties and their
     *                      respective values
     */
    public function to_array()
    {
        return array(
            'type'          => $this->type,
            'message'       => $this->message,
            'heading'       => $this->heading,
            'attributes'    => $this->attributes,
        );
    }
    
    
    /**
     * Magic call to magically get or set a property
     * 
     * @access  public
     * @param   string  $method     Name of method to be called
     * @param   array   $arguments  Array of arguments passed to method call
     * @throws  \BadMethodCallException     If the method does not exist
     * 
     * @return  mixed               Returns return value of the get or set method
     */
    public function __call($method, $arguments)
    {
        if ( preg_match('/(?<method>(s|g)et)\_(?<property>[a-zA-Z0-9]+)/', $method, $matches) )
        {
            return $this->{$matches['method']}($matches['property']);
        }
        
        throw new \BadMethodCallException('Invalid method: ' . get_called_class() . '::' . $method);
    }
    
    
    /**
     * Magic get
     * 
     * @access  public
     * @param   string  $property   Name of property to get
     * 
     * @return  mixed               Value of $property is returned
     */
    public function __get($property)
    {
        return $this->get($property);
    }
    
    
    /**
     * Magic set
     * 
     * @access  public
     * @param   string  $property   Name of property to set
     * @param   mixed   $value      Value to assign to property
     */
    public function __set($property, $value)
    {
        return $this->set($property, $value);
    }
    
}

/* End of file item.php */
/* Location: ./fuel/packages/message/classes/item.php */
