<?php namespace Gasoline\Model;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Gasoline Development Teams
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @copyright   2010 - 2013 Fuel Development Team
 * @link        http://hubspace.github.io/gasoline
 */

class Auth_User extends Base {
        
    /**
     * @var  string  connection to use
     */
    protected static $_connection = null;
    
    /**
     * @var  string  table name to overwrite assumption
     */
    protected static $_table_name;
    
    /**
     * @var array   model properties
     */
    protected static $_properties = array(
        'id',
        'username' => array(
            'label' => 'auth.model.user.username',
            'null'  => false,
            'form'  => array(
                'type'  => 'text',
            ),
            'validation'  => array(
                'required',
                'max_length' => array(255),
            ),
        ),
        'email' => array(
            'label' => 'auth.model.user.email',
            'null'  => false,
            'form'  => array(
                'type'  => 'email',
            ),
            'validation'  => array(
                'required',
                'valid_email',
                'unique' => null,
            )
        ),
        'group_id' => array(
            'label' => 'auth.model.user.group_id',
            'null'  => false,
            'form'  => array(
                'type' => 'select'
            ),
            'validation'  => array(
                'required',
                'is_numeric',
                'exists'    => null,
            )
        ),
        'password' => array(
            'label' => 'auth.model.user.password',
            'null'  => false,
            'form'  => array(
                'type' => 'password',
            ),
            'validation'  => array(
                'min_length' => array(4),
            )
        ),
        'last_login' => array(
            'label' => 'auth.model.user.last_login',
            'form'  => array(
                'type' => false,
            ),
        ),
        'previous_login' => array(
            'label' => 'auth.model.user.previous_login',
            'form'  => array(
                'type' => false,
            ),
        ),
        'login_hash' => array(
            'label' => 'auth.model.user.login_hash',
            'form'  => array(
                'type' => false,
            ),
        ),
        'user_id' => array(
            'label'     => 'auth.model.user.user_id',
            'default'   => 0,
            'null'      => false,
            'form'      => array(
                'type' => false,
            ),
        ),
        'created_at' => array(
            'label'     => 'auth.model.user.created_at',
            'default'   => 0,
            'null'      => false,
            'form'      => array(
                'type' => false,
            ),
        ),
        'updated_at' => array(
            'label'     => 'auth.model.user.updated_at',
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
            'mysql_timestamp'   => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events'            => array('before_update'),
            'property'          => 'updated_at',
            'mysql_timestamp'   => false,
        ),
        'Orm\\Observer_Typing' => array(
            'events' => array('after_load', 'before_save', 'after_save'),
        ),
        'Orm\\Observer_Self' => array(
            'events'    => array('before_insert', 'before_update'),
            'property'  => 'user_id',
        ),
    );
    
    // EAV container for user metadata
    protected static $_eav = array(
        'metadata' => array(
            'attribute' => 'attribute',
            'value'     => 'value',
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
    );
    
    /**
     * @var array   has_many relationships
     */
    protected static $_has_many = array(
        'metadata' => array(
            'key_from'          => 'id',
            'key_to'            => 'parent_id',
            'model_to'          => 'Model\\Auth_Metadata',
            'cascade_save'      => true,
            'cascade_delete'    => true,
        ),
        'userpermissions' => array(
            'key_from'          => 'id',
            'key_to'            => 'user_id',
            'model_to'          => 'Model\\Auth_User_Permission',
            'cascade_save'      => false,
            'cascade_delete'    => true,
        ),
    );
    
    /**
     * @var array   many_many relationships
     */
    protected static $_many_many = array(
        'roles' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'user_id',
            'table_through'     => null,
            'key_through_to'    => 'role_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_Role',
        ),
        'permissions' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'user_id',
            'table_through'     => null,
            'key_through_to'    => 'perms_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_Permission',
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
        static::$_table_name = \Config::get('gasauth.table_name', 'users');

        // set the relations through table names
        static::$_many_many['roles']['table_through'] = \Config::get('gasauth.table_name', 'users').'_user_roles';
        static::$_many_many['permissions']['table_through'] = \Config::get('gasauth.table_name', 'users').'_user_permissions';
        
        // Set the validation rules that require the base table name
        static::$_properties['email']['validation']['unique'] = array(\Config::get('gasauth.table_name', 'users') . '.email',);
        static::$_properties['group_id']['validation']['exists'] = array(\Config::get('gasauth.table_name', 'users') . '_groups.id',);

        // model language file
        \Lang::load('auth/model/user', true);
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


/* End of file user.php */
/* Location: ./fuel/gasoline/classes/model/auth/user.php */
