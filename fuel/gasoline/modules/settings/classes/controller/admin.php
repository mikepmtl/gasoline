<?php namespace Settings\Controller;

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
        
        \Lang::load('navigation', 'setting.navigation');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/settings', __('setting.navigation.breadcrumb._section'));
    }
    
    public function router($method, $arguments)
    {
        $method OR $method = 'index';
        
        // check if a input specific method exists
        $controller_method = strtolower(\Input::method()) . '_' . $method;
        
        // fall back to action_ if no rest method is provided
        if ( ! method_exists($this, $controller_method) )
        {
            $controller_method = 'action_' . $method;
        }
        
        // check if the action method exists
        if ( method_exists($this, $controller_method) )
        {
            return call_fuel_func_array(array($this, $controller_method), $arguments);
        }
        
        foreach ( \Config::get('module_paths', array()) as $module_path )
        {
            if ( glob($path = $module_path . $method . DS . 'classes' . DS . 'controller' . DS . 'settings*') )
            {
                try
                {
                    $request = \Request::forge($method . '/settings/admin')
                        ->set_method(\Request::active()->get_method())
                        ->execute();
                    
                    return $this->view = $request->response()->body;
                }
                // catch ( \HttpNotFoundException $e )
                // {
                //     throw new \HttpNotFoundException();
                // }
                catch ( \Exception $e )
                {
                    throw $e;
                }
            }
        }
        
        throw new \HttpNotFoundException();
    }
    
    
    public function action_index()
    {
        \Module::load('widgets');
        
        $this->view = static::$theme
            ->view('admin/index')
            ->set('widgets', \Widgets\Model\Area::get('settings.admin'));
    }
    
}

/* End of file admin.php */
/* Location: ./fuel/gasoline/modules/settings/classes/controller/admin.php */
