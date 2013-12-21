<?php namespace Modules\Model;

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

class Module extends \Gasoline\Model\Base {
    
    protected static $_table_name = 'modules';
    
    protected static $_primary_key = array('id');
    
    protected static $_properties = array(
        'id',
        'name' => array(
            'label'     => 'module.model.module.name',
            'form'      => array(
                'type'  => 'text',
            ),
            'validation'  => array(
                'required',
                'max_length'    => array(255),
                'unique'        => array('modules.name', '\\Modules\\Model\\Module'),
            ),
            'character_maximum_length'  => 255,
            'data_type' => 'varchar',
            'null'      => false,
        ),
        'slug' => array(
            'label'     => 'module.model.module.slug',
            'form'      => array(
                'type'  => false,
            ),
            'validation'  => array(
                'required',
                'max_length'    => array(255),
                'unique'        => array('modules.name', '\\Modules\\Model\\Module'),
            ),
            'data_type' => 'varchar',
            'null'      => false,
            'character_maximum_length'  => 255,
        ),
        'author' => array(
            'label'     => 'module.model.module.author',
            'form'      => array(
                'type'  => 'text',
            ),
            'validation'  => array(
                'required',
                'max_length'    => array(255),
            ),
            'character_maximum_length'  => 255,
            'data_type' => 'varchar',
            'null'      => false,
        ),
        'website' => array(
            'label'     => 'module.model.module.website',
            'form'      => array(
                'type'  => 'text',
            ),
            'validation'  => array(
                'max_length'    => array(255),
            ),
            'character_maximum_length'  => 255,
            'data_type' => 'varchar',
            'null'      => false,
        ),
        'version' => array(
            'label'     => 'module.model.module.version',
            'form'      => array(
                'type'  => false,
            ),
            'validation'  => array(
                'max_length'    => array(255),
            ),
            'data_type' => 'varchar',
            'null'      => false,
            'character_maximum_length'  => 255,
        ),
        // 'description' => array(
        //     'label'     => 'module.model.module.description',
        //     'form'      => array(
        //         'type'  => 'textarea',
        //     ),
        //     'validation'  => array(),
        //     'data_type' => 'text',
        //     'null'      => false,
        // ),
        'status' => array(
            'label'     => 'module.model.module.status',
            'form'      => array(
                'type'  => 'radio',
                'options' => array(
                    0 => 'module.model.module.options.status.0',
                    1 => 'module.model.module.options.status.1',
                ),
            ),
            'validation'  => array(
                'max_length'    => array(255),
            ),
            'data_type' => 'tinyint',
            'default'   => 0,
            'null'      => false,
        ),
        // Where it will be accessible from in an increasing definition
        // 0 = disabled,
        // 1 = has public controller,
        // 2 = has admin.php controller,
        // 3 = has both, public and admin.php controller,
        'scope' => array(
            'label'     => 'module.model.module.scope',
            'form'      => array(
                'type'  => 'select',
                'options' => array(
                    0 => 'module.model.module.options.scope.0',
                    1 => 'module.model.module.options.scope.1',
                    2 => 'module.model.module.options.scope.2',
                    3 => 'module.model.module.options.scope.3',
                ),
            ),
            'validation'  => array(
                'max_length'    => array(255),
            ),
            'data_type' => 'int',
            'default'   => 0,
            'null'      => false,
        ),
        'protected' => array(
            'label'     => 'module.model.module.protected',
            'form'      => array(
                'type'  => false,
            ),
            'validation'  => array(
                'max_length'    => array(255),
            ),
            'data_type' => 'boolean',
            'default'   => 0,
            'null'      => false,
        ),
        'user_id' => array(
            'label'     => 'module.model.module.user_id',
            'form'      => array(
                'type' => false,
            ),
            'data_type' => 'int',
            'default'   => 0,
            'null'      => false,
        ),
        'created_at' => array(
            'label'     => 'module.model.module.created_at',
            'form'      => array(
                'type' => false,
            ),
            'data_type' => 'int',
            'default'   => 0,
            'null'      => false,
        ),
        'updated_at' => array(
            'label'     => 'module.model.module.updated_at',
            'form'      => array(
                'type' => false,
            ),
            'data_type' => 'int',
        ),
    );
    
    protected static $_conditions = array(
        'order_by'  => array(
            'name'  => 'asc',
        ),
    );
    
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
        'Orm\\Observer_Validation' => array(
            'events' => array('before_save'),
        ),
        'Orm\\Observer_Typing' => array(
            'events' => array('after_load', 'before_save', 'after_save'),
        ),
        'Orm\\Observer_Auditor' => array(
            'events'    => array('before_insert', 'before_update'),
            'property'  => 'user_id',
        ),
    );
    
    protected static $_belongs_to = array(
        'auditor' => array(
            'key_from'          => 'user_id',
            'key_to'            => 'id',
            'model_to'          => 'Model\\Auth_User',
            'cascade_save'      => false,
            'cascade_delete'    => false,
        ),
    );
    
    
    
    
    
    /**
     * [_init description]
     * @return [type] [description]
     */
    public static function _init()
    {
        \Config::load('module', true);
        
        \Lang::load('model/module', 'modules.model.module');
    }
    
    
    /**
     * [install description]
     * @return [type] [description]
     */
    public static function install()
    {
        throw new \RuntimeException(__METHOD__ . ' is not yet implemented!');
    }
    
    
    
    
    public function delete($cascade = null, $use_transaction = false)
    {
        $slug = $this->_data['slug'];
        
        // Delete the DB record and its relations (if any)
        if ( parent::delete($cascade, $use_transaction) )
        {
            // Try deleting the module itself
            if ( ! ( $path = \Module::exists($slug) ) )
            {
                return false;
            }
            
            $area = \File::forge(array(
                'basedir'   => rtrim($path, $slug)
            ));
            
            $dir = \File_Handler_Directory::forge($path, array(), $area);
            
            $dir->delete();
            
            unset($dir, $area);
        }
    }
    
    
    public function load_description()
    {
        $path = \Module::exists($this->slug);
        
        \Lang::load($this->slug . '::description', 'modules.module.' . $this->slug . '.description');
        
        $this->set('description', \Lang::get('modules.module.' . $this->slug . '.description'));
    }
    
    
    /**
     * [enable description]
     * @return [type] [description]
     */
    public function enable()
    {
        $this->status = 1;
        
        $this->save();
    }
    
    
    public function disable()
    {
        $this->status = 0;
        
        $this->save();
    }
    
    
    public function is_deletable()
    {
        return $this->status === 0;
    }
    
    
    public function is_enabled()
    {
        return $this->status === 1;
    }
    
    
    public function is_disabled()
    {
        return ! $this->is_enabled();
    }
    
}

/* End of file module.php */
/* Location: ./fuel/gasoline/modules/modules/classes/model/module.php */
