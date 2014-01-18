<?php namespace Widgets\Model;

class Area {
    
    protected static $user_id = 0;
    
    protected static $widgets_cached = array();
    
    
    /**
     * [_init description]
     * @return [type] [description]
     */
    public static function _init()
    {
        list(, $user_id) = \Auth::get_user_id();
        
        static::$user_id = $user_id;
    }
    
    
    /**
     * [get description]
     * @param  [type]  $area      [description]
     * @param  boolean $overwrite [description]
     * @return [type]             [description]
     */
    public static function get($area, $overwrite = false)
    {
        if ( array_key_exists($area, static::$widgets_cached) )
        {
            return static::$widgets_cached[$area];
        }
        
        $area = trim($area, '.');
        
        if ( $overwrite )
        {
            $widgets = static::search($area);
        }
        else
        {
            try
            {
                $widgets = static::read_cache($area);
            }
            catch ( \CacheNotFoundException $e )
            {
                $widgets = static::search($area);
            }
            catch ( \CacheExpiredException $e )
            {
                $widgets = static::search($area);
            }
        }
        
        static::$widgets_cached[$area] = $widgets;
        
        \Cache::set('widgets.' . $area . '.user_' . static::$user_id, $widgets);
        
        return $widgets;
    }
    
    
    /**
     * [count description]
     * @param  [type] $area [description]
     * @return [type]       [description]
     */
    public static function count($area)
    {
        return count(static::get($area));
    }
    
    
    /**
     * [search description]
     * @param  [type] $area [description]
     * @return [type]       [description]
     */
    public static function search($area)
    {
        $area = trim($area, '.');
        
        $widgets = array();
        
        $area_path = str_replace('.', '/', $area);
        
        // Loop through all modules and display their dashboard widget
        foreach ( \Config::get('module_paths') as $module_paths )
        {
            if ( ! ( $modules = glob($module_paths . '*') ) )
            {
                continue;
            }
            
            foreach ( $modules as $module_path )
            {
                if ( ! ( $controllers = glob($module_path . '/classes/controller/widgets*')) )
                {
                    continue;
                }
                
                foreach ( $controllers as $controller )
                {
                    $_module = substr($module_path, strripos($module_path, '/') + 1);
                    
                    try
                    {
                        $response = \Request::forge($_module . '/widgets/' . $area_path, false)->execute()->response();
                        
                        $widget = \Widgets\Model\Widget::forge(array(
                            'module'    => $_module,
                            'content'   => $response->body,
                        ));
                        
                        $widget->displays() && $widgets[$_module] = $widget;
                    }
                    catch ( \Exception $e ){}
                }
            }
        }
        
        return $widgets;
    }
    
    
    /**
     * [read_cache description]
     * @param  [type] $area [description]
     * @return [type]       [description]
     */
    protected static function read_cache($area)
    {
        $area = trim($area, '.');
        
        return \Cache::get('widgets.' . $area . '.user_' . static::$user_id);
    }
    
}
