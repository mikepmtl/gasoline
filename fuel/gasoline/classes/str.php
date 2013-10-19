<?php namespace Gasoline;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Gasoline Development Teams
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @copyright   2010 - 2013 Fuel Development Team
 * @link        http://hubspace.github.io/gasoline
 */

/**
 * String handling with encoding support
 *
 * PHP needs to be compiled with --enable-mbstring or a fallback without encoding
 *  support is used
 */

class Str extends \Fuel\Core\Str {
    
    
    /**
     * Create a more random string of characters than the parent class does
     * 
     * 
     * @param   string  $type   The type of random string to match
     * @param   integer $length Desired length of the string (where applicable)
     * @return  string  Returns the random string
     */
    public static function random($type = 'alnum', $length = 16)
    {
        static $seed;
        
        if ( ! $seed )
        {
            mt_srand( (double) microtime() * 1000000 );
            
            $seed = true;
        }
        
        return parent::random($type, $length);
    }
    
}

/* End of file str.php */
/* Location: ./fuel/gasoline/str.php */
