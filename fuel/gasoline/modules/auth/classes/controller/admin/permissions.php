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

class Admin_Permissions extends \Controller\Admin {
    
    public function before()
    {
        parent::before();
        
        \Lang::load('navigation', 'auth.navigation');
        \Lang::load('navigation/permission', 'auth.navigation.permission');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.navigation.breadcrumb._section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions', __('auth.navigation.permission.breadcrumb._section'));
    }
    
    public function action_index()
    {
        static::restrict('permission.admin[index]');
        
        // \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/index', __('auth.navigation.permission.breadcrumb.index'));
        
        $this->view = static::$theme
            ->view('admin/permissions/index');
    }
    
    
    public function get_groups($id = null)
    {
        \Lang::load('navigation/group', 'auth.navigation.group');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/groups', __('auth.navigation.group.breadcrumb._section'));
        
        if ( $id ) 
        {
            return $this->list_permissions('group', $id);
        }
        
        return $this->choice('groups');
    }
    
    
    public function get_roles($id = null)
    {
        \Lang::load('navigation/roles', 'auth.navigation.role');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/roles', __('auth.navigation.role.breadcrumb._section'));
        
        if ( $id ) 
        {
            return $this->list_permissions('role', $id);
        }
        
        return $this->choice('roles');
    }
    
    
    public function get_users($id = null)
    {
        \Lang::load('navigation/user', 'auth.navigation.user');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/users', __('auth.navigation.user.breadcrumb._section'));
        
        if ( $id ) 
        {
            return $this->list_permissions('user', $id);
        }
        
        return $this->choice('users');
    }
    
    
    public function post_users()
    {
        return $this->set_permissions('user', \Input::post('user_id'));
    }
    
    
    public function post_roles()
    {
        return $this->set_permissions('role', \Input::post('role_id'));
    }
    
    
    public function post_groups()
    {
        return $this->set_permissions('group', \Input::post('group_id'));
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
                        ->add_cell(\Html::anchor('admin/auth/permissions/users/' . $user->username, e($user->username)));
                }
            break;
            
            case 'roles':
                $roles = \Model\Auth_Role::find('all', array('where' => array(array('filter', '=', ''))));
                
                foreach ( $roles as &$role )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('data', $role)
                        ->add_cell('')
                        ->add_cell(\Html::anchor('admin/auth/permissions/roles/' . $role->slug, e($role->name)));
                }
            break;
            
            case 'groups':
                $groups = \Model\Auth_Group::find('all');
                
                foreach ( $groups as &$group )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('data', $group)
                        ->add_cell('')
                        ->add_cell(\Html::anchor('admin/auth/permissions/groups/' . $group->slug, e($group->name)));
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
        
        $table = \Table\Table::forge()->headers(array(
            'Area',
            'Permission',
            'Actions',
        ));
        
        switch ( $scope )
        {
            default:
                throw new \HttpNotFoundException();
            break;
            
            case 'user':
                $query = \Model\Auth_User::query()
                    ->related('userpermissions')
                    ->related('userpermissions.permission')
                    ->and_where_open()
                        ->where('id', '=', $id)
                        ->or_where('username', '=', $id)
                    ->and_where_close();
                
                if ( ! $user = $query->get_one() )
                {
                    throw new \HttpNotFoundException();
                }
                
                \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/' . $scope . '/' . $user->id, e($user->username));
                
                $perms = \Model\Auth_Permission::query()
                    ->get();
                
                foreach ( $perms as &$perm )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('permission', $perm)
                        ->add_cell(e($perm->area))
                        ->add_cell(e($perm->permission))
                        ->add_cell(function() use ($perm, $user) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $cbx = new \Gasform\Input_Checkbox('permission[' . $perm->id . ']', array(), $k);
                                $perms[] = $cbx->render(new \Gasform\Render_Simple) . '&nbsp' . __('auth.permission.permissions.' . $perm->area . '.' . $perm->permission . '.' . $action);
                            }
                            
                            return implode('<br />', $perms);
                        });
                }
                
                $data = $user;
            break;
            
            case 'role':
                $query = \Model\Auth_Role::query()
                    ->related('rolepermissions')
                    ->related('rolepermissions.permission')
                    ->and_where_open()
                        ->where('id', '=', $id)
                        ->or_where('slug', '=', $id)
                    ->and_where_close();
                
                if ( ! $role = $query->get_one() )
                {
                    throw new \HttpNotFoundException();
                }
                
                \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/' . $scope . '/' . $role->id, e($role->name));
                
                if ( $role->filter )
                {
                    \Message\Container::push(\Message\Item::forge('success', 'Role ' . e($role->name) . ' has filter '. $role->filter . ' applied, therefore cannot have permissions set', 'Not possible')->is_flash(true));
                    
                    return \Response::redirect_back('admin/auth/permissions/roles');
                }
                
                $perms = \Model\Auth_Permission::query()
                    ->get();
                
                foreach ( $perms as &$perm )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('permission', $perm)
                        ->add_cell(e($perm->area))
                        ->add_cell(e($perm->permission))
                        ->add_cell(function() use ($perm, $role) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $cbx = new \Gasform\Input_Checkbox('permission[' . $perm->id . ']', array(), $k);
                                $perms[] = $cbx->render(new \Gasform\Render_Simple) . '&nbsp' . __('auth.permission.permissions.' . $perm->area . '.' . $perm->permission . '.' . $action);
                            }
                            
                            return implode('<br />', $perms);
                        });
                }
                
                $data = $role;
            break;
            
            case 'group':
                $query = \Model\Auth_Group::query()
                    ->related('grouppermissions')
                    ->related('grouppermissions.permission')
                    ->and_where_open()
                        ->where('id', '=', $id)
                        ->or_where('slug', '=', $id)
                    ->and_where_close();
                
                if ( ! $group = $query->get_one() )
                {
                    throw new \HttpNotFoundException();
                }
                
                \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/' . $scope . '/' . $group->id, e($group->name));
                
                $perms = \Model\Auth_Permission::query()
                    ->get();
                
                foreach ( $perms as &$perm )
                {
                    $row = $table->get_body()->add_row();
                    
                    $row->set_meta('permission', $perm)
                        ->add_cell(e($perm->area))
                        ->add_cell(e($perm->permission))
                        ->add_cell(function() use ($perm, $group) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $cbx = new \Gasform\Input_Checkbox('permission[' . $perm->id . ']', array(), $k);
                                $perms[] = $cbx->render(new \Gasform\Render_Simple) . '&nbsp' . __('auth.permission.permissions.' . $perm->area . '.' . $perm->permission . '.' . $action);
                            }
                            
                            return implode('<br />', $perms);
                        });
                }
                
                $data = $group;
            break;
        }
        
        $this->view = static::$theme
            ->view('admin/permissions/list')
            ->set('table', $table, false)
            ->set('scope', $scope)
            ->set('data', $data, false);
    }
    
    
    protected function set_permissions($scope, $id = null)
    {
        if ( ! $id )
        {
            throw new \HttpNotFoundException();
        }
        
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
    }
    
}

/* End of file permissions.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/permissions.php */
