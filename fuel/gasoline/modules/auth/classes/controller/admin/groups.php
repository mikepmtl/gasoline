<?php namespace Auth\Controller;

class Admin_Groups extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('group', true);
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups', __('group.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('groups.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/list', __('group.breadcrumb.list'));
        
        // $pagination_config = array(
        //     'pagination_url' => \Uri::create('admin/auth/groups'),
        //     'total_items'    => \Model\Auth_Group::count(),
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
        
        $groups = \Model\Auth_Group::query()
            ->limit($limit)
            ->offset($offset)
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array(
            '',
            __('auth.model.group.name'),
            __('auth.model.group.slug'),
            __('global.tools'),
        ));
        
        if ( $groups )
        {
            foreach ( $groups as &$group )
            {
                $row = $table->get_body()->add_row();
                
                $row->set_meta('group', $group);
                
                $row->add_cell('')
                    ->add_cell( \Auth::has_access('groups.admin[read]') ? \Html::anchor('admin/auth/groups/details/' . $group->id, e($group->name)) : e($group->name) )
                    ->add_cell(e($group->slug))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/groups/list')
            ->set('groups', $groups)
            // ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function action_create()
    {
        static::restrict('groups.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/create', __('group.breadcrumb.create'));
        
        $group = \Model\Auth_Group::forge();
        
        $form = $group->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($group);
            
            if ( $val->run() )
            {
                try
                {
                    $group->from_array(array(
                        'name'  => $val->validated('name'),
                    ));
                    
                    $group->save();
                    
                    return \Response::redirect('admin/auth/groups/details/' . $group->id);
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
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.create'));
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/groups/_form')
            ->set('action', 'create')
            ->set('form', $form)
            ->set('group', $group);
    }
    
    
    public function action_update($id)
    {
        static::restrict('groups.admin[update]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/update', __('group.breadcrumb.update'));
        
        if ( ! ( $group = \Model\Auth_Group::find($id) ) )
        {
            throw new \HttpNotFoundException();
        }
        
        $form = $group->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = \Validation::forge()->add_model($group);
            
            if ( $val->run() )
            {
                try
                {
                    $group->from_array(array(
                        'name'      => $val->validated('name'),
                    ));
                    
                    $group->save();
                    
                    return \Response::redirect('admin/auth/groups/details/' . $group->id);
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
            ->view('admin/groups/_form')
            ->set('action', 'update')
            ->set('form', $form)
            ->set('group', $group);
    }
    
    
    public function action_details($id)
    {
        static::restrict('groups.admin[read]');
        
        $query = \Model\Auth_Group::query()
            ->related('auditor')
            ->related('users')
            ->related('grouppermissions')
            ->related('roles')
            ->related('permissions')
            ->where('id', '=', $id);
        
        if ( ! ( $group = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/', __('group.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/details/' . $group->id, e($group->name));
        
        $this->view = static::$theme
            ->view('admin/groups/details')
            ->set('group', $group);
    }
    
}
