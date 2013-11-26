<?php namespace Auth\Model;

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

class Permission extends \Model\Base {
    
    /**
     * @var  string  connection to use
     */
    protected static $_connection = null;
    
    /**
     * @var  string  table name to overwrite assumption
     */
    protected static $_table_name;
    
    /**
     * @var array   primary key of the model
     */
    protected static $_primary_key = array('id');
    
    /**
     * @var array   model properties
     */
    protected static $_properties = array(
        'id',
        'area'           => array(
            'label'       => 'auth.model.permission.area',
            'null'        => false,
            'validation'  => array(
                'required',
                'max_length' => array(25),
            ),
        ),
        'permission'      => array(
            'label'       => 'auth.model.permission.permission',
            'null'        => false,
            'validation'  => array(
                'required',
                'max_length' => array(25),
            ),
        ),
        'description'     => array(
            'label'       => 'auth.model.permission.description',
            'null'        => false,
            'validation'  => array(
                'required',
                'max_length' => array(255),
            ),
        ),
        'actions'         => array(
            'data_type'     => 'json',
            'json_assoc'    => true,
            'default'       => array(),
            'null'          => false,
            'form'          => array(
                'type' => false
            ),
        ),
        'user_id' => array(
            'label'     => 'auth.model.permission.user_id',
            'default'   => 0,
            'null'      => false,
            'form'      => array(
                'type' => false,
            ),
        ),
        'created_at' => array(
            'label'     => 'auth.model.permission.created_at',
            'default'   => 0,
            'null'      => false,
            'form'      => array(
                'type' => false,
            ),
        ),
        'updated_at' => array(
            'label'     => 'auth.model.permission.updated_at',
            'form'      => array(
                'type' => false,
            ),
        ),
    );
    
    /**
     * @var array   defined observers
     */
    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events'            => array('before_insert'),
            'property'          => 'created_at',
            'mysql_timestamp'   => false
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events'            => array('before_update'),
            'property'          => 'updated_at',
            'mysql_timestamp'   => false
        ),
        'Orm\\Observer_Typing' => array(
            'events' => array('after_load', 'before_save', 'after_save')
        ),
        'Orm\\Observer_Self' => array(
            'events'    => array('before_insert', 'before_update'),
            'property'  => 'user_id'
        ),
    );
    
    /**
     * @var array   many_many relationships
     */
    protected static $_many_many = array(
        'users' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'perms_id',
            'table_through'     => null,
            'key_through_to'    => 'user_id',
            'key_to'            => 'id',
            'model_to'          => 'Auth\\Model\\User',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'groups' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'perms_id',
            'table_through'     => null,
            'key_through_to'    => 'group_id',
            'key_to'            => 'id',
            'model_to'           => 'Auth\\Model\\Group',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'roles' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'perms_id',
            'table_through'     => null,
            'key_through_to'    => 'role_id',
            'key_to'            => 'id',
            'model_to'          => 'Auth\\Model\\Role',
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
        static::$_table_name = \Config::get('gasauth.table_name', 'users').'_permissions';
        
        // set the relations through table names
        static::$_many_many['users']['table_through'] = \Config::get('gasauth.table_name', 'users').'_user_permissions';
        static::$_many_many['groups']['table_through'] = \Config::get('gasauth.table_name', 'users').'_group_permissions';
        static::$_many_many['roles']['table_through'] = \Config::get('gasauth.table_name', 'users').'_role_permissions';
        
        // model language file
        \Lang::load('model/permission', 'auth.model.permission');
    }
    
    
    
    
    
    /**
     * before_insert observer event method
     */
    public function _event_before_insert()
    {
        $this->_set_auditor();
    }
    
    /**
     * before_update observer event method
     */
    public function _event_before_update()
    {
        $this->_set_auditor();
    }
    
    
    protected function _set_auditor()
    {
        // assign the user id that lasted updated this record
        $this->user_id = ( ( $this->user_id = \Auth::get_user_id() ) ? $this->user_id[1] : 0 );
    }
    
}

/* End of file permission.php */
/* Location: ./fuel/gasoline/modules/auth/classes/model/auth/permission.php */
