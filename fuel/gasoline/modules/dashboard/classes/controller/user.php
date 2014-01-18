<?php namespace Dashboard\Controller;

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

class User extends \Controller\Authenticated {
    
    protected static $me;
    
    
    public function before()
    {
        parent::before();
        
        static::$me = \Auth::get_user();
        
        \Breadcrumb\Container::instance()->set_crumb('dashboard', __('global.dashboard'));
    }
    
    
    public function action_index()
    {
        \Module::load('widgets');
        
        $this->view = static::$theme
            ->view('user/index')
            ->set('widgets', \Widgets\Model\Area::get('dashboard.user'), false)
            ->set('user', static::$me);
    }
    
}

/* End of file user.php */
/* Location: ./fuel/gasoline/modules/dashboard/classes/controller/user.php */
