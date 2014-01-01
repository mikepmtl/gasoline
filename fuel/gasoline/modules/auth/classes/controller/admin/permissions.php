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
        \Lang::load('navigation/admin/permission', 'auth.navigation.admin.permission');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.navigation.breadcrumb._section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions', __('auth.navigation.admin.permission.breadcrumb._section'));
    }
    
    public function action_index()
    {
        static::restrict('permission.admin[index]');
        
        // \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/index', __('auth.navigation.admin.permission.breadcrumb.index'));
        
        $this->view = static::$theme
            ->view('admin/permissions/index');
    }
    
    
    public function get_groups($id = null)
    {
        \Lang::load('navigation/admin/group', 'auth.navigation.admin.group');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/groups', __('auth.navigation.admin.group.breadcrumb._section'));
        
        if ( $id ) 
        {
            return $this->list_permissions('group', $id);
        }
        
        return $this->choice('groups');
    }
    
    
    public function get_roles($id = null)
    {
        \Lang::load('navigation/admin/role', 'auth.navigation.admin.role');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/roles', __('auth.navigation.admin.role.breadcrumb._section'));
        
        if ( $id ) 
        {
            return $this->list_permissions('role', $id);
        }
        
        return $this->choice('roles');
    }
    
    
    public function get_users($id = null)
    {
        \Lang::load('navigation/admin/user', 'auth.navigation.admin.user');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/users', __('auth.navigation.admin.user.breadcrumb._section'));
        
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
        
        $table = \Table\Table::forge();
        $data = array();
        
        switch ( $scope )
        {
            default:
                throw new \HttpNotFoundException();
            break;
            
            case 'users':
                $users = $data = \Model\Auth_User::find('all');
                
                foreach ( $users as &$user )
                {
                    $row = \Table\Row::forge()
                        ->set_meta('data', $user);
                    
                    $row['name'] = \Table\Cell::forge(\Html::anchor('admin/auth/permissions/users/' . $user->username, e($user->username)));
                    
                    $table[$user->id] = $row;
                }
            break;
            
            case 'roles':
                $roles = $data = \Model\Auth_Role::find('all', array('where' => array(array('filter', '=', ''))));
                
                foreach ( $roles as &$role )
                {
                    $row = \Table\Row::forge()
                        ->set_meta('data', $role);
                    
                    $row['name'] = \Table\Cell::forge(\Html::anchor('admin/auth/permissions/roles/' . $role->name, e($role->name)));
                    
                    $table[$role->id] = $row;
                }
            break;
            
            case 'groups':
                $groups = $data = \Model\Auth_Group::find('all');
                
                foreach ( $groups as &$group )
                {
                    $row = \Table\Row::forge()
                        ->set_meta('data', $group);
                    
                    $row['name'] = \Table\Cell::forge(\Html::anchor('admin/auth/permissions/groups/' . $group->name, e($group->name)));
                    
                    $table[$group->id] = $row;
                }
            break;
        }
        
        $this->view = static::$theme
            ->view('admin/permissions/choice')
            ->set('table', $table, false)
            ->set('scope', $scope)
            ->set('data', $data);
    }
    
    
    /**
     * [list_permissions description]
     * 
     * @todo    Update this to load the lang lines per permission from the module
     *          sth like \Lang::load($perm->area . '::permissions');
     * 
     * @param  [type] $scope [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    protected function list_permissions($scope, $id)
    {
        \Package::load('table');
        
        $table = \Table\Table::forge()->add_header(array(
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
                
                $render_simple = new \Gasform\Render_Simple();
                
                foreach ( $perms as &$perm )
                {
                    $row = \Table\Row::forge()
                        ->set_meta('permission', $perm);
                    
                    $row['area']        = \Table\Cell::forge(e($perm->area));
                    $row['permission']  = \Table\Cell::forge(e($perm->permission));
                    $row['actions']     = \Table\Cell::forge(function() use ($render_simple, $perm, $user) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $perms[] = $render_simple->render(\Gasform\Input_Checkbox::forge('permission[' . $perm->id . ']', $k)->set_checked( (bool) array_key_exists('[' . $user->id . '][' . $perm->id . ']', $user->userpermissions) ))
                                    . '&nbsp;'
                                    . __('auth.permissions.' . $perm->area . '.' . $perm->permission . '.' . $action);
                            }
                            
                            return implode('<br />', $perms);
                        });
                    
                    $table[$perm->id] = $row;
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
                
                $render_simple = new \Gasform\Render_Simple();
                
                foreach ( $perms as &$perm )
                {
                    \Module::exists($perm->area) && \Module::load($perm->area);
                    
                    $lang = ( \Lang::load($perm->area . '::permissions', 'auth.permissions') ? : \Lang::load('permissions.' . $perm->area, 'auth.permissions') );
                    
                    $row = \Table\Row::forge()
                        ->set_meta('permission', $perm);
                    
                    $row['area']        = \Table\Cell::forge(e($perm->area));
                    $row['permission']  = \Table\Cell::forge(e($perm->permission));
                    $row['actions']     = \Table\Cell::forge(function() use ($render_simple, $perm, $role) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $perms[] = $render_simple->render(\Gasform\Input_Checkbox::forge('permission[' . $perm->id . ']', $k)->set_checked( (bool) array_key_exists('[' . $role->id . '][' . $perm->id . ']', $role->rolepermissions) ))
                                    . '&nbsp;'
                                    . __('auth.permissions.' . $perm->area . '.' . $perm->permission . '.' . $action);
                            }
                            
                            return implode('<br />', $perms);
                        });
                    
                    $table[$perm->id] = $row;
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
                
                $render_simple = new \Gasform\Render_Simple();
                
                foreach ( $perms as &$perm )
                {
                    $row = \Table\Row::forge()
                        ->set_meta('permission', $perm);
                    
                    $row['area']        = \Table\Cell::forge(e($perm->area));
                    $row['permission']  = \Table\Cell::forge(e($perm->permission));
                    $row['actions']     = \Table\Cell::forge(function() use ($render_simple, $perm, $group) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $perms[] = $render_simple->render(\Gasform\Input_Checkbox::forge('permission[' . $perm->id . ']', $k)->set_checked( (bool) array_key_exists('[' . $group->id . '][' . $perm->id . ']', $group->grouppermissions) ))
                                    . '&nbsp;'
                                    . __('auth.permissions.' . $perm->area . '.' . $perm->permission . '.' . $action);
                            }
                            
                            return implode('<br />', $perms);
                        });
                    
                    $table[$perm->id] = $row;
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
