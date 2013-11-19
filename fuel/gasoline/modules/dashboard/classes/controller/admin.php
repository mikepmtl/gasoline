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
            $controllers = glob($module_path . '*/classes/controller/widget.php');
            
            if ( ! $controllers )
            {
                continue;
            }
            
            foreach ( $controllers as $controller )
            {
                $path = explode(DS, str_replace($module_path, '', $controller));
                
                $module = $path[0];
                
                try
                {
                    $response = \Request::forge($module . '/widget/dashboard/admin', false)->execute()->response();
                    
                    $widgets[] = array(
                        'module'    => $module,
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
