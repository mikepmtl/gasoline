<?php namespace Permissions\Controller;

class Admin extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Lang::load('auth/page-title/permission', true);
        
        \Lang::load('auth/breadcrumb/auth', true);
        \Lang::load('auth/breadcrumb/group', true);
        \Lang::load('auth/breadcrumb/permission', true);
        \Lang::load('auth/breadcrumb/role', true);
        \Lang::load('auth/breadcrumb/user', true);
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions', __('auth.breadcrumb.permission.section'));
    }
    
    public function action_index()
    {
        static::restrict('permission.admin[index]');
        
        // \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/in', __('auth.breadcrumb.permission.index'));
        
        $this->view = static::$theme
            ->view('admin/permissions/index');
    }
    
    
    public function get_groups($id = null)
    {
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/groups', __('auth.breadcrumb.permission.groups'));
        
        if ( $id ) 
        {
            return $this->list_permissions('group', $id);
        }
        
        return $this->choice('groups');
    }
    
    
    public function get_roles($id = null)
    {
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/roles', __('auth.breadcrumb.permission.roles'));
        
        if ( $id ) 
        {
            return $this->list_permissions('role', $id);
        }
        
        return $this->choice('roles');
    }
    
    
    public function get_users($id = null)
    {
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/users', __('auth.breadcrumb.permission.users'));
        
        if ( $id ) 
        {
            return $this->list_permissions('user', $id);
        }
        
        return $this->choice('users');
    }
    
    
    protected function list_permissions($scope, $id)
    {
        switch ( $scope )
        {
            default:
                throw new \HttpServerErrorException();
            break;
            
            case 'user':
                
            break;
            
            case 'role':
                
            break;
            
            case 'group':
                
            break;
        }
        
        $this->view = static::$theme
            ->view('admin/permissions/list')
            ->set('table', $table, false);
    }
    
}
