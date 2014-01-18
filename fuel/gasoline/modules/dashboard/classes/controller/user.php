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

class User extends \Controller\Authenticated {
    
    protected static $me;
    
    
    public function before()
    {
        parent::before();
        
        static::$me = \Auth::get_user();
        
        \Breadcrumb\Container::instance()->set('dashboard', __('global.dashboard'));
    }
    
    
    public function action_index()
    {
        try
        {
            $widgets = \Cache::get('dashboard.user.id_' . static::$me->id);
        }
        catch ( \Exception $e )
        {
            $widgets = array();
            
            // Loop through all modules and display their dashboard widget
            foreach ( \Config::get('module_paths') as $module_path )
            {
                if ( ! ( $controller = glob($module_path . '*/classes/controller/widgets/dashboard*') ) )
                {
                    continue;
                }
                
                foreach ( $controller as $module )
                {
                    $path = explode(DS, str_replace($module_path, '', $module));
                    
                    $_module = $path[0];
                    
                    try
                    {
                        $response = \Request::forge($_module . '/widgets/dashboard/user', false)->execute()->response();
                        
                        $widgets[] = array(
                            'module'    => $_module,
                            'body'      => $response->body,
                        );
                    }
                    catch ( \Exception $e ) {}
                }
            }
            
            \Cache::set('dashboard.user.id_' . static::$me->id, $widgets);
        }
        
        $this->view = static::$theme
            ->view('user/index')
            ->set('widgets', $widgets, false)
            ->set('user', static::$me);
    }
    
}

/* End of file user.php */
/* Location: ./fuel/gasoline/modules/dashboard/classes/controller/user.php */
