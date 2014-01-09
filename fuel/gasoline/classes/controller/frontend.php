<?php namespace Gasoline\Controller;

abstract class Frontend extends Base {
    
    public function before()
    {
        parent::before();
        
        \Breadcrumb\Container::instance()->set('/', new \Breadcrumb\Crumb(array('href' => ( array_key_exists('_root_', \Router::$routes) ? \Router::$routes['_root_']->translation : \Config::get('routes._root_', '/') ), 'text' => 'Home', 'attributes' => array())));
    }
    
}

/* End of file public.php */
/* Location: ./fuel/gasoline/classes/controller/public.php */
