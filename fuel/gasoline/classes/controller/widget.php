<?php namespace Gasoline\Controller;

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

class Widget extends Base {
    
    public function before()
    {
        if ( ! \Request::is_hmvc() )
        {
            throw new \HttpNotFoundException();
        }
        
        parent::before();
    }
    
}
