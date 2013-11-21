<?php namespace Gasoline\Orm;

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

class Observer_Slug extends \Orm\Observer_Slug {
    
    
    
    /**
     * Default lowercasing flag for Inflector
     * 
     * @access  public
     * @static
     * @var     boolean
     */
    public static $lowercase = true;
    
    
    /**
     * Default separator for Inflector
     * 
     * @access  public
     * @static
     * @var     string
     */
    public static $separator = '-';
    
    
    /**
     * Final lowercase property for observer to use
     * 
     * @access  protected
     * @var     boolean
     */
    protected $_lowercase;
    
    
    /**
     * Final separator for observer to use
     * 
     * @access  protected
     * @var     string
     */
    protected $_separator;
    
    
    
    
    
    //--------------------------------------------------------------------------
    
    /**
     * Set the properties for this observer instance, based on the parent model's
     * configuration or the defined defaults.
     *
     * @param  string  Model class this observer is called on
     */
    public function __construct($class)
    {
        $props = $class::observers(get_class($this));
        $this->_source    = isset($props['source']) ? $props['source'] : static::$source;
        $this->_property  = isset($props['property']) ? $props['property'] : static::$property;
        $this->_lowercase = isset($props['lowercase']) ? $props['lowercase'] : static::$lowercase;
        $this->_separator = isset($props['separator']) ? $props['separator'] : static::$separator;
    }
    
    
    //--------------------------------------------------------------------------
    
    /**
     * Creates a unique slug and adds it to the object
     *
     * @param  Model  Model object subject of this observer method
     */
    public function before_insert(\Orm\Model $obj)
    {
        // determine the slug
        $properties = (array) $this->_source;
        $source = '';
        foreach ($properties as $property)
        {
            $source .= $this->_separator.$obj->{$property};
        }
        $slug = \Inflector::friendly_title(substr($source, 1), $this->_separator, $this->_lowercase);
        
        // query to check for existence of this slug
        $query = $obj->query()->where($this->_property, 'like', $slug.'%');
        
        // is this a temporal model?
        if ($obj instanceOf Model_Temporal)
        {
            // add a filter to only check current revisions excluding the current object
            $class = get_class($obj);
            $query->where($class::temporal_property('end_column'), '=', $class::temporal_property('max_timestamp'));
            
            foreach($class::getNonTimestampPks() as $key)
            {
                $query->where($key, '!=', $obj->{$key});
            }
        }
        
        // do we have records with this slug?
        $same = $query->get();
        
        // make sure our slug is unique
        if ( ! empty($same))
        {
            $max = -1;
            
            foreach ($same as $record)
            {
                if (preg_match('/^'.$slug.'(?:-([0-9]+))?$/', $record->{$this->_property}, $matches))
                {
                    $index = isset($matches[1]) ? (int) $matches[1] : 0;
                    $max < $index and $max = $index;
                }
            }
            
            $max < 0 or $slug .= $this->_separator.($max + 1);
        }
        
        $obj->{$this->_property} = $slug;
    }
    
    
    //--------------------------------------------------------------------------
    
    /**
     * Creates a new unique slug and update the object
     *
     * @param  Model  Model object subject of this observer method
     */
    public function before_update(\Orm\Model $obj)
    {
        // determine the slug
        $properties = (array) $this->_source;
        $source = '';
        foreach ($properties as $property)
        {
            $source .= $this->_separator.$obj->{$property};
        }
        $slug = \Inflector::friendly_title(substr($source, 1), $this->_separator, $this->_lowercase);
        
        // update it if it's different from the current one
        $obj->{$this->_property} === $slug or $this->before_insert($obj);
    }
    
}

/* End of file slug.php */
/* Location: ./fuel/gasoline/classes/orm/observer/slug.php */
