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

class View extends \Fuel\Core\View {
    
    /**
     * Sets the initial view filename and local data.
     *
     *     $view = new View($file);
     *
     * @param   string  view filename
     * @param   array   array of values
     * @return  void
     * @uses    View::set_filename
     */
    public function __construct($file = null, $data = null, $filter = null)
    {
        if (is_object($data) === true)
        {
            $data = get_object_vars($data);
        }
        elseif ($data and ! is_array($data))
        {
            throw new \InvalidArgumentException('The data parameter only accepts objects and arrays.');
        }

        $this->auto_filter = is_null($filter) ? \Config::get('security.auto_filter_output', true) : $filter;

        if ($file !== null)
        {
            $this->set_filename($file);
        }

        if ($data !== null)
        {
            // Add the values to the current data
            $this->data = $data;
        }

        // store the current request search paths to deal with out-of-context rendering
        if (class_exists('Request', false) and $active = \Request::active() and \Request::main() !== $active)
        {
            $this->request_paths = $active->get_paths();
        }
        isset($active) and $this->active_request = $active;

        // store the active language, so we can render the view in the correct language later
        $this->active_language = \Config::get('language', 'en');
    }
    
}

/* End of file view.php */
/* Location: ./fuel/gasoline/classes/view.php */
