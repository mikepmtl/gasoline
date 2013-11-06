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

class Authenticated extends Base {
    
    public function before()
    {
        if ( ! \Auth::check() )
        {
            return \Response::redirect_back(\Router::get('_root_'));
        }
        
        parent::before();
    }
    
}
