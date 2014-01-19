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
        
        \Lang::load('navigation', 'auth.navigation');
        \Lang::load('navigation/admin/group', 'auth.navigation.admin.group');
        \Lang::load('messages/group', 'auth.messages.group');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth', __('auth.navigation.breadcrumb._section'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups', __('auth.navigation.admin.group.breadcrumb._section'));
    }
    
    public function action_list()
    {
        static::restrict('auth.admin:groups[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/list', __('auth.navigation.admin.group.breadcrumb.list'));
        
        $pagination_config = array(
            'pagination_url'    => \Uri::create('admin/auth/groups'),
            'total_items'       => \Model\Auth_Group::count(),
            'per_page'          => 15,
            'uri_segment'       => 'page',
            'name'              => 'todo-sm',
        );
        
        // Create a pagination instance named 'mypagination'
        $pagination = \Pagination::forge('groups-pagination', $pagination_config);
        
        $groups = \Model\Auth_Group::query()
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->related('users')
            ->related('roles')
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->add_header(array(
            html_tag('input', array('type' => 'checkbox')),
            __('auth.model.group.name'),
            __('auth.model.group.slug'),
            __('global.tools'),
        ));
        
        if ( $groups )
        {
            foreach ( $groups as &$group )
            {
                $row = \Table\Row::forge()
                    ->set_meta('group', $group);
                
                $row['cbx']     = \Table\Cell::forge(\Gasform\Input_Checkbox::forge('group_id[]', $group->id, array()));
                $row['name']    = \Table\Cell::forge( \Auth::has_access('auth.admin:groups[read]') ? \Html::anchor('admin/auth/groups/details/' . $group->slug, e($group->name)) : e($group->name) );
                $row['slug']    = \Table\Cell::forge(e($group->slug));
                $row['actions'] = \Table\Cell::forge('');
                
                $table[$group->id] = $row;
            }
        }
        
        $form = new \Gasform\Form(\Uri::create('admin/roles/groups/action'));
        $bulk_actions = new \Gasform\Input_Select();
        
        $bulk_actions['bulk'] = \Gasform\Input_Option::forge(__('global.bulk_actions'), '', array());;
        
        $bulk_actions['delete'] = \Gasform\Input_Option::forge(__('button.delete'), 'delete', array());;
        
        $form['bulk_action'] = $bulk_actions->set_name('action');
        
        $form['submit'] = \Gasform\Input_Submit::forge('submit', __('button.submit'), array());;
        
        $this->view = static::$theme
            ->view('admin/groups/list')
            ->set('groups', $groups)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false)
            ->set('form', $form, false);
    }
    
    
    public function action_create()
    {
        static::restrict('auth.admin:groups[create]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/create', __('auth.navigation.admin.group.breadcrumb.create'));
        
        $group = \Model\Auth_Group::forge();
        
        $form = $group->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = $form->forge_validation();
            
            $form->repopulate(\Input::post());
            
            if ( $val->run() )
            {
                try
                {
                    $group->from_array(array(
                        'name'  => $val->validated('name'),
                    ));
                    
                    if ( $roles = \Input::post('roles', \Input::post('role_id', \Input::post('roles_ids', false))) )
                    {
                        is_array($roles) OR $roles = (array) $roles;
                        
                        try
                        {
                            $roles = \Model\Auth_Role::query()
                                ->where('id', 'IN', $roles)
                                ->get();
                            
                            $group->roles = $roles;
                        }
                        catch ( \Exception $e ) {}
                    }
                    
                    $group->save();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.group.create.success.message', array('name' => e($group->name))), __('auth.messages.group.create.success.heading'))->is_flash(true));
                    
                    try
                    {
                        \Cache::delete(\Config::get('gasauth.cache_prefix', 'auth').'.roles');
                    }
                    catch ( \Exception $e )
                    {
                        
                    }
                    
                    return \Response::redirect('admin/auth/groups/details/' . $group->slug);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.group.create.success.message', array('name' => e($group->name))), __('auth.messages.group.create.success.heading'))->is_flash(true));
                    
                    $form->set_errors($e->get_fieldset());
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_DEBUG, $e->getMessage());
                    
                    \Message\Container::instance('group-form')->push(\Message\Item::forge('danger', __('auth.messages.group.create.failure.message'), __('auth.messages.group.create.failure.heading')));
                }
            }
            else
            {
                \Message\Container::instance('group-form')->push(\Message\Item::forge('warning', __('auth.messages.group.validation_failed.message'), __('auth.messages.group.validation_failed.heading')));
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', __('button.create'), array());
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
        static::restrict('auth.admin:groups[update]');
        
        $query = \Model\Auth_Group::query()
            ->related('auditor')
            ->related('users')
            ->related('grouppermissions')
            ->related('roles')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('slug', '=', $id)
            ->and_where_close();
        
        if ( ! ( $group = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/update', __('auth.navigation.admin.group.breadcrumb.update'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/update/' . $group->slug, e($group->name));
        
        $form = $group->to_form();
        
        if ( \Input::method() === "POST" )
        {
            $val = $form->forge_validation();
            
            $form->repopulate(\Input::post());
            
            if ( $val->run() )
            {
                try
                {
                    $group->from_array(array(
                        'name'      => $val->validated('name'),
                    ));
                    
                    if ( $roles = \Input::post('roles', \Input::post('role_ids', \Input::post('role_id', false))) )
                    {
                        is_array($roles) OR $roles = (array) $roles;
                        
                        foreach ( $group->roles as $id => $role )
                        {
                            if ( ! in_array($id, $roles) )
                            {
                                unset($group->roles[$id]);
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
                                    $group->roles[] = $role;
                                }
                            }
                            catch ( \Exception $e ) {}
                        }
                    }
                    else
                    {
                        unset($group->roles);
                    }
                    
                    $group->save();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.group.update.success.message', array('name' => e($group->name))), __('auth.messages.group.update.success.heading'))->is_flash(true));
                    
                    try
                    {
                        \Cache::delete(\Config::get('gasauth.cache_prefix', 'auth').'.roles');
                    }
                    catch ( \Exception $e )
                    {
                        
                    }
                    
                    return \Response::redirect('admin/auth/groups/details/' . $group->slug);
                }
                catch ( \Orm\ValidationFailed $e )
                {
                    \Message\Container::instance('group-form')->push(\Message\Item::forge('warning', __('auth.messages.group.validation_failed.message'), __('auth.messages.group.validation_failed.heading')));
                    
                    $form->set_errors($e->get_fieldset());
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_DEBUG, $e->getMessage());
                    
                    \Message\Container::instance('group-form')->push(\Message\Item::forge('danger', __('auth.messages.group.update.failure.message'), __('auth.messages.group.update.failure.heading')));
                }
            }
            else
            {
                \Message\Container::instance('group-form')->push(\Message\Item::forge('warning', __('auth.messages.group.validation_failed.message'), __('auth.messages.group.validation_failed.heading')));
                
                $form->set_errors($val->error());
            }
        }
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', __('button.update'), array());
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
        static::restrict('auth.admin:groups[delete]');
        
        $query = \Model\Auth_Group::query()
            ->related('auditor')
            ->related('users')
            ->related('grouppermissions')
            ->related('roles')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('slug', '=', $id)
            ->and_where_close();
        
        if ( ! ( $group = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/delete', __('auth.navigation.admin.group.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/delete/' . $group->slug, e($group->name));
        
        $form = $group->to_form();
        
        $form->disable_fields();
        
        if ( \Input::method() === "POST" )
        {
            if ( \Input::post('confirm') === 'yes' )
            {
                try
                {
                    $name = $group->name;
                    $group->delete();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('auth.messages.group.delete.success.message', array('name' => e($name))), __('auth.messages.group.delete.success.heading'))->is_flash(true));
                }
                catch ( \Exception $e )
                {
                    logger(\Fuel::L_INFO, $e->getMessage(), __METHOD__);
                    
                    \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.group.delete.failure.message', array('name' => e($group->name))), __('auth.messages.group.delete.failure.heading'))->is_flash(true));
                }
                
                try
                {
                    \Cache::delete(\Config::get('gasauth.cache_prefix', 'auth').'.roles');
                }
                catch ( \Exception $e )
                {
                    
                }
            }
            else
            {
                \Message\Container::push(\Message\Item::forge('warning', __('auth.messages.group.warning.delete.message', array('name' => e($group->name))), __('auth.messages.group.warning.delete.heading'))->is_flash(true));
            }
            
            return \Response::redirect('admin/auth/groups');
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
            ->view('admin/groups/_form')
            ->set('action', 'delete')
            ->set('form', $form)
            ->set('group', $group);
    }
    
    
    public function action_details($id)
    {
        static::restrict('auth.admin:groups[read]');
        
        $query = \Model\Auth_Group::query()
            ->related('auditor')
            ->related('users')
            ->related('grouppermissions')
            ->related('roles')
            ->related('permissions')
            ->and_where_open()
                ->where('id', '=', $id)
                ->or_where('slug', '=', $id)
            ->and_where_close();
        
        if ( ! ( $group = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/', __('auth.navigation.admin.group.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/auth/groups/details/' . $group->slug, e($group->name));
        
        $this->view = static::$theme
            ->view('admin/groups/details')
            ->set('group', $group);
    }
    
    
    public function post_action()
    {
        switch ( \Input::post('action', false) )
        {
            default:
                throw new \HttpNotFoundException();
            
            break;
            case 'delete':
                static::restrict('auth.admin:groups[delete]');
                
                if ( $ids = \Input::post('group_id', false) )
                {
                    is_array($ids) OR $ids = array($ids);
                    
                    try
                    {
                        $groups = \Model\Auth_Group::query()
                            ->where('id', 'IN', $ids)
                            ->get();
                    }
                    catch ( \Exception $e )
                    {
                        break;
                    }
                    
                    if ( ! $groups )
                    {
                        break;
                    }
                    
                    $success = $failed = array();
                    
                    foreach ( $groups as &$group )
                    {
                        try
                        {
                            $name = $group->name;
                            
                            $group->delete();
                            
                            $success[] = e($name);
                        }
                        catch ( \Exception $e )
                        {
                            $failed[] = e($group->name);
                        }
                    }
                    
                    try
                    {
                        \Cache::delete(\Config::get('gasauth.cache_prefix', 'auth').'.roles');
                    }
                    catch ( \Exception $e )
                    {
                        
                    }
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('auth.messages.group.delete_batch.success.message', array('names' => implode(', ', $success))), __('auth.messages.group.delete_batch.success.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('auth.messages.group.delete_batch.failure.message', array('names' => implode(', ', $failed))), __('auth.messages.group.delete_batch.failure.heading'))->is_flash(true));
                }
            break;
        }
        
        return \Response::redirect('admin/auth/groups');
    }
    
}

/* End of file groups.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/admin/groups.php */
