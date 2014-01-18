<?php namespace Gasoline\Controller;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

abstract class Authenticated extends Frontend {
    
    public function before()
    {
        if ( ! \Auth::check() )
        {
            if ( \Request::is_hmvc() )
            {
                return false;
            }
            
            return \Response::redirect(\Uri::create(\Router::get('auth.login'), array(), array('redirect' => \Uri::string())));
        }
        
        parent::before();
    }
    
}
