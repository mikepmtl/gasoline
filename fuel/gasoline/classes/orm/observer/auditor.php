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

class Observer_Auditor extends \Orm\Observer {
    
    
    
    /**
     * Default property to put the current user's ID in
     * 
     * @access  public
     * @static
     * @var     boolean
     */
    public static $property = 'user_id';
    
    
    /**
     * Final property to put the current user's ID in
     * 
     * @access  protected
     * @var     string
     */
    protected $_property;
    
    
    
    
    
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
        $this->_property  = isset($props['property']) ? $props['property'] : static::$property;
    }
    
    
    //--------------------------------------------------------------------------
    
    /**
     * Sets the auditor property on inserting
     *
     * @param  Model  Model object subject of this observer method
     */
    public function before_insert(\Orm\Model $obj)
    {
        $auth_id = \Auth::get_user_id();
        
        $obj->{$this->_property} = ( $auth_id ? $auth_id[1] : 0 );
    }
    
    
    //--------------------------------------------------------------------------
    
    /**
     * Sets the auditor property on any update
     *
     * @param  Model  Model object subject of this observer method
     */
    public function before_update(\Orm\Model $obj)
    {
        $this->before_insert($obj);
    }
    
}

/* End of file auditor.php */
/* Location: ./fuel/gasoline/classes/orm/observer/auditor.php */
