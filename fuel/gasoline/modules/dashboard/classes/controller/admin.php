<?php namespace Dashboard\Controller;

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

class Admin extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Breadcrumb\Container::instance()->set_crumb('admin/dashboard', __('global.dashboard'));
    }
    
    public function action_index()
    {
        $widgets = array();
        
        // Loop through all modules and display their dashboard widget
        foreach ( \Config::get('module_paths') as $module_path )
        {
            if ( ! ( $to_consider = glob($module_path . '*/classes/controller/widgets*') ) )
            {
                continue;
            }
            
            foreach ( $to_consider as $module )
            {
                $path = explode(DS, str_replace($module_path, '', $module));
                
                $_module = $path[0];
                
                try
                {
                    $response = \Request::forge($_module . '/widgets/dashboard/admin', false)->execute()->response();
                    
                    $widgets[] = array(
                        'module'    => $_module,
                        'body'      => $response->body,
                    );
                }
                catch ( \Exception $e ) {}
            }
        }
        
        $this->view = static::$theme
            ->view('admin/index')
            ->set('widgets', $widgets, false);
    }
    
}

/* End of file admin.php */
/* Location: ./fuel/gasoline/modules/dashboard/classes/controller/admin.php */
