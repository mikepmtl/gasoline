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

class Admin_Roles extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('auth/role', 'auth.role');
        \Lang::load('messages/role', 'auth.messages.role');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.breadcrumb.section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles', __('auth.role.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('roles.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/list', __('auth.role.breadcrumb.list'));
        
        $pagination_config = array(
            'pagination_url'    => \Uri::create('admin/auth/roles'),
            'total_items'       => \Model\Auth_Role::count(),
            'per_page'          => 15,
            'uri_segment'       => 'page',
            'name'              => 'todo-sm',
        );
        
        // Create a pagination instance named 'mypagination'
        $pagination = \Pagination::forge('roles-pagination', $pagination_config);
        
        $roles = \Model\Auth_Role::query()
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->related('users')
            ->related('groups')
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->headers(array(
            html_tag('input', array('type' => 'checkbox')),
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
                    ->add_cell(new \Gasform\Input_Checkbox('role_id[]', array(), $role->id))
                    ->add_cell( \Auth::has_access('roles.admin[read]') ? \Html::anchor('admin/auth/roles/details/' . $role->id, e($role->name)) : e($role->name) )
                    ->add_cell(e($role->slug))
                    ->add_cell('');
            }
        }
        
        $form = new \Gasform\Form(\Uri::create('admin/auth/roles/action'));
        $bulk_actions = new \Gasform\Input_Select();
        
        $bulk = new \Gasform\Input_Option(__('global.bulk_actions'), array(), '');
        $bulk_actions['bulk'] = $bulk;
        
        $delete = new \Gasform\Input_Option(__('button.delete'), array(), 'delete');
        $bulk_actions['delete'] = $delete;
        
        $form['bulk_action'] = $bulk_actions->set_name('action');
        
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.submit'));
        $form['submit'] = $submit;
        
        $this->view = static::$theme
            ->view('admin/roles/list')
            ->set('roles', $roles)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false)
            ->set('form', $form, false);
    }
    
    
    public function action_create()
    {
        static::restrict('roles.admin[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/create', __('auth.role.breadcrumb.create'));
        
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
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.success.create.message', array('name' => e($role->name))), __('auth.messages.role.success.create.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/roles/details/' . $role->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('warning', __('auth.messages.role.validation_failed.message'), __('auth.messages.role.validation_failed.heading')));
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('danger', __('auth.messages.role.failure.create.message'), __('auth.messages.role.failure.create.heading')));
                }
            }
            else
            {
                \Message\Container::instance('role-form')->push(\Message\Item::forge('warning', __('auth.messages.role.validation_failed.message'), __('auth.messages.role.validation_failed.heading')));
                
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
        
        $query = \Model\Auth_Role::query()
            ->related('auditor')
            ->related('rolepermissions')
            ->related('users')
            ->related('groups')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('slug', '=', $id)
            ->and_where_close();
        
        if ( ! ( $role = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/update', __('auth.role.breadcrumb.update'));
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
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.success.update.message', array('name' => e($role->name))), __('auth.messages.role.success.update.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/roles/details/' . $role->id);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('warning', __('auth.messages.role.validation_failed.message'), __('auth.messages.role.validation_failed.heading')));
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('danger', __('auth.messages.role.failure.update.message'), __('auth.messages.role.failure.update.heading')));
                }
            }
            else
            {
                \Message\Container::instance('role-form')->push(\Message\Item::forge('warning', __('auth.messages.role.validation_failed.message'), __('auth.messages.role.validation_failed.heading')));
                
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
        
        $query = \Model\Auth_Role::query()
            ->related('auditor')
            ->related('rolepermissions')
            ->related('users')
            ->related('groups')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('slug', '=', $id)
            ->and_where_close();
        
        if ( ! ( $role = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/delete', __('auth.role.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/delete/' . $role->id, e($role->name));
        
        $form = $role->to_form();
        
        $form->disable_fields();
        
        if ( \Input::method() === "POST" )
        {
            if ( \Input::post('confirm') === 'yes' )
            {
                try
                {
                    $name = $role->name;
                    $role->delete();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.success.delete.message', array('name' => e($name))), __('auth.messages.role.success.delete.heading'))->is_flash(true));
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_INFO, $e->getMessage(), __METHOD__);
                    
                    \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.role.failure.delete.message'), __('auth.messages.role.failure.delete.heading'))->is_flash(true));
                }
            }
            else
            {
                \Message\Container::push(\Message\Item::forge('warning', __('auth.messages.role.warning.delete.message', array('name' => e($role->name))), __('auth.messages.role.warning.delete.heading'))->is_flash(true));
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
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('slug', '=', $id)
            ->and_where_close();
        
        if ( ! ( $role = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/', __('auth.role.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/details/' . $role->id, e($role->name));
        
        $this->view = static::$theme
            ->view('admin/roles/details')
            ->set('role', $role);
    }
    
    
    public function post_action()
    {
        switch ( \Input::post('action', false) )
        {
            default:
                throw new \HttpNotFoundException();
            
            break;
            case 'delete':
                static::restrict('roles.admin[delete]');
                
                if ( $ids = \Input::post('role_id', false) )
                {
                    is_array($ids) OR $ids = array($ids);
                    
                    try
                    {
                        $roles = \Model\Auth_Role::query()
                            ->where('id', 'IN', $ids)
                            ->get();
                    }
                    catch ( \Exception $e )
                    {
                        break;
                    }
                    
                    if ( ! $roles )
                    {
                        break;
                    }
                    
                    $success = $failed = array();
                    
                    foreach ( $roles as &$role )
                    {
                        try
                        {
                            $name = $role->name;
                            
                            $role->delete();
                            
                            $success[] = e($name);
                        }
                        catch ( \Exception $e )
                        {
                            $failed[] = e($role->name);
                        }
                    }
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.success.delete_batch.message', array('names' => implode(', ', $success))), __('auth.messages.role.success.delete_batch.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.role.failure.delete_batch.message', array('names' => implode(', ', $failed))), __('auth.messages.role.failure.delete_batch.heading'))->is_flash(true));
                }
            break;
        }
        
        return \Response::redirect('admin/auth/roles');
    }
    
}

/* End of file roles.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/roles.php */
