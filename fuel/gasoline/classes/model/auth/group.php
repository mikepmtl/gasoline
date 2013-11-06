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

class Auth_Group extends Base {
    
    /**
     * @var string  connection to use
     */
    protected static $_connection = null;
    
    /**
     * @var string  table name to overwrite assumption
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
        'name'     => array(
            'label'       => 'auth.model.group.name',
            'default'     => '',
            'null'        => false,
            'validation'  => array(
                'required',
                'max_length' => array(255)
            ),
        ),
        'user_id'         => array(
            'label'       => 'auth.model.group.user_id',
            'default'     => 0,
            'null'        => false,
            'form'        => array(
                'type' => false
            ),
        ),
        'created_at'      => array(
            'label'       => 'auth.model.group.created_at',
            'default'     => 0,
            'null'        => false,
            'form'        => array(
                'type' => false
            ),
        ),
        'updated_at'      => array(
            'label'       => 'auth.model.group.updated_at',
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
        'users' => array(
            'key_from'          => 'id',
            'key_to'            => 'group_id',
            'model_to'          => 'Model\\Auth_User',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'grouppermissions' => array(
            'key_from'          => 'id',
            'key_to'            => 'group_id',
            'model_to'          => 'Model\\Auth_Group_Permission',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
    );
    
    /**
     * @var array   many_many relationships
     */
    protected static $_many_many = array(
        'roles' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'group_id',
            'table_through'     => null,
            'key_through_to'    => 'role_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_Role',
            'cascade_save'      => true,
            'cascade_delete'    => false,
        ),
        'permissions' => array(
            'key_from'          => 'id',
            'key_through_from'  => 'group_id',
            'table_through'     => null,
            'key_through_to'    => 'perms_id',
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
        static::$_table_name = \Config::get('gasauth.table_name', 'users').'_groups';

        // set the relations through table names
        static::$_many_many['roles']['table_through'] = \Config::get('gasauth.table_name', 'users').'_group_roles';
        static::$_many_many['permissions']['table_through'] = \Config::get('gasauth.table_name', 'users').'_group_permissions';

        // model language file
        \Lang::load('auth/model/group', true);
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

/* End of file group.php */
/* Location: ./fuel/gasoline/classes/model/auth/group.php */
