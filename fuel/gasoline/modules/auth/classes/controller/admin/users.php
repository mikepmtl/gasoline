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
        
        \Lang::load('auth/user', 'auth.user');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users', __('auth.user.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('users.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/list', __('auth.user.breadcrumb.list'));
        
        $pagination_config = array(
            'pagination_url'    => \Uri::create('admin/auth/users'),
            'total_items'       => \Model\Auth_User::count(),
            'per_page'          => 15,
            'uri_segment'       => 'page',
            'name'              => 'todo-sm',
        );
        
        // Create a pagination instance named 'mypagination'
        $pagination = \Pagination::forge('users-pagination', $pagination_config);
        
        $users = \Model\Auth_User::query()
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->related('roles')
            ->related('group')
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array(
            html_tag('input', array('type' => 'checkbox')),
            __('auth.model.user.username'),
            __('auth.model.user.email'),
            __('global.tools'),
        ));
        
        if ( $users )
        {
            foreach ( $users as &$user )
            {
                $row = $table->get_body()->add_row();
                
                $row->set_meta('user', $user)
                    ->add_cell(new \Gasform\Input_Checkbox('user_id', array(), $user->id))
                    ->add_cell( \Auth::has_access('users.admin[read]') ? \Html::anchor('admin/auth/users/details/' . $user->id, e($user->username)) : e($user->username) )
                    ->add_cell(e($user->email))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/users/list')
            ->set('users', $users)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function action_create()
    {
        static::restrict('users.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/create', __('auth.user.breadcrumb.create'));
        
        $user = \Model\Auth_User::forge();
        
        $form = $user->to_form();
        
        $roles = \Model\Auth_Role::to_form_element();
        $form['roles'] = $roles->set_name('roles[]')->set_label(__('auth.model.user.roles'))->allow_multiple(true);
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($user);
            
            if ( $val->run() )
            {
                if ( $user_id = \Auth::create_user($val->validated('username'), $val->validated('password'), $val->validated('email'), $val->validated('group_id'), array()) )
                {
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.user.success.create.message', array('name' => e($user->username))), __('auth.messages.user.success.create.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/users/details/' . $user_id);
                }
                else
                {
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('danger', __('auth.messages.user.failure.create.message'), __('auth.messages.user.failure.create.heading')));
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
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.create'));
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
        static::restrict('users.admin[update]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/update', __('auth.user.breadcrumb.update'));
        
        $query = \Model\Auth_User::query()
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
        
        $form = $user->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($user);
            
            if ( $val->run() )
            {
                try
                {
                    // $user->from_array(array(
                    //     'name'      => $val->validated('name'),
                    //     'filter'    => $val->validated('filter'),
                    // ));
                    
                    $user->save();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.user.success.update.message', array('name' => e($user->username))), __('auth.messages.user.success.update.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/users/details/' . $user->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('warning', __('auth.messages.user.validation_failed.message'), __('auth.messages.user.validation_failed.heading')));
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance('user-form')->push(\Message\Item::forge('danger', __('auth.messages.user.failure.update.message'), __('auth.messages.user.failure.update.heading')));
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
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.update'));
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/users/_form')
            ->set('action', 'update')
            ->set('form', $form)
            ->set('user', $user);
    }
    
    
    public function action_details($id)
    {
        static::restrict('users.admin[read]');
        
        $query = \Model\Auth_User::query()
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/', __('auth.user.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/details/' . $user->id, e($user->username));
        
        $this->view = static::$theme
            ->view('admin/users/details')
            ->set('user', $user);
    }
    
}

/* End of file users.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/users.php */
