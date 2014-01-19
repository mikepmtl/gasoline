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

class Admin_Users extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('navigation', 'auth.navigation');
        \Lang::load('navigation/admin/user', 'auth.navigation.admin.user');
        \Lang::load('messages/user', 'auth.messages.user');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.navigation.breadcrumb._section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users', __('auth.navigation.admin.user.breadcrumb._section'));
    }
    
    public function action_list()
    {
        static::restrict('auth.admin:users[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/list', __('auth.navigation.admin.user.breadcrumb.list'));
        
        $pagination_config = array(
            'pagination_url'    => \Uri::create('admin/auth/users'),
            'total_items'       => ( \Model\Auth_User::count() - 1 ),
            'per_page'          => 15,
            'uri_segment'       => 'page',
            'name'              => 'todo-sm',
        );
        
        // Create a pagination instance named 'mypagination'
        $pagination = \Pagination::forge('users-pagination', $pagination_config);
        
        $users = \Model\Auth_User::query()
            ->where('id', '!=', '0')
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->related('roles')
            ->related('group')
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->add_header(array(
            html_tag('input', array('type' => 'checkbox')),
            __('auth.model.user.username'),
            __('auth.model.user.email'),
            __('global.tools'),
        ));
        
        if ( $users )
        {
            foreach ( $users as &$user )
            {
                $row = \Table\Row::forge()
                    ->set_meta('user', $user);
                
                $row['cbx']         = \Table\Cell::forge(\Gasform\Input_Checkbox::forge('user_id[]', $user->id, array()));
                $row['username']    = \Table\Cell::forge( \Auth::has_access('auth.admin:users[read]') ? \Html::anchor('admin/auth/users/details/' . $user->username, e($user->username)) : e($user->username) );
                $row['email']       = \Table\Cell::forge(e($user->email));
                $row['actions']     = \Table\Cell::forge('');
                
                $table[$user->id] = $row;
            }
        }
        
        $form = new \Gasform\Form(\Uri::create('admin/auth/users/action'));
        $bulk_actions = new \Gasform\Input_Select();
        
        $bulk = new \Gasform\Input_Option(__('global.bulk_actions'), '', array());
        $bulk_actions['bulk'] = $bulk;
        
        $delete = new \Gasform\Input_Option(__('button.delete'), 'delete', array());
        $bulk_actions['delete'] = $delete;
        
        $form['bulk_action'] = $bulk_actions->set_name('action');
        
        $submit = new \Gasform\Input_Submit('submit', __('button.submit'), array());
        $form['submit'] = $submit;
        
        $this->view = static::$theme
            ->view('admin/users/list')
            ->set('users', $users)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false)
            ->set('form', $form, false);
    }
    
    
    public function action_create()
    {
        static::restrict('auth.admin:users[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/create', __('auth.navigation.admin.user.breadcrumb.create'));
        
        $user = \Model\Auth_User::forge();
        
        $form = $user->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = $form->forge_validation();
            
            $form->repopulate(\Input::post());
            
            if ( $val->run() )
            {
                try
                {
                    $user = \Model\Auth_User::forge(array(
                        'username'  => $val->validated('username'),
                        'email'     => $val->validated('email'),
                        'group_id'  => $val->validated('group_id'),
                        'password'  => \Auth::hash_password($val->validated('password')),
                    ));
                    
                    if ( $roles = \Input::post('roles', \Input::post('role_ids', \Input::post('role_id', false))) )
                    {
                        try
                        {
                            $roles = \Model\Auth_Role::query()
                                ->where('id', 'IN', $roles)
                                ->get();
                            
                            $user->roles = $roles;
                        }
                        catch ( \Exception $e ) {}
                    }
                    
                    $user->save();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.user.create.success.message', array('username' => e($user->username))), __('auth.messages.user.create.success.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/users/details/' . $user->username);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('warning', __('auth.messages.user.validation_failed.message'), __('auth.messages.user.validation_failed.heading')));
                    
                    $form->set_errors($e->get_fieldset());
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_DEBUG, $e->getMessage());
                    
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('danger', __('auth.messages.user.create.failure.message', array('username' => $user->username)), __('auth.messages.user.create.failure.heading')));
                }
            }
            else
            {
                \Message\Container::instance('user-form')->push(\Message\Item::forge('warning', __('auth.messages.user.validation_failed.message'), __('auth.messages.user.validation_failed.heading')));
                
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', __('button.create'), array());
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/users/_form')
            ->set('action', 'create')
            ->set('form', $form)
            ->set('user', $user);
    }
    
    
    public function action_update($id)
    {
        static::restrict('auth.admin:users[update]');
        
        $query = \Model\Auth_User::query()
            ->where('id', '!=', '0')
            ->related('group')
            ->related('auditor')
            ->related('metadata')
            ->related('userpermissions')
            ->related('roles')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('username', '=', $id)
            ->and_where_close();
        
        if ( ! ( $user = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/update', __('auth.navigation.admin.user.breadcrumb.update'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/update/' . $id, e($user->username));
        
        $form = $user->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = $form->forge_validation();
            
            $form->repopulate(\Input::post());
            
            if ( $val->run() )
            {
                try
                {
                    $user->from_array(array(
                        'username'  => $val->validated('username'),
                        'group_id'  => $val->validated('group_id'),
                        'email'     => $val->validated('email'),
                    ));
                    
                    if ( $val->validated('password') )
                    {
                        $user->set('password', \Auth::hash_password($val->validated('password')));
                    }
                    
                    if ( $roles = \Input::post('roles', \Input::post('role_ids', \Input::post('role_id', false))) )
                    {
                        is_array($roles) OR $roles = (array) $roles;
                        
                        foreach ( $user->roles as $id => $role )
                        {
                            if ( ! in_array($id, $roles) )
                            {
                                unset($user->roles[$id]);
                            }
                            else
                            {
                                unset($roles[array_search($id, $roles)]);
                            }
                        }
                        
                        if ( $roles )
                        {
                            try
                            {
                                $roles = \Model\Auth_Role::query()
                                    ->where('id', 'IN', $roles)
                                    ->get();
                                
                                foreach ( $roles as $role )
                                {
                                    $user->roles[] = $role;
                                }
                            }
                            catch ( \Exception $e ) {}
                        }
                    }
                    else
                    {
                        unset($user->roles);
                    }
                    
                    $user->save();
                    
                    try
                    {
                        \Cache::delete(\Config::get('ormauth.cache_prefix', 'auth').'.permissions.user_' . $user->id);
                    }
                    catch ( \Exception $e ) {}
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.user.update.success.message', array('username' => e($user->username))), __('auth.messages.user.update.success.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/users/details/' . $user->username);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('warning', __('auth.messages.user.validation_failed.message'), __('auth.messages.user.validation_failed.heading')));
                    
                    $form->set_errors($e->get_fieldset());
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_DEBUG, $e->getMessage());
                    
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('danger', __('auth.messages.user.update.failure.message', array('username' => $user->username)), __('auth.messages.user.update.failure.heading')));
                }
            }
            else
            {
                \Message\Container::instance('user-form')->push(\Message\Item::forge('warning', __('auth.messages.user.validation_failed.message'), __('auth.messages.user.validation_failed.heading')));
                
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', __('button.update'), array());
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/users/_form')
            ->set('action', 'update')
            ->set('form', $form)
            ->set('user', $user);
    }
    
    
    public function action_delete($id)
    {
        static::restrict('auth.admin:users[delete]');
        
        $query = \Model\Auth_User::query()
            ->where('id', '!=', '0')
            ->related('group')
            ->related('auditor')
            ->related('metadata')
            ->related('userpermissions')
            ->related('roles')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('username', '=', $id)
            ->and_where_close();
        
        if ( ! ( $user = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        if ( ! $user->is_deletable() )
        {
            return \Response::redirect_back('admin/auth/users');
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/delete', __('auth.navigation.admin.user.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/delete/' . $user->username, e($user->username));
        
        $form = $user->to_form();
        
        $form->disable_fields();
        
        if ( \Input::method() === "POST" )
        {
            if ( \Input::post('confirm') === 'yes' )
            {
                try
                {
                    $username = $user->username;
                    $user->delete();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.user.delete.success.message', array('username' => e($username))), __('auth.messages.user.delete.success.heading'))->is_flash(true));
                    
                    try
                    {
                        \Cache::delete(\Config::get('ormauth.cache_prefix', 'auth').'.permissions.user_' . $user->id);
                    }
                    catch ( \Exception $e ) {}
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_INFO, $e->getMessage(), __METHOD__);
                    
                    \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.user.delete.failure.message', array('username' => e($user->username))), __('auth.messages.user.delete.failure.heading'))->is_flash(true));
                }
            }
            else
            {
                \Message\Container::push(\Message\Item::forge('warning', __('auth.messages.user.warning.delete.message', array('username' => e($user->username))), __('auth.messages.user.warning.delete.heading'))->is_flash(true));
            }
            
            return \Response::redirect('admin/auth/users');
        }
        
        $cbx_group = \Gasform\Input_CheckboxGroup::forge();
        $cbx_group['yes'] = \Gasform\Input_Checkbox::forge('confirm', 'yes', array())
            ->set_label(__('global.confirm_delete'));
        $form['confirm'] = $cbx_group
            ->set_label(__('global.confirmation'))
            ->allow_multiple(false);
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $btn_group['submit'] = \Gasform\Input_Submit::forge('submit', __('button.delete'), array());
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/users/_form')
            ->set('action', 'delete')
            ->set('form', $form)
            ->set('user', $user);
    }
    
    
    public function action_details($id)
    {
        static::restrict('auth.admin:users[read]');
        
        $query = \Model\Auth_User::query()
            ->where('id', '!=', '0')
            ->related('group')
            ->related('auditor')
            ->related('metadata')
            ->related('userpermissions')
            ->related('roles')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('username', '=', $id)
            ->and_where_close();
        
        if ( ! ( $user = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/', __('auth.navigation.admin.user.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/details/' . $user->username, e($user->username));
        
        $this->view = static::$theme
            ->view('admin/users/details')
            ->set('user', $user);
    }
    
    
    public function post_action()
    {
        switch ( \Input::post('action', false) )
        {
            default:
                throw new \HttpNotFoundException();
            
            break;
            case 'delete':
                static::restrict('auth.admin:users[delete]');
                
                if ( $ids = \Input::post('user_id', \Input::post('user_ids', false)) )
                {
                    is_array($ids) OR $ids = array($ids);
                    
                    list(, $me_id) = \Auth::get_user_id();
                    
                    if ( false !== ( $key = array_search($me_id, $ids) ) )
                    {
                        unset($ids[$key]);
                    }
                    
                    if ( ! $ids )
                    {
                        break;
                    }
                    
                    try
                    {
                        $users = \Model\Auth_User::query()
                            ->where('id', '!=', '0')
                            ->where('id', 'IN', $ids)
                            ->related('metadata')
                            ->get();
                    }
                    catch ( \Exception $e )
                    {
                        break;
                    }
                    
                    if ( ! $users )
                    {
                        break;
                    }
                    
                    $success = $failed = array();
                    
                    foreach ( $users as &$user )
                    {
                        if ( ! $user->is_deletable() )
                        {
                            continue;
                        }
                        
                        try
                        {
                            $username = $user->username;
                            
                            $user->delete();
                            
                            $success[] = e($username);
                            
                            try
                            {
                                \Cache::delete(\Config::get('ormauth.cache_prefix', 'auth').'.permissions.user_' . $user->id);
                            }
                            catch ( \Exception $e ) {}
                        }
                        catch ( \Exception $e )
                        {
                            $failed[] = e($user->username);
                        }
                    }
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('auth.messages.user.delete_batch.success.message', array('usernames' => implode(', ', $success))), __('auth.messages.user.delete_batch.success.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.user.delete_batch.failure.message', array('usernames' => implode(', ', $failed))), __('auth.messages.user.delete_batch.failure.heading'))->is_flash(true));
                }
            break;
        }
        
        return \Response::redirect('admin/auth/users');
    }
    
}

/* End of file users.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/users.php */
