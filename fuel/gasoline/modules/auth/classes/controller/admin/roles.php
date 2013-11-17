<?php namespace Auth\Controller;

class Admin_Roles extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('role', true);
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles', __('role.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('roles.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/list', __('role.breadcrumb.list'));
        
        // $pagination_config = array(
        //     'pagination_url' => \Uri::create('admin/auth/roles'),
        //     'total_items'    => \Model\Auth_Role::count(),
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
        
        $roles = \Model\Auth_Role::query()
            ->limit($limit)
            ->offset($offset)
            ->related('users')
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array(
            '',
            __('auth.model.role.name'),
            __('auth.model.role.slug'),
            __('global.tools'),
        ));
        
        if ( $roles )
        {
            foreach ( $roles as &$role )
            {
                $row = $table->get_body()->add_row();
                
                $row->set_meta('role', $role)
                    ->add_cell('')
                    ->add_cell( \Auth::has_access('roles.admin[read]') ? \Html::anchor('admin/auth/roles/details/' . $role->id, e($role->name)) : e($role->name) )
                    ->add_cell(e($role->slug))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/roles/list')
            ->set('roles', $roles)
            // ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function action_create()
    {
        static::restrict('roles.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/create', __('role.breadcrumb.create'));
        
        $role = \Model\Auth_Role::forge();
        
        $form = $role->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($role);
            
            if ( $val->run() )
            {
                try
                {
                    $role->from_array(array(
                        'name'      => $val->validated('name'),
                        'filter'    => $val->validated('filter'),
                    ));
                    
                    $role->save();
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Created!')->is_flash());
                    
                    return \Response::redirect('admin/auth/roles/details/' . $role->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation Failed!'));
                    
                    die('orm validation failed');
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'Some weird error occured!'));
                    
                    die('some other error');
                }
            }
            else
            {
                \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation Failed!'));
                
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.create'));
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/roles/_form')
            ->set('action', 'create')
            ->set('form', $form)
            ->set('role', $role);
    }
    
    
    public function action_update($id)
    {
        static::restrict('roles.admin[update]');
        
        if ( ! ( $role = \Model\Auth_Role::find($id) ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/update', __('role.breadcrumb.update'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/update/' . $role->id, e($role->name));
        
        $form = $role->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($role);
            
            if ( $val->run() )
            {
                try
                {
                    $role->from_array(array(
                        'name'      => $val->validated('name'),
                        'filter'    => $val->validated('filter'),
                    ));
                    
                    $role->save();
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Updated!')->is_flash());
                    
                    return \Response::redirect('admin/auth/roles/details/' . $role->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation Failed!'));
                    
                    die('orm validation failed');
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'Some weird error occured!'));
                    
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
            ->view('admin/roles/_form')
            ->set('action', 'update')
            ->set('form', $form)
            ->set('role', $role);
    }
    
    
    public function action_delete($id)
    {
        static::restrict('roles.admin[delete]');
        
        if ( ! ( $role = \Model\Auth_Role::find($id) ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/delete', __('role.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/delete/' . $role->id, e($role->name));
        
        $form = $role->to_form();
        
        $form->disable_fields();
        
        if ( \Input::method() === "POST" )
        {
            if ( \Input::post('confirm') === 'yes' )
            {
                try
                {
                    $role->delete();
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Deleted!')->is_flash());
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_INFO, $e->getMessage(), __METHOD__);
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'Failure!')->is_flash());
                }
            }
            else
            {
                \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Not confirmed!')->is_flash());
            }
            
            return \Response::redirect('admin/auth/roles');
        }
        
        $cbx_group = new \Gasform\Input_CheckboxGroup();
        $cbx = new \Gasform\Input_Checkbox('confirm', array(), 'yes');
        $cbx_group['yes'] = $cbx->set_label(__('global.confirm_delete'));
        $form['confirm'] = $cbx_group->set_label(__('global.confirmation'));
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.delete'));
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/roles/_form')
            ->set('action', 'delete')
            ->set('form', $form)
            ->set('role', $role);
    }
    
    
    public function action_details($id)
    {
        static::restrict('roles.admin[read]');
        
        $query = \Model\Auth_Role::query()
            ->related('auditor')
            ->related('rolepermissions')
            ->related('users')
            ->related('groups')
            ->related('permissions')
            ->where('id', '=', $id);
        
        if ( ! ( $role = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/', __('role.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/details/' . $role->id, e($role->name));
        
        $this->view = static::$theme
            ->view('admin/roles/details')
            ->set('role', $role);
    }
    
}
