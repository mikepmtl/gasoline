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
        
        \Lang::load('module', 'module');
        
        \Breadcrumb\Container::instance()->set_crumb('admin', __('global.admin'));
        \Breadcrumb\Container::instance()->set_crumb('admin/modules', __('module.breadcrumb.section'));
    }
    
    public function action_list()
    {
        static::restrict('modules.admin[list]');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/list', __('module.breadcrumb.list'));
        
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
        
        $table = \Table\Table::forge()->headers(array(
            html_tag('input', array('type' => 'checkbox')),
            __('module.model.module.name'),
            __('module.model.module.slug'),
            __('module.model.module.version'),
            __('global.tools'),
        ));
        
        if ( $modules )
        {
            foreach ( $modules as &$module )
            {
                \Module::load($module->slug);
                
                \Lang::load($module->slug . '::module', 'module.modules.' . $module->slug);
                
                $module->description = \Lang::get('module.modules.' . $module->slug . '.description');
                
                $row = $table->get_body()->add_row();
                
                $row->set_meta('module', $module)
                    ->add_cell(new \Gasform\Input_Checkbox('module_id', array(), $module->id))
                    ->add_cell( \Auth::has_access('modules.admin[read]') ? \Html::anchor('admin/modules/details/' . $module->id, e($module->name)) : e($module->name) )
                    ->add_cell(e($module->slug))
                    ->add_cell(e($module->version))
                    ->add_cell('');
            }
        }
        
        $this->view = static::$theme
            ->view('admin/list')
            ->set('modules', $modules)
            ->set('pagination', $pagination, false)
            ->set('table', $table, false);
    }
    
    
    public function get_upload()
    {
        throw new \HttpServerErrorException();
    }
    
    
    public function action_details($id_or_slug)
    {
        static::restrict('roles.admin[read]');
        
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
        
        \Module::load($module->slug);
        
        \Lang::load($module->slug . '::module', 'module.modules.' . $module->slug);
        
        $module->description = \Lang::get('module.modules.' . $module->slug . '.description');
        
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/', __('module.breadcrumb.details'));
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
            
            \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Module enabled!')->is_flash());
        }
        catch ( \Exception $e )
        {
            \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'Enabling failed!')->is_flash());
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
            
            \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Module disabled!')->is_flash());
        }
        catch ( \Exception $e )
        {
            \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'Disabling failed!')->is_flash());
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
            \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Cannot delete disabled module!')->is_flash());
            
            return \Response::redirect_back('admin/modules/list');
        }
        
        if ( $module->protected )
        {
            \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Cannot delete protected module!')->is_flash());
            
            return \Response::redirect_back('admin/modules/list');
        }
        
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/delete', __('module.breadcrumb.delete'));
        \Breadcrumb\Container::instance()->set_crumb('admin/modules/delete/' . $id_or_slug, e($module->name));
        
        $form = $module->to_form();
        
        $form->disable_fields();
        
        $cbx_group = new \Gasform\Input_CheckboxGroup();
        $cbx = new \Gasform\Input_Checkbox('confirm', array(), 'yes');
        $cbx_group['yes'] = $cbx->set_label(__('global.confirm_delete'));
        $form['confirm'] = $cbx_group->set_label(__('global.confirmation'));
        
        $btn_group = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), __('button.delete'));
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
                    $module->delete();
                    
                    \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Yes!', 'Deleted!')->is_flash());
                }
                catch ( \Exception $e )
                {
                    \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'No!', 'Failure!')->is_flash());
                }
            }
            else
            {
                \Message\Container::instance()->set(null, \Message\Item::forge('warning', 'No!', 'Not confirmed!')->is_flash());
            }
            
            return \Response::redirect('admin/modules/list');
        }
        
        return $this->get_delete();
    }
    
}
