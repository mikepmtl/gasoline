<?php namespace Seeds;

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

class Register_module_auth {
    
    public function up()
    {
        \Module::load('modules');
        
        $module = \Modules\Model\Module::forge(array(
            'name'      => 'Auth',
            'slug'      => 'auth',
            'author'    => 'Philipp Tempel',
            'version'   => '1.0-dev',
            'status'    => true,
            'scope'     => 3,
            'protected' => true,
        ));
        
        $module->save();
    }
    
    public function down()
    {
        $module = \Modules\Model\Module::find_by_slug('auth');
        
        $module && $module->delete();
    }
    
}

/* End of file 002_Register_module_auth.php */
/* Location: ./fuel/gasoline/modules/auth/seeds/002_Register_module_auth.php */
