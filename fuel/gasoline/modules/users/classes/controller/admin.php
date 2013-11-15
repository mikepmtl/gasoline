<?php namespace Users\Controller;

class Admin extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('user', true);
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/users', __('user.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('users.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/users/list', __('user.breadcrumb.list'));
        
        // $pagination_config = array(
        //     'pagination_url' => \Uri::create('admin/users'),
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
                    ->add_cell( \Auth::has_access('users.admin[read]') ? \Html::anchor('admin/users/details/' . $user->id, e($user->username)) : e($user->username) )
                    ->add_cell(e($user->email))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/list')
            ->set('users', $users)
            // ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function action_create()
    {
        static::restrict('users.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/users/create', __('user.breadcrumb.create'));
        
        $user = \Model\Auth_User::forge();
        
        $form = $user->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($user);
            
            if ( $val->run() )
            {
                try
                {
                    $user->from_array(array(
                        'name'      => $val->validated('name'),
                        'filter'    => $val->validated('filter'),
                    ));
                    
                    $user->save();
                    
                    return \Response::redirect('admin/users/details/' . $user->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    die('orm validation failed');
                }
                catch ( \Exception $e )
                {
                    die('some other error');
                }
            }
            else
            {
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.create'));
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/_form')
            ->set('action', 'create')
            ->set('form', $form)
            ->set('user', $user);
    }
    
    
    public function action_update($id)
    {
        static::restrict('users.admin[update]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/users/update', __('user.breadcrumb.update'));
        
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
                    $user->from_array(array(
                        'name'      => $val->validated('name'),
                        'filter'    => $val->validated('filter'),
                    ));
                    
                    $user->save();
                    
                    return \Response::redirect('admin/users/details/' . $user->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    die('orm validation failed');
                }
                catch ( \Exception $e )
                {
                    die('some other error');
                }
            }
            else
            {
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.update'));
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/_form')
            ->set('action', 'update')
            ->set('form', $form)
            ->set('user', $user);
    }
    
    
    public function action_details($id)
    {
        static::restrict('users.admin[read]');
        
        if ( ! ( $user = \Model\Auth_User::find($id) ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/users/', __('user.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/users/details/' . $user->id, e($user->username));
        
        $this->view = static::$theme
            ->view('admin/details')
            ->set('user', $user);
    }
    
}
