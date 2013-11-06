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

class Helpers {
    
    /**
     * Evaluates the passed variable
     * 
     * @static
     * @access  public
     * @param   mixed   $val    The value to evaluate. If it's a callable "thing",
     *                          it will be evaluated with the arguments passed after
     *                          $val, otherwise it will just be returned as it is.
     * 
     * @return  mixed
     */
    public static function result($val)
    {
        return ( is_callable($val) ? call_fuel_func_array($val, @array_splice(func_get_args(), 1)) : $val );
    }
    
}

/* End of file helpers.php */
/* Location: ./fuel/gasoline/helpers.php */
