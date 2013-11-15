<?php namespace Gasoline\Controller;

abstract class Admin extends Authenticated {
    
    public static $theme = 'todo';
    
    public function before()
    {
        parent::before();
        
        \Breadcrumb\Container::instance()->set('admin', new \Breadcrumb\Crumb(array('href' => 'admin', 'text' => 'Admin', 'attributes' => array())));
    }
    
}

/* End of file admin.php */
/* Location: ./fuel/gasoline/classes/controller/admin.php */
