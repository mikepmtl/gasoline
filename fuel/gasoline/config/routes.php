<?php

return array(
    // 'dashboard' => array('auth/dashboard/user', 'name' => 'dashboard.user'),
    
    'admin/dashboard'   => array('name' => 'dashboard.admin'),
    
    'auth/login'    => array('auth/auth/login',     'name' => 'auth.login'),
    'auth/logout'   => array('auth/auth/logout',    'name' => 'auth.logout'),
    
    'admin/(:segment)/(:any)'   => '$1/admin/$2',
    'admin/(:segment)'          => '$1/admin',
    'admin'             => 'dashboard/admin',
);
