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

abstract class ViewModel extends \Fuel\Core\ViewModel {
    
    protected function set_view()
    {
        $this->_view = \Theme::instance()->view($this->_view);
    }
    
}

/* End of file viewmodel.php */
/* Location: ./fuel/gasoline/classes/viewmodel.php */
