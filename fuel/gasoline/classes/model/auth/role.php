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

class Auth_Role extends Base {
    
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
        'name'            => array(
            'label'       => 'auth.model.role.name',
            'default'     => 0,
            'null'        => false,
            'validation'  => array(
                'required',
                'max_length' => array(255)
            ),
        ),
        'filter'          => array(
            'label'       => 'auth.model.role.filter',
            'null'        => false,
            'data_type'   => 'enum',
            'default'     => '',
            'options'     => array(
                '', 'A', 'D', 'R'
            ),
            'form'        => array(
                'type' => 'select'
            ),
            'validation'  => array(),
        ),
        'user_id'         => array(
            'label'       => 'auth.model.role.user_id',
            'default'     => 0,
            'null'        => false,
            'form'        => array(
                'type' => false
            ),
        ),
        'created_at'      => array(
            'label'       => 'auth.model.role.created_at',
            'default'     => 0,
            'null'        => false,
            'form'        => array(
                'type' => false
            ),
        ),
        'updated_at'      => array(
            'label'       => 'auth.model.role.updated_at',
            'form'        => array(
                'type' => false
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
     * @var array   has_many relationships
     */
    protected static $_has_many = array(
        'rolepermissions' => array(
            'key_from'          => 'id',
            'key_to'            => 'role_id',
            'model_to'          => '\\Model\\Auth_Role_Permission',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
    );

    /**
     * @var array   many_many relationships
     */
    protected static $_many_many = array(
        'users' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'role_id',
            'table_through'     => null,
            'key_through_to'    => 'user_id',
            'key_to'            => 'id',
            'model_to'          => '\\Model\\Auth_User',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'groups' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'role_id',
            'table_through'     => null,
            'key_through_to'    => 'group_id',
            'key_to'            => 'id',
            'model_to'          => '\\Model\\Auth_Group',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'permissions' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'role_id',
            'table_through'     => null,
            'key_through_to'    => 'perms_id',
            'key_to'            => 'id',
            'model_to'          => '\\Model\\Auth_Permission',
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
        \Config::load('ormauth', true);

        // set the connection this model should use
        static::$_connection = \Config::get('ormauth.db_connection');

        // set the models table name
        static::$_table_name = \Config::get('ormauth.table_name', 'users').'_roles';

        // set the relations through table names
        static::$_many_many['users']['table_through'] = \Config::get('ormauth.table_name', 'users').'_user_roles';
        static::$_many_many['groups']['table_through'] = \Config::get('ormauth.table_name', 'users').'_group_roles';
        static::$_many_many['permissions']['table_through'] = \Config::get('ormauth.table_name', 'users').'_role_permissions';

        // set the filter options from the language file
        // static::$_properties['filter']['form']['options'] = \Lang::get('auth.model.role.permissions');

        // model language file
        \Lang::load('auth/model/role', true);
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

/* End of file role.php */
/* Location: ./fuel/gasoline/classes/model/auth/role.php */
