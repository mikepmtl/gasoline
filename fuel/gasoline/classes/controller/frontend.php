<?php namespace Gasoline\Controller;

abstract class Frontend extends Public {
    
    public function before()
    {
        parent::before();
        
        \Breadcrumb\Container::instance()->set('/', new \Breadcrumb\Crumb(array('href' => 'admin', 'text' => 'Home', 'attributes' => array())));
    }
    
}

/* End of file public.php */
/* Location: ./fuel/gasoline/classes/controller/public.php */
