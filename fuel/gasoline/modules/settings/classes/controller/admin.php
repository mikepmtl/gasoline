<?php namespace Settings\Controller;

class Admin extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Lang::load('setting', true);
        
        \Breadcrumb\Container::instance()->set_crumb('admin/settings', __('setting.breadcrumb.section'));
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
            if ( file_exists($path = $module_path . $method . DS . 'classes' . DS . 'controller' . DS . 'settings.php') )
            {
                try
                {
                    $request = \Request::forge($method . '/settings/admin')
                        ->set_method(\Request::active()->get_method())
                        ->execute();
                    
                    return $request->response()->body;
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
        
        // if not, we got ourselfs a genuine 404!
        throw new \HttpNotFoundException();
    }
    
}
