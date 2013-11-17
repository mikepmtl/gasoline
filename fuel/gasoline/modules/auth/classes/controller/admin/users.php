<?php namespace Auth\Controller;

class Admin_Users extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('user', true);
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users', __('user.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('users.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/list', __('user.breadcrumb.list'));
        
        // $pagination_config = array(
        //     'pagination_url' => \Uri::create('admin/auth/users'),
        //     'total_items'    => \Model\Auth_User::count(),
        //     'per_page'       => \Input::get('per_page', 5),
        //     'uri_segment'    => 'page',
        // );
        
        // Create a pagination instance named 'mypagination'
        // $pagination = \Pagination::forge('todo', $pagination_config);
        
        $limit = filter_var(
            \Input::get(
                'per_page',
                5
            ),
            FILTER_SANITIZE_NUMBER_INT,
            array(
                'options' => array(
                    'default'   => 5,
                    'min_range' => 0
                )
            )
        );
        
        $page = filter_var(
            \Input::get(
                'page',
                1
            ),
            FILTER_SANITIZE_NUMBER_INT,
            array(
                'options' => array(
                    'default'   => 1,
                    'min_range' => 0
                )
            )
        );
        
        $offset = ( $page - 1 ) * $limit;
        
        $users = \Model\Auth_User::query()
            ->limit($limit)
            ->offset($offset)
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array(
            '',
            __('auth.model.user.username'),
            __('auth.model.user.email'),
            __('global.tools'),
        ));
        
        if ( $users )
        {
            foreach ( $users as &$user )
            {
                $row = $table->get_body()->add_row();
                $cbx = new \Gasform\Input_Checkbox('user_id', array(), $user->id);
                
                $row->set_meta('user', $user)
                    ->add_cell($cbx)
                    ->add_cell( \Auth::has_access('users.admin[read]') ? \Html::anchor('admin/auth/users/details/' . $user->id, e($user->username)) : e($user->username) )
                    ->add_cell(e($user->email))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/users/list')
            ->set('users', $users)
            // ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function action_create()
    {
        static::restrict('users.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/create', __('user.breadcrumb.create'));
        
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
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Created!')->is_flash());
                    
                    return \Response::redirect('admin/auth/users/details/' . $user_id);
                }
                else
                {
                    \Message\Container::instance()->set(null, new \Message\Item('danger', 'No!', 'Failed!'));
                }
            }
            else
            {
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
                
                \Message\Container::instance()->set(null, new \Message\Item('warning', 'No!', 'Validation Failed!'));
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/update', __('user.breadcrumb.update'));
        
        if ( ! ( $user = \Model\Auth_User::find($id) ) )
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
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Updated!')->is_flash());
                    
                    return \Response::redirect('admin/auth/users/details/' . $user->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation failed!'));
                    
                    die('orm validation failed');
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'General error!'));
                    
                    die('some other error');
                }
            }
            else
            {
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
                
                \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation failed!'));
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
            ->where('id', '=', $id);
        
        if ( ! ( $user = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/', __('user.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/users/details/' . $user->id, e($user->username));
        
        $this->view = static::$theme
            ->view('admin/users/details')
            ->set('user', $user);
    }
    
}
