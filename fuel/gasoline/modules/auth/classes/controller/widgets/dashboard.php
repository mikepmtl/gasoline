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

class Widgets_Dashboard extends \Controller\Widget {
    
    public function action_admin()
    {
        $recent = \Model\Auth_User::query()
            ->where('id', '!=', '0')
            ->limit('5')
            ->order_by('created_at', 'desc')
            ->get();
        
        $this->view = static::$theme
            ->view('widgets/dashboard/admin')
            ->set('recent', $recent);
    }
    
    public function action_user()
    {
        return '';
    }
  
}

/* End of file dashboard.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/widget/dashboard.php */
