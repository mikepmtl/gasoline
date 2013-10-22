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

class Item {
    
    public static function _init()
    {
        \Config::load('message', true);
    }
    
    
    protected $flash = false;
    
    protected $type;
    
    protected $heading;
    
    protected $message;
    
    protected $attributes = array();
    
    protected $id = null;
    
    
    
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
    
    
    
    public function get($property)
    {
        if ( ! property_exists($this, $property) )
        {
            throw new \InvalidArgumentException('Undefined property ' . $property);
        }
        
        return $this->{$property};
    }
    
    
    public function is_flash()
    {
        return $this->flash == true;
    }
    
    
    public function set($property, $value = null)
    {
        if ( ! property_exists($this, $property) )
        {
            throw new \InvalidArgumentException('Undefined property ' . $property);
        }
        
        $this->{$property} = $value;
        
        return $this;
    }
    
    
    public function to_array()
    {
        return array(
            'type'          => $this->type,
            'message'       => $this->message,
            'heading'       => $this->heading,
            'attributes'    => $this->attributes,
        );
    }
    
    
    public function render()
    {
        // Find the view of this
    }
    
    
    
    public function __call($method, $arguments)
    {
        if ( preg_match('/(?<method>(s|g)et)\_(?<property>[a-zA-Z0-9]+)/', $method, $matches) )
        {
            return $this->{$matches['method']}($matches['property']);
        }
        
        throw new \BadMethodCallException('Invalid method: ' . get_called_class() . '::' . $method);
    }
    
    
    public function __get($property)
    {
        return $this->get($property);
    }
    
    
    public function __set($property, $value)
    {
        return $this->set($property, $value);
    }
    
    
    public function __toString()
    {
        return $this->render();
    }
    
}

/* End of file item.php */
/* Location: ./fuel/packages/message/classes/item.php */
