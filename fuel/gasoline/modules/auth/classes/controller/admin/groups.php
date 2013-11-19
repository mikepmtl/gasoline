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

class Admin_Groups extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('auth/group', 'auth.group');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups', __('auth.group.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('groups.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/list', __('auth.group.breadcrumb.list'));
        
        $pagination_config = array(
            'pagination_url'    => \Uri::create('admin/auth/groups'),
            'total_items'       => \Model\Auth_Group::count(),
            'per_page'          => 15,
            'uri_segment'       => 'page',
            'name'              => 'todo-sm',
        );
        
        // Create a pagination instance named 'mypagination'
        $pagination = \Pagination::forge('todo', $pagination_config);
        
        $groups = \Model\Auth_Group::query()
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->related('users')
            ->related('roles')
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array(
            html_tag('input', array('type' => 'checkbox')),
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
                
                $row->add_cell(new \Gasform\Input_Checkbox('group_id', array(), $group->id))
                    ->add_cell( \Auth::has_access('groups.admin[read]') ? \Html::anchor('admin/auth/groups/details/' . $group->id, e($group->name)) : e($group->name) )
                    ->add_cell(e($group->slug))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/groups/list')
            ->set('groups', $groups)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function action_create()
    {
        static::restrict('groups.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/create', __('auth.group.breadcrumb.create'));
        
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
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Created!')->is_flash());
                    
                    return \Response::redirect('admin/auth/groups/details/' . $group->id);
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
                \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation failed!'));
                
                $form->repopulate(\Input::post());
                
                $form->set_errors($val->error());
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
        
        if ( ! ( $group = \Model\Auth_Group::find($id) ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/update', __('auth.group.breadcrumb.update'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/update/' . $group->id, e($group->name));
        
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
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Updated!')->is_flash());
                    
                    return \Response::redirect('admin/auth/groups/details/' . $group->id);
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
                \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Validation failed!'));
                
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
    
    
    public function action_delete($id)
    {
        static::restrict('groups.admin[delete]');
        
        if ( ! ( $group = \Model\Auth_Group::find($id) ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/delete', __('auth.group.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/delete/' . $group->id, e($group->name));
        
        $form = $group->to_form();
        
        $form->disable_fields();
        
        if ( \Input::method() === "POST" )
        {
            if ( \Input::post('confirm') === 'yes' )
            {
                try
                {
                    $group->delete();
                    
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
            
            return \Response::redirect('admin/auth/groups');
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
            ->view('admin/groups/_form')
            ->set('action', 'delete')
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/', __('auth.group.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/details/' . $group->id, e($group->name));
        
        $this->view = static::$theme
            ->view('admin/groups/details')
            ->set('group', $group);
    }
    
}

/* End of file groups.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/groups.php */
