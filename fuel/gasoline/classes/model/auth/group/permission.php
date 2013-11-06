<?php namespace Gasoline\Model;

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

class Auth_Group_Permission extends Base {
    
    /**
     * @var  string  connection to use
     */
    protected static $_connection = null;

    /**
     * @var  string  table name to overwrite assumption
     */
    protected static $_table_name;

    /**
     * @var  array  name or names of the primary keys
     */
    protected static $_primary_key = array('group_id', 'perms_id');

    /**
     * @var array   model properties
     */
    protected static $_properties = array(
        'group_id',
        'perms_id',
        'actions'         => array(
            'data_type'     => 'json',
            'json_assoc'    => true,
            'default'       => array(),
            'null'          => false,
            'form'          => array(
                'type' => false
            ),
        ),
    );

    /**
     * @var array   defined observers
     */
    protected static $_observers = array(
        'Orm\\Observer_Typing' => array(
            'events' => array('after_load', 'before_save', 'after_save')
        ),
    );

    /**
     * @var array   belongs_to relationships
     */
    protected static $_belongs_to = array(
        'group' => array(
            'key_from'          => 'group_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_Group',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'permission' => array(
            'key_from'          => 'perms_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_Permission',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
    );

    /**
     * init the class
     */
    public static function _init()
    {
        // auth config
        \Config::load('gasauth', true);

        // set the connection this model should use
        static::$_connection = \Config::get('gasauth.db_connection');

        // set the models table name
        static::$_table_name = \Config::get('gasauth.table_name', 'users').'_group_permissions';
        
        // model language file
        \Lang::load('auth/model/user/permission', true);
    }
    
}

/* End of file permission.php */
/* Location: ./fuel/gasoline/classes/model/auth/group/permission.php */
