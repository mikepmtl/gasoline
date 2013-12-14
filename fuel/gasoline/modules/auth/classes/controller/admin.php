<?php namespace Auth\Controller;

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

class Admin extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Lang::load('navigation', 'auth.navigation');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.navigation.breadcrumb._section'));
    }
    
    public function action_index()
    {
        $this->view = static::$theme
            ->view('admin/index');
    }
    
}

/* End of file admin.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin.php */
