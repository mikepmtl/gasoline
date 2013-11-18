<?php namespace Auth\Controller;

class Admin_Permissions extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Lang::load('auth/page-title/permission', true);
        
        \Lang::load('auth/permission', 'auth.permission');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions', __('auth.permission.breadcrumb.section'));
    }
    
    public function action_index()
    {
        static::restrict('permission.admin[index]');
        
        // \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/index', __('auth.permission.breadcrumb.index'));
        
        $this->view = static::$theme
            ->view('admin/permissions/index');
    }
    
    
    public function get_groups($id = null)
    {
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/groups', __('auth.permission.breadcrumb.groups'));
        
        if ( $id ) 
        {
            return $this->list_permissions('group', $id);
        }
        
        return $this->choice('groups');
    }
    
    
    public function get_roles($id = null)
    {
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/roles', __('auth.permission.breadcrumb.roles'));
        
        if ( $id ) 
        {
            return $this->list_permissions('role', $id);
        }
        
        return $this->choice('roles');
    }
    
    
    public function get_users($id = null)
    {
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/users', __('auth.permission.breadcrumb.users'));
        
        if ( $id ) 
        {
            return $this->list_permissions('user', $id);
        }
        
        return $this->choice('users');
    }
    
    
    protected function choice($scope)
    {
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array());
        
        switch ( $scope )
        {
            default:
                throw new \HttpNotFoundException();
            break;
            
            case 'users':
                $users = \Model\Auth_User::find('all');
                
                foreach ( $users as &$user )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('data', $user)
                        ->add_cell('')
                        ->add_cell(\Html::anchor('admin/auth/permissions/users/' . $user->id, e($user->username)));
                }
            break;
            
            case 'roles':
                $roles = \Model\Auth_Role::find('all');
                
                foreach ( $roles as &$role )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('data', $role)
                        ->add_cell('')
                        ->add_cell(\Html::anchor('admin/auth/permissions/roles' . $role->id, e($role->name)));
                }
            break;
            
            case 'groups':
                $groups = \Model\Auth_Group::find('all');
                
                foreach ( $groups as &$group )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('data', $group)
                        ->add_cell('')
                        ->add_cell(\Html::anchor('admin/auth/permissions/groups' . $group->id, e($group->name)));
                }
            break;
        }
        
        $this->view = static::$theme
            ->view('admin/permissions/choice')
            ->set('table', $table, false)
            ->set('scope', $scope);
    }
    
    
    protected function list_permissions($scope, $id)
    {
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array());
        
        switch ( $scope )
        {
            default:
                throw new \HttpNotFoundException();
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
