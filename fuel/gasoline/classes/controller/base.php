<?php namespace Gasoline\Controller;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

class Base extends \Controller {
    
    public function before()
    {
        parent::before();
    }
    
    
    public function after($response)
    {
        return parent::after($response);
    }
    
}
