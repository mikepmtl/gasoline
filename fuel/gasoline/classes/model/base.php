<?php namespace Gasoline\Model;

class Base extends \Orm\Model {
    
    /**
     * Allows for lazily adding has_one relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'profile',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function has_one($key, array $options)
    {
        isset(static::$_has_one) OR static::$_has_one = array();
        
        static::$_has_one[$key] = $options;
    }
    
    
    /**
     * Allows for lazily adding has_many relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'comments',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function has_many($key, array $options)
    {
        isset(static::$_has_many) OR static::$_has_many = array();
        
        static::$_has_many[$key] = $options;
    }
    
    
    /**
     * Allows for lazily adding belongs_to relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'group',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function belongs_to($key, array $options)
    {
        isset(static::$_belongs_to) OR static::$_belongs_to = array();
        
        static::$_belongs_to[$key] = $options;
    }
    
    
    /**
     * Allows for lazily adding many_many relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'rsvp',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function many_many($key, array $options)
    {
        isset(static::$_many_many) OR static::$_many_many = array();
        
        static::$_many_many[$key] = $options;
    }
    
}
