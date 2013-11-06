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

class Html extends \Fuel\Core\Html {
    
    public static function anchor($href, $text = null, $attr = array(), $secure = null)
    {
        if ( \Config::get('app.general.links.external_no_follow', false) === true )
        {
            isset($attr['rel']) OR $attr['rel'] = 'nofollow';
        }
        
        return parent::anchor($href, $text, $attr, $secure);
    }
    
}

/* End of file html.php */
/* Location: ./fuel/gasoline/classes/html.php */
