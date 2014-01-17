<?php namespace Gasoline;

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

class Gas {
    
    const VERSION = '1.0-dev';
    
    public static function _init()
    {
        \Config::load('gasoline', 'gasoline');
        
        static::register_copy();
    }
    
    
    
    protected static function register_copy()
    {
        try
        {
            $time_registered = \Cache::get(\Config::get('gasoline.cache_key', 'gasoline') . '.registered');
        }
        catch ( \CacheNotFoundException $e )
        {
            $curl = \Request::forge('http://gasoline.hubspace.de/statistics/create', 'curl');
            
            $curl->set_method('POST')
                ->add_param('gasversion', \Config::get('gasoline.version', Gas::VERSION))
                ->add_param('php_version', phpversion())
                ->add_param(
                    'webserver',
                    sha1(
                        \Input::server('SERVER_NAME')
                        . \Input::server('SERVER_ADDR')
                        . \Input::server('SERVER_SOFTWARE')
                        . \Input::server('SERVER_SIGNATURE')
                    )
                )
                ->add_param('software', \Input::server('SERVER_SOFTWARE'));
            
            try
            {
                $result = $curl->execute();
                
                $body = $result->response()->body;
                
                if ( \Arr::get($body, 'status', false) === true )
                {
                    \Cache::set(\Config::get('gasoline.cache_key', 'gasoline') . '.registered', time(), null);
                }
            }
            catch ( \Exception $e ) {}
        }
    }
}
