<?php namespace Auth\Controller;

class Admin extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
    }
    
    public function action_index()
    {
        $this->view = static::$theme
            ->view('admin/index');
    }
    
}
