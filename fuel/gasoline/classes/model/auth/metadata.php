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

class Auth_Metadata extends Base {
    
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
        'parent_id',
        'attribute',
        'value',
        'user_id' => array(
            'default'   => 0,
            'null'      => false,
            'form'      => array(
                'type' => false
            ),
        ),
        'created_at' => array(
            'label'     => 'auth.metadata.created_at',
            'default'   => 0,
            'null'      => false,
            'form'      => array(
                'type' => false,
            ),
        ),
        'updated_at' => array(
            'label'     => 'auth.metadata.updated_at',
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
    
    /**
     * @var array   belongs_to relationships
     */
    protected static $_belongs_to = array(
        'user' => array(
            'key_from'          => 'parent_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_User',
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
        static::$_table_name = \Config::get('gasauth.table_name', 'users').'_metadata';
        
        // model language file
        \Lang::load('auth/model/metadata', true);
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

/* End of file metadata.php */
/* Location: ./fuel/gasoline/classes/model/auth/metadata.php */
