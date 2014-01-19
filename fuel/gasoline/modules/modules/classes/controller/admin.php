<?php namespace Modules\Controller;

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

class Admin extends \Controller\Admin {
    
    public $default_action = 'list';
    
    public function before()
    {
        parent::before();
        
        \Lang::load('messages', 'modules.messages');
        \Lang::load('navigation/admin', 'modules.navigation.admin');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/modules', __('modules.navigation.admin.breadcrumb._section'));
    }
    
    public function action_list()
    {
        static::restrict('modules.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/list', __('modules.navigation.admin.breadcrumb.list'));
        
        $pagination_config = array(
            'pagination_url'    => \Uri::create('admin/modules/list'),
            'total_items'       => \Modules\Model\Module::count(),
            'per_page'          => 15,
            'uri_segment'       => 'page',
            'name'              => 'todo-sm',
        );
        
        // Create a pagination instance named 'mypagination'
        $pagination = \Pagination::forge('modules-pagination', $pagination_config);
        
        $modules = \Modules\Model\Module::query()
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->get();
        
        \Package::load('table');
        
        $table = \Table\Table::forge()->add_header(array(
            html_tag('input', array('type' => 'checkbox')),
            __('modules.model.module.name'),
            __('modules.model.module.slug'),
            __('modules.model.module.version'),
            __('global.tools'),
        ));
        
        if ( $modules )
        {
            foreach ( $modules as &$module )
            {
                $module->load_description();
                
                $row = \Table\Row::forge()
                    ->set_meta('module', $module);
                
                $row['cbx']     = \Table\Cell::forge(\Gasform\Input_Checkbox::forge('module_id[]', $module->id, array()));
                $row['name']    = \Table\Cell::forge( \Auth::has_access('modules.admin[read]') ? \Html::anchor('admin/modules/details/' . $module->slug, e($module->name)) : e($module->name) );
                $row['slug']    = \Table\Cell::forge(e($module->slug));
                $row['version'] = \Table\Cell::forge(e($module->version));
                $row['actions'] = \Table\Cell::forge('');
                
                $table[$module->id] = $row;
            }
        }
        
        $form_bulk = new \Gasform\Form(\Uri::create('admin/auth/users/action'));
        $bulk_actions = new \Gasform\Input_Select();
        
        $bulk = new \Gasform\Input_Option(__('global.bulk_actions'), '', array());
        $bulk_actions['bulk'] = $bulk;
        
        $disable = new \Gasform\Input_Option(__('button.disable'), 'disable', array());
        $bulk_actions['disable'] = $disable;
        
        $enable = new \Gasform\Input_Option(__('button.enable'), 'enable', array());
        $bulk_actions['enable'] = $enable;
        
        // $download = new \Gasform\Input_Option(__('button.download'), 'download', array());
        // $bulk_actions['download'] = $download;
        
        $delete = new \Gasform\Input_Option(__('button.delete'), 'delete', array());
        $bulk_actions['delete'] = $delete;
        
        $form_bulk['bulk_action'] = $bulk_actions->set_name('action');
        
        $submit = new \Gasform\Input_Submit('submit', __('button.submit'), array());
        $form_bulk['submit'] = $submit;
        
        $this->view = static::$theme
            ->view('admin/list')
            ->set('modules', $modules)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false)
            ->set('form_bulk', $form_bulk);;
    }
    
    
    public function get_upload()
    {
        throw new \HttpNotFoundException();
    }
    
    
    public function action_details($id_or_slug)
    {
        static::restrict('auth.admin:roles[read]');
        
        $query = \Modules\Model\Module::query()
            ->related('auditor')
            ->and_where_open()
                ->where('id', '=', $id_or_slug)
                ->or_where('slug', '=', $id_or_slug)
            ->and_where_close();
        
        if ( ! ( $module = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        $module->load_description();
        
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/', __('modules.navigation.admin.breadcrumb.details'));
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/details/' . $id_or_slug, e($module->name));
        
        $this->view = static::$theme
            ->view('admin/details')
            ->set('module', $module);
    }
    
    
    public function action_enable($id_or_slug)
    {
        static::restrict('modules.admin[enable]');
        
        $query = \Modules\Model\Module::query()
            ->and_where_open()
                ->where('id', '=', $id_or_slug)
                ->or_where('slug', '=', $id_or_slug)
            ->and_where_close();
        
        if ( ! ( $module = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        try
        {
            $module->enable();
            
            \Message\Container::push(\Message\Item::forge('success', __('modules.messages.enable.success.message', array('name' => e($module->name))), __('modules.messages.enable.success.heading'))->is_flash(true));
        }
        catch ( \Exception $e )
        {
            logger(\Fuel::L_DEBUG, $e->getMessage());
            
            \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.enable.failure.message', array('name' => e($module->name))), __('modules.messages.enable.failure.heading'))->is_flash(true));
        }
        
        return \Response::redirect_back('admin/modules/list');
    }
    
    
    public function action_disable($id_or_slug)
    {
        static::restrict('modules.admin[disable]');
        
        $query = \Modules\Model\Module::query()
            ->and_where_open()
                ->where('id', '=', $id_or_slug)
                ->or_where('slug', '=', $id_or_slug)
            ->and_where_close();
        
        if ( ! ( $module = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        try
        {
            $module->disable();
            
            \Message\Container::push(\Message\Item::forge('success', __('modules.messages.disable.success.message', array('name' => e($module->name))), __('modules.messages.disable.success.heading'))->is_flash(true));
        }
        catch ( \Exception $e )
        {
            logger(\Fuel::L_DEBUG, $e->getMessage());
            
            \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.disable.failure.message', array('name' => e($module->name))), __('modules.messages.disable.failure.heading'))->is_flash(true));
        }
        
        return \Response::redirect_back('admin/modules/list');
    }
    
    
    public function get_delete($id_or_slug)
    {
        static::restrict('modules.admin[delete]');
        
        $query = \Modules\Model\Module::query()
            ->and_where_open()
                ->where('id', '=', $id_or_slug)
                ->or_where('slug', '=', $id_or_slug)
            ->and_where_close();
        
        if ( ! ( $module = $query->get_one() ) )
        {
            throw new \HttpNotFoundException();
        }
        
        if ( $module->is_enabled() )
        {
            \Message\Container::push(\Message\Item::forge('warning', __('modules.messages.delete.enabled.message', array('name' => e($module->name))), __('modules.messages.delete.enabled.heading'))->is_flash(true));
            
            return \Response::redirect_back('admin/modules/list');
        }
        
        if ( $module->protected )
        {
            \Message\Container::push(\Message\Item::forge('warning', __('modules.messages.delete.protected.message', array('name' => e($module->name))), __('modules.messages.delete.protected.heading'))->is_flash(true));
            
            return \Response::redirect_back('admin/modules/list');
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/delete', __('modules.navigation.admin.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/delete/' . $id_or_slug, e($module->name));
        
        $form = $module->to_form();
        
        $form->disable_fields();
        
        $cbx_group = \Gasform\Input_CheckboxGroup::forge();
        $cbx = \Gasform\Input_Checkbox::forge('confirm', 'yes', array());
        $cbx_group['yes'] = $cbx->set_label(__('global.confirm_delete'));
        $form['confirm'] = $cbx_group->set_label(__('global.confirmation'));
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', __('button.delete'), array());
        $btn_group['submit'] = $submit;
        
        $form['btn-group'] = $btn_group;
        
        $this->view = static::$theme
            ->view('admin/_form')
            ->set('action', 'delete')
            ->set('form', $form)
            ->set('module', $module);
    }
    
    
    public function post_delete()
    {
        if ( \Input::post('id', false) && $module = \Modules\Model\Module::find(\Input::post('id')) )
        {
            if ( \Input::post('confirm', 'no') == 'yes' )
            {
                try
                {
                    $name = $module->name;
                    $module->delete();
                    
                    \Message\Container::push(\Message\Item::forge('success', __('modules.messages.delete.success.message', array('name' => e($name))), __('modules.messages.delete.success.heading'))->is_flash(true));
                }
                catch ( \Exception $e )
                {
                    \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.delete.failure.message', array('name' => e($module->name))), __('modules.messages.delete.failure.heading'))->is_flash(true));
                }
            }
            else
            {
                \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.delete.unconfirmed.message', array('name' => e($module->name))), __('modules.messages.delete.unconfirmed.heading'))->is_flash(true));
            }
            
            return \Response::redirect('admin/modules/list');
        }
        
        return $this->get_delete();
    }
    
    
    public function post_action()
    {
        switch ( \Input::post('action', false) )
        {
            default:
                throw new \HttpNotFoundException();
            
            break;
            
            case 'delete':
                static::restrict('modules.admin[delete]');
                
                if ( $ids = \Input::post('module_id', \Input::post('module_ids', false)) )
                {
                    is_array($ids) OR $ids = array($ids);
                    
                    if ( ! $ids )
                    {
                        break;
                    }
                    
                    try
                    {
                        $modules = \Modules\Model\Module::query()
                            ->where('id', 'IN', $ids)
                            ->get();
                    }
                    catch ( \Exception $e )
                    {
                        break;
                    }
                    
                    if ( ! $modules )
                    {
                        break;
                    }
                    
                    $success = $failed = $enabled = $protected = array();
                    
                    foreach ( $modules as &$module )
                    {
                        if ( $module->is_enabled() )
                        {
                            $enabled[] = e($module->name);
                            
                            continue;
                        }
                        
                        if ( $module->is_protected() )
                        {
                            $protected[] = e($module->name);
                            
                            continue;
                        }
                        
                        try
                        {
                            $name = $module->name;
                            
                            $module->delete();
                            
                            $success[] = e($name);
                        }
                        catch ( \Exception $e )
                        {
                            $failed[] = e($module->name);
                        }
                    }
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('modules.messages.delete_batch.success.message', array('names' => implode(', ', $success))), __('modules.messages.delete_batch.success.heading'))->is_flash(true));
                    
                    $enabled && \Message\Container::push(\Message\Item::forge('warning', __('modules.messages.delete_batch.enabled.message', array('names' => implode(', ', $enabled))), __('modules.messages.delete_batch.enabled.heading'))->is_flash(true));
                    
                    $protected && \Message\Container::push(\Message\Item::forge('warning', __('modules.messages.delete_batch.protected.message', array('names' => implode(', ', $protected))), __('modules.messages.delete_batch.protected.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.delete_batch.failure.message', array('names' => implode(', ', $failed))), __('modules.messages.delete_batch.failure.heading'))->is_flash(true));
                }
            break;
            
            case 'disable':
                static::restrict('modules.admin[disable]');
                
                if ( $ids = \Input::post('module_id', \Input::post('module_ids', false)) )
                {
                    is_array($ids) OR $ids = array($ids);
                    
                    if ( ! $ids )
                    {
                        break;
                    }
                    
                    try
                    {
                        $modules = \Modules\Model\Module::query()
                            ->where('id', 'IN', $ids)
                            ->get();
                    }
                    catch ( \Exception $e )
                    {
                        break;
                    }
                    
                    if ( ! $modules )
                    {
                        break;
                    }
                    
                    $success = $failed = array();
                    
                    foreach ( $modules as &$module )
                    {
                        if ( $module->is_disabled() )
                        {
                            continue;
                        }
                        
                        try
                        {
                            $module->disable();
                            
                            $success[] = e($module->name);
                        }
                        catch ( \Exception $e )
                        {
                            $failed[] = e($module->name);
                        }
                    }
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('modules.messages.disable_batch.success.message', array('names' => implode(', ', $success))), __('modules.messages.disable_batch.success.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.disable_batch.failure.message', array('names' => implode(', ', $failed))), __('modules.messages.disable_batch.failure.heading'))->is_flash(true));
                }
            break;
            
            case 'enable':
                static::restrict('modules.admin[enable]');
                
                if ( $ids = \Input::post('module_id', \Input::post('module_ids', false)) )
                {
                    is_array($ids) OR $ids = array($ids);
                    
                    if ( ! $ids )
                    {
                        break;
                    }
                    
                    try
                    {
                        $modules = \Modules\Model\Module::query()
                            ->where('id', 'IN', $ids)
                            ->get();
                    }
                    catch ( \Exception $e )
                    {
                        break;
                    }
                    
                    if ( ! $modules )
                    {
                        break;
                    }
                    
                    $success = $failed = array();
                    
                    foreach ( $modules as &$module )
                    {
                        if ( $module->is_enabled() )
                        {
                            continue;
                        }
                        
                        try
                        {
                            $module->enable();
                            
                            $success[] = e($module->name);
                        }
                        catch ( \Exception $e )
                        {
                            $failed[] = e($module->name);
                        }
                    }
                    
                    $success && \Message\Container::push(\Message\Item::forge('success', __('modules.messages.enable_batch.success.message', array('names' => implode(', ', $success))), __('modules.messages.enable_batch.success.heading'))->is_flash(true));
                    
                    $failed && \Message\Container::push(\Message\Item::forge('danger', __('modules.messages.enable_batch.failure.message', array('names' => implode(', ', $failed))), __('modules.messages.enable_batch.failure.heading'))->is_flash(true));
                }
            break;
        }
        
        return \Response::redirect('admin/modules');
    }
    
}
