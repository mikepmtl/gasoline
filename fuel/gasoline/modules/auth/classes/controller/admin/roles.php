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
        
        \Lang::load('navigation', 'auth.navigation');
        \Lang::load('navigation/admin/role', 'auth.navigation.admin.role');
        \Lang::load('messages/role', 'auth.messages.role');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.navigation.breadcrumb._section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles', __('auth.navigation.admin.role.breadcrumb._section'));
    }
    
    public function action_list()
    {
        static::restrict('roles.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/list', __('auth.navigation.admin.role.breadcrumb.list'));
        
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
        
        $table = \Table\Table::forge()->add_header(array(
            html_tag('input', array('type' => 'checkbox')),
            __('auth.model.role.name'),
            __('auth.model.role.slug'),
            __('global.tools'),
        ));
        
        if ( $roles )
        {
            foreach ( $roles as &$role )
            {
                $row = \Table\Row::forge()
                    ->set_meta('role', $role);
                
                $row['cbx']     = \Table\Cell::forge(\Gasform\Input_Checkbox::forge('role_id[]', $role->id, array()));
                $row['name']    = \Table\Cell::forge( \Auth::has_access('roles.admin[read]') ? \Html::anchor('admin/auth/roles/details/' . $role->slug, e($role->name)) : e($role->name) );
                $row['slug']    = \Table\Cell::forge(e($role->slug));
                $row['actions'] = \Table\Cell::forge('');
                
                $table[$role->id] = $row;
            }
        }
        
        $form = new \Gasform\Form(\Uri::create('admin/auth/roles/action'));
        $bulk_actions = new \Gasform\Input_Select();
        $bulk_actions['bulk'] = \Gasform\Input_Option::forge(__('global.bulk_actions'), '', array());
        $bulk_actions['delete'] = \Gasform\Input_Option::forge(__('button.delete'), 'delete', array());;
        
        $form['bulk_action'] = $bulk_actions->set_name('action');
        
        $form['submit'] = \Gasform\Input_Submit::forge('submit', __('button.submit'), array());;
        
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/create', __('auth.navigation.admin.role.breadcrumb.create'));
        
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
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.create.success.message', array('name' => e($role->name))), __('auth.messages.role.create.success.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/roles/details/' . $role->slug);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('warning', __('auth.messages.role.validation_failed.message'), __('auth.messages.role.validation_failed.heading')));
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('danger', __('auth.messages.role.create.failure.message'), __('auth.messages.role.create.failure.heading')));
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
        $btn_group['submit'] = \Gasform\Input_Submit::forge('submit', __('button.create'), array());
        
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/update', __('auth.navigation.admin.role.breadcrumb.update'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/update/' . $role->slug, e($role->name));
        
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
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.update.success.message', array('name' => e($role->name))), __('auth.messages.role.update.success.heading'))->is_flash(true));
                    
                    return \Response::redirect('admin/auth/roles/details/' . $role->slug);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('warning', __('auth.messages.role.validation_failed.message'), __('auth.messages.role.validation_failed.heading')));
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance('role-form')->push(\Message\Item::forge('danger', __('auth.messages.role.update.failure.message'), __('auth.messages.role.update.failure.heading')));
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
        $btn_group['submit'] = \Gasform\Input_Submit::forge('submit', __('button.update'), array());
        
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/delete', __('auth.navigation.admin.role.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/delete/' . $role->slug, e($role->name));
        
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
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.delete.success.message', array('name' => e($name))), __('auth.messages.role.delete.success.heading'))->is_flash(true));
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_INFO, $e->getMessage(), __METHOD__);
                    
                    \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.role.delete.failure.message', array('name' => e($role->name))), __('auth.messages.role.delete.failure.heading'))->is_flash(true));
                }
            }
            else
            {
                \Message\Container::push(\Message\Item::forge('warning', __('auth.messages.role.warning.delete.message', array('name' => e($role->name))), __('auth.messages.role.warning.delete.heading'))->is_flash(true));
            }
            
            return \Response::redirect('admin/auth/roles');
        }
        
        $cbx_group = new \Gasform\Input_CheckboxGroup();
        $cbx_group['yes'] = \Gasform\Input_Checkbox::forge('confirm', 'yes', array())
            ->set_label(__('global.confirm_delete'));
        $form['confirm'] = $cbx_group->set_label(__('global.confirmation'));
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $btn_group['submit'] = \Gasform\Input_Submit::forge('submit', __('button.delete'), array());
        
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
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/', __('auth.navigation.admin.role.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/roles/details/' . $role->slug, e($role->name));
        
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
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('auth.messages.role.delete_batch.success.message', array('names' => implode(', ', $success))), __('auth.messages.role.delete_batch.success.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.role.delete_batch.failure.message', array('names' => implode(', ', $failed))), __('auth.messages.role.delete_batch.failure.heading'))->is_flash(true));
                }
            break;
        }
        
        return \Response::redirect('admin/auth/roles');
    }
    
}

/* End of file roles.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/roles.php */
