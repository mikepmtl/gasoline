<?php

return array(
    'dashboard' => array('name' => 'dashboard.user'),
    
    'admin/dashboard'   => array('name' => 'dashboard.admin'),
    
    'auth/login'    => array('name' => 'auth.login'),
    'auth/logout'   => array('name' => 'auth.logout'),
    
    'admin/(:segment)/(:any)'   => '$1/admin/$2',
    'admin/(:segment)'          => '$1/admin',
    'admin'                     => 'dashboard/admin',
);
