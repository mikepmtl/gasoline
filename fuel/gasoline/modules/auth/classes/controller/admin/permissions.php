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
        static::restrict('auth.admin:permissions');
        
        // \Breadcrumb\Container::instance()->set_crumb('admin/auth/permissions/index', __('auth.navigation.admin.permission.breadcrumb.index'));
        
        $this->view = static::$theme
            ->view('admin/permissions/index');
    }
    
    
    public function get_groups($id = null)
    {
        static::restrict('auth.admin:permissions[group]');
        
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
        static::restrict('auth.admin:permissions[role]');
        
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
        static::restrict('auth.admin:permissions[user]');
        
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
        static::restrict('auth.admin:permissions[user]');
        
        return $this->set_permissions('user', \Input::post('user_id'));
    }
    
    
    public function post_roles()
    {
        static::restrict('auth.admin:permissions[role]');
        
        return $this->set_permissions('role', \Input::post('role_id'));
    }
    
    
    public function post_groups()
    {
        static::restrict('auth.admin:permissions[group]');
        
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
                $users = $data = \Model\Auth_User::find('all', array('where' => array(array('id', '!=', '0'))));
                
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
                    ->where('id', '!=', '0')
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
                    \Module::exists($perm->area) && \Module::load($perm->area);
                    
                    $lang = ( \Lang::load($perm->area . '::permissions', 'auth.permissions') ? : \Lang::load('permissions/' . $perm->area, 'auth.permissions') );
                    $row = \Table\Row::forge()
                        ->set_meta('permission', $perm);
                    
                    $row['area']        = \Table\Cell::forge(e($perm->area));
                    $row['permission']  = \Table\Cell::forge(e($perm->permission));
                    $row['actions']     = \Table\Cell::forge(function() use ($render_simple, $perm, $user) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $perms[] = $render_simple->render(\Gasform\Input_Checkbox::forge('permission[' . $perm->id . '][]', $k)->set_checked( (bool) array_key_exists($key = '[' . $user->id . '][' . $perm->id . ']', $user->userpermissions) && in_array($k, $user->userpermissions[$key]->actions) ))
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
                    
                    $lang = ( \Lang::load($perm->area . '::permissions', 'auth.permissions') ? : \Lang::load('permissions/' . $perm->area, 'auth.permissions') );
                    
                    $row = \Table\Row::forge()
                        ->set_meta('permission', $perm);
                    
                    $row['area']        = \Table\Cell::forge(e($perm->area));
                    $row['permission']  = \Table\Cell::forge(e($perm->permission));
                    $row['actions']     = \Table\Cell::forge(function() use ($render_simple, $perm, $role) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $perms[] = $render_simple->render(\Gasform\Input_Checkbox::forge('permission[' . $perm->id . '][]', $k)->set_checked( (bool) array_key_exists($key = '[' . $role->id . '][' . $perm->id . ']', $role->rolepermissions) && in_array($k, $role->rolepermissions[$key]->actions) ))
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
                    \Module::exists($perm->area) && \Module::load($perm->area);
                    
                    $lang = ( \Lang::load($perm->area . '::permissions', 'auth.permissions') ? : \Lang::load('permissions/' . $perm->area, 'auth.permissions') );
                    
                    $row = \Table\Row::forge()
                        ->set_meta('permission', $perm);
                    
                    $row['area']        = \Table\Cell::forge(e($perm->area));
                    $row['permission']  = \Table\Cell::forge(e($perm->permission));
                    $row['actions']     = \Table\Cell::forge(function() use ($render_simple, $perm, $group) {
                            $perms = array();
                            
                            foreach ( $perm->actions as $k => $action )
                            {
                                $perms[] = $render_simple->render(\Gasform\Input_Checkbox::forge('permission[' . $perm->id . '][]', $k)->set_checked( (bool) array_key_exists($key = '[' . $group->id . '][' . $perm->id . ']', $group->grouppermissions) && in_array($k, $group->grouppermissions[$key]->actions) ))
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
        if ( ! $id OR ( \Input::post($scope . '_id') != $id ) )
        {
            throw new \HttpNotFoundException();
        }
        
        $new_perms = \Input::post('permission', \Input::post('permissions', array()));
        
        $redirect = '';
        
        switch ( $scope )
        {
            default:
                throw new \HttpNotFoundException();
            break;
            
            case 'user':
                $query = \Model\Auth_User::query()
                    ->related('userpermissions')
                    ->related('userpermissions.permission')
                    ->where('id', '!=', '0')
                    ->where('id', '=', $id);
                
                if ( ! $user = $query->get_one() )
                {
                    throw new \HttpNotFoundException();
                }
                
                if ( $user->userpermissions )
                {
                    foreach ( $user->userpermissions as $userpermission )
                    {
                        // Permission is still set?
                        if ( in_array($userpermission->perms_id, $new_perms) )
                        {
                            // Then just update the assigned actions
                            $userpermission->actions = $new_perms[$userpermission->perms_id];
                        }
                        // Permission was revoked
                        else
                        {
                            unset($new_perms[array_search($userpermission->perms_id, $new_perms)]);
                            
                            $userpermission->delete();
                        }
                    }
                }
                
                if ( $new_perms )
                {
                    foreach ( $new_perms as $perm_id => $_new_perms )
                    {
                        $userpermission = \Model\Auth_Userpermission::forge(array(
                            'user_id'   => $id,
                            'perms_id'  => $perm_id,
                            'actions'   => $_new_perms,
                        ));
                        
                        $userpermission->save();
                    }
                }
                
                try
                {
                    \Cache::delete(\Config::get('gasauth.cache_prefix', 'auth').'.permissions.user_' . $user->id);
                }
                catch ( \Exception $e ) {}
                
                $redirect = '/users/' . $user->username;
            break;
            
            case 'role':
                $query = \Model\Auth_Role::query()
                    ->related('rolepermissions')
                    ->related('rolepermissions.permission')
                    ->where('id', '!=', '0')
                    ->where('id', '=', $id);
                
                if ( ! $role = $query->get_one() )
                {
                    throw new \HttpNotFoundException();
                }
                
                if ( $role->rolepermissions )
                {
                    foreach ( $role->rolepermissions as $rolepermission )
                    {
                        // Permission is still set?
                        if ( in_array($rolepermission->perms_id, $new_perms) )
                        {
                            // Then just update the assigned actions
                            $rolepermission->actions = $new_perms[$rolepermission->perms_id];
                        }
                        // Permission was revoked
                        else
                        {
                            unset($new_perms[array_search($rolepermission->perms_id, $new_perms)]);
                            
                            $rolepermission->delete();
                        }
                    }
                }
                
                if ( $new_perms )
                {
                    foreach ( $new_perms as $perm_id => $_new_perms )
                    {
                        $rolepermission = \Model\Auth_Rolepermission::forge(array(
                            'role_id'   => $id,
                            'perms_id'  => $perm_id,
                            'actions'   => $_new_perms,
                        ));
                        
                        $rolepermission->save();
                    }
                }
                
                $redirect = '/roles/' . $role->name;
            break;
            
            case 'group':
                $query = \Model\Auth_Group::query()
                    ->related('grouppermissions')
                    ->related('grouppermissions.permission')
                    ->where('id', '!=', '0')
                    ->where('id', '=', $id);
                
                if ( ! $group = $query->get_one() )
                {
                    throw new \HttpNotFoundException();
                }
                
                if ( $group->grouppermissions )
                {
                    foreach ( $group->grouppermissions as $grouppermission )
                    {
                        // Permission is still set?
                        if ( in_array($grouppermission->perms_id, $new_perms) )
                        {
                            // Then just update the assigned actions
                            $grouppermission->actions = $new_perms[$grouppermission->perms_id];
                        }
                        // Permission was revoked
                        else
                        {
                            unset($new_perms[array_search($grouppermission->perms_id, $new_perms)]);
                            
                            $grouppermission->delete();
                        }
                    }
                }
                
                if ( $new_perms )
                {
                    foreach ( $new_perms as $perm_id => $_new_perms )
                    {
                        $grouppermission = \Model\Auth_Grouppermission::forge(array(
                            'group_id'   => $id,
                            'perms_id'  => $perm_id,
                            'actions'   => $_new_perms,
                        ));
                        
                        $grouppermission->save();
                    }
                }
                
                try
                {
                    \Cache::delete_all(\Config::get('ormauth.cache_prefix', 'auth').'.permissions');
                }
                catch ( \Exception $e ) {}
                
                $redirect = '/groups/' . $group->name;
            break;
        }
        
        return \Response::redirect_back('admin/auth/permissions' . $redirect);
    }
    
}

/* End of file permissions.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/permissions.php */
