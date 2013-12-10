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

class Register_module_dashboard {
    
    public function up()
    {
        \Module::load('modules');
        
        $module = \Modules\Model\Module::forge(array(
            'name'      => 'Dashboard',
            'slug'      => 'dashboard',
            'author'    => 'Philipp Tempel',
            'website'   => 'http://gasoline.github.io/gasoline',
            'version'   => '1.0-dev',
            'status'    => true,
            'scope'     => 3,
            'protected' => true,
        ));
        
        $module->save();
    }
    
    public function down()
    {
        $module = \Modules\Model\Module::find_by_slug('dashboard');
        
        $module && $module->delete();
    }
    
}

/* End of file 001_Register_module_dashboard.php */
/* Location: ./fuel/gasoline/modules/dashboard/seeds/001_Register_module_dashboard.php */
