<?php namespace Gasoline;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Gasoline Development Teams
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @copyright   2010 - 2013 Fuel Development Team
 * @link        http://hubspace.github.io/gasoline
 */

use ArrayAccess;
use Countable;

class DataContainer implements ArrayAccess, Countable {
    
    /**
     * Stores the data
     * 
     * @access  protected
     * @var     array()
     */
    protected $data = array();
    
    /**
     * Keeps the parent datacontainer, if set and enabled
     * 
     * @access  protected
     * @var     Datacontainer
     */
    protected $parent;
    
    /**
     * Whether the parent is enabled
     * 
     * @access  protected
     * @var     boolean
     */
    protected $parent_enabled = false;
    
    /**
     * Allows datacontainers to read only
     * 
     * @access  protected
     * @var     boolean
     */
    protected $readonly = false;
    
    
    /**
     * Constructor to create a new datacontainer
     * 
     * @access  public
     * @param   array   $data       The data to set upon construction
     * @param   boolean $readonly   Boolean whether the data container is read only or not
     */
    public function __construct(array $data = array(), $readonly = false)
    {
        $this->data = $data;
        
        $this->readonly = $readonly;
    }
    
    
    /**
     * Set the parent of this data container
     * 
     * @access  public
     * @param   Datacontainer   $parent     The parent to set
     * 
     * @return  Datacontainer
     */
    public function set_parent(Datacontainer $parent = null)
    {
        $this->parent = $parent;
        
        $this->enable_parent();
        
        return $this;
    }
    
    
    /**
     * Enables the parent to be accessible
     * 
     * @access  public
     * 
     * @return  Datacontainer
     */
    public function enable_parent()
    {
        $this->enable_parent = true;
        
        return $this;
    }
    
    
    /**
     * Disable accessibility of the parent datacontainer
     * 
     * @access  public
     * 
     * @return  Datacontainer
     */
    public function disable_parent()
    {
        $this->enable_parent = false;
        
        return $this;
    }
    
    
    /**
     * Set the data even after construction
     * 
     * @access  public
     * @param   array   $data   Array of data to set
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  Datacontainer
     */
    public function set_data(array $data)
    {
        if ( $this->readonly )
        {
            throw new \RuntimeException('Changing values on a read-only data container is not allowed');
        }
        
        $this->data = $data;
        
        return $this;
    }
    
    
    /**
     * Clear all the data of this container
     * 
     * @access  public
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  DataContainer
     */
    public function delete_data()
    {
        if ( $this->readonly )
        {
            throw new \RuntimeException('Changing values on a read-only data container is not allowed');
        }
        
        $this->data = array();
        
        return $this;
    }
    
    
    /**
     * Get the data of the container
     * 
     * Will merge the parent's data with this data if there's a parent container
     * 
     * @access  public
     * 
     * @return  array
     */
    public function get_data()
    {
        return ( $this->parent_enabled && $this->parent ? \Arr::merge($this->parent->get_data(), $this->data) : $this->data );
    }
    
    
    /**
     * Set the readonly state
     * 
     * @access  public
     * @param   boolean $state  The state to set. Must be a (bool) convertible value
     * 
     * @return  Datacontainer
     */
    public function readonly($state = true)
    {
        $this->readonly = (bool) $state;
        
        return $this;
    }
    
    
    /**
     * Returns the state of the containers read only status
     * 
     * @access  public
     * 
     * @return  boolean
     */
    public function is_readonly()
    {
        return $this->readonly;
    }
    
    
    /**
     * Checks whether a given key is inside the data of this container
     * 
     * @access  public
     * @param   mixed   $key    The key to check for
     * 
     * @return  boolean
     */
    public function has($key)
    {
        $result = \Arr::key_exists($this->data, $key);
        
        if ( ! $result && $this->parent_enabled && $this->parent )
        {
            $result = $this->parent->has($key);
        }
        
        return $result;
    }
    
    
    /**
     * Get data from the container
     * 
     * @access  public
     * @param   mixed   $key        The value to return
     * @param   mixed   $default    Default value to return if $key was not found
     * 
     * @return  mixed               Returns either the value of $key if found,
     *                              otherwise the value $default
     */
    public function get($key, $default = null)
    {
        $this_fail = uniqid('__FAIL__', true);
        
        $result = \Arr::get($this->data, $key, $default, $this_fail);
        
        if ( $result === $this_fail )
        {
            if ( $this->parent_enabled && $this->parent )
            {
                $result = $this->parent->get($key, $default);
            }
            else
            {
                $result = Helpers::result($default);
            }
        }
        
        return $result;
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
    public function set($key, $value = null)
    {
        if ( $this->readonly )
        {
            throw new \RuntimeException('Changing values on a read-only data container is not allowed');
        }
        
        if ( $key === null )
        {
            $this->data[] = $value;
        }
        else
        {
            \Arr::set($this->data, $key, $value);
        }
        
        return $this;
    }
    
    
    /**
     * Unset data from the container
     * 
     * @access  public
     * @param   mixed   $key    The key to unset
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  boolean
     */
    public function delete($key)
    {
        if ( $this->readonly )
        {
            throw new \RuntimeException('Changing values on a read-only data container is not allowed');
        }
        
        if ( false === ( $result = \Arr::delete($this->data, $key) ) && $this->parent_enabled && $this->parent )
        {
            $result = $this->parent->delete($key);
        }
        
        return $result;
    }
    
    
    /**
     * Array Access Methods
     */
    
    /**
     * Check whether an offset is set
     * 
     * @access  public
     * 
     * @param   mixed   $offset     The offset to check
     * 
     * @return  boolean
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    
    
    /**
     * Get an offset via array access notation i.e, "echo $bag['key']"
     * 
     * @access  public
     * @param   mixed   $offset The offset to get
     * 
     * @throws  OutOfBoundsException    If the data container does not have the offset
     * 
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset, function() use ($offset) {
            throw new \OutOfBoundsException('Access to undefined index: ' . $offset);
        });
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
        return $this->set($offset, $value);
    }
    
    
    /**
     * Unset data from the container
     * 
     * @access  public
     * @param   mixed   $key    The key to unset
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  boolean
     */
    public function offsetUnset($offset)
    {
        return $this->delete($offset);
    }
    
    
    /**
     * Count the number of data of this container
     * 
     * @access  public
     * 
     * @return  integer
     */
    public function count()
    {
        return count($this->get_data());
    }
    
    
    /**
     * Magically get data from the container
     * 
     * @access  public
     * @param   mixed   $key        The value to return
     * @param   mixed   $default    Default value to return if $key was not found
     * 
     * @return  mixed               Returns either the value of $key if found,
     *                              otherwise the value $default
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    
    /**
     * Magically checks whether a given key is inside the data of this container
     * 
     * @access  public
     * @param   mixed   $key    The key to check for
     * 
     * @return  boolean
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
    
    
    /**
     * Magically set a specific item of the data
     * 
     * @access  public
     * @param   mixed   $key    The key to set the data to
     * @param   mixed   $value  The value to set
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  Datacontainer
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }
    
    
    /**
     * Magically unset data from the container
     * 
     * @access  public
     * @param   mixed   $key    The key to unset
     * 
     * @throws  RuntimeException    If the data container is read only
     * 
     * @return  boolean
     */
    public function __unset($key)
    {
        return $this->delete($key);
    }
    
}

/* End of file datacontainer.php */
/* Location: ./fuel/gasoline/datacontainer.php */
