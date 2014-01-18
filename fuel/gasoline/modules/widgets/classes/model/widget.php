<?php namespace Widgets\Model;

class Widget {
    
    
    protected static $_widgets_cached = array();
    
    public static function forge($data = array())
    {
        return new static($data);
    }
    
    
    
    
    protected $_data = array();
    
    protected $_custom_data = array();
    
    
    public function __construct($data = array())
    {
        $properties = $this->properties();
        
        foreach ( $properties as $prop => $settings )
        {
            if ( array_key_exists($prop, $data) )
            {
                $this->_data[$prop] = $data[$prop];
                unset($data[$prop]);
            }
        }
        
        $this->_custom_data = $data;
    }
    
    
    public function properties()
    {
        return array(
            'module',
            'content',
        );
    }
    
    
    public function displays()
    {
        $content = $this->get('content');
        
        return ( ! empty($content) );
    }
    
    
    /**
     * [get description]
     * @param  [type] $property   [description]
     * @param  array  $conditions [description]
     * @return [type]             [description]
     */
    public function & get($property, array $conditions = array())
    {
        // model columns
        if (array_key_exists($property, static::properties()))
        {
            if ( ! array_key_exists($property, $this->_data))
            {
                $result = null;
            }
            else
            {
                // use a reference
                $result =& $this->_data[$property];
            }
        }
        // stored custom data
        elseif (array_key_exists($property, $this->_custom_data))
        {
            // use a reference
            $result =& $this->_custom_data[$property];
        }
        else
        {
            throw new \OutOfBoundsException('Property "' . $property . '" not found for ' . get_class($this) . '.');
        }
        
        return $result;
    }
    
    
    /**
     * [set description]
     * @param [type] $property [description]
     * @param [type] $value    [description]
     */
    public function set($property, $value = null)
    {
        if ( is_array($property) )
        {
            foreach ( $property as $p => $v )
            {
                $this->set($p, $v);
            }
        }
        else
        {
            if ( func_num_args() < 2 )
            {
                throw new \InvalidArgumentException('You need to pass both a property name and a value to set().');
            }

            if ( array_key_exists($property, static::properties()) )
            {
                $this->_data[$property] = $value;
            }
            else
            {
                $this->_custom_data[$property] = $value;
            }
        }

        return $this;
    }
    
    
    
    

    /**
     * Fetch a property or relation
     *
     * @param   string
     * @return  mixed
     */
    public function & __get($property)
    {
        return $this->get($property);
    }

    /**
     * Set a property or relation
     *
     * @param  string
     * @param  mixed
     *
     * @return Model
     */
    public function __set($property, $value)
    {
        return $this->set($property, $value);
    }
    
}
