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

class Widget extends \Controller\Widget {
    
    public function action_dashboard($scope = 'user')
    {
        if ( method_exists($this, 'dashboard_' . $scope) )
        {
            return $this->{'dashboard_' . $scope}();
        }
        
        return '';
    }
    
    public function dashboard_admin()
    {
        $recent = \Model\Auth_User::query()
            ->order_by('created_at', 'desc')
            ->limit('5')
            ->get();
        
        $this->view = static::$theme
            ->view('widget/dashboard/admin')
            ->set('recent', $recent);
    }
    
    public function dashboard_user()
    {
        return '';
    }
  
}

/* End of file dashboard.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/widget/dashboard.php */
