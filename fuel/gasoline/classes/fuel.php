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

class Fuel extends \Fuel\Core\Fuel {
    
    
    /**
     * Cleans a file path so that it does not contain absolute file paths.
     *
     * @static
     * @access  public
     * @param   string  $path   The path to clean
     * 
     * @return  string          Cleaned path
     */
    public static function clean_path($path)
    {
        static $search = array(APPPATH, COREPATH, PKGPATH, DOCROOT, GASPATH, '\\');
        static $replace = array('APPPATH/', 'COREPATH/', 'PKGPATH/', 'DOCROOT/', 'GASPATH', '/');
        return str_ireplace($search, $replace, $path);
    }
    
}
