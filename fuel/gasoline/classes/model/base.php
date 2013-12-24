<?php namespace Gasoline\Model;

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

abstract class Base extends \Orm\Model {
    
    // protected static $_form_element_support = false;
    
    // protected static $_form_element_options = array(
    //     'content'    => null,    // What to display in the dropdown
    //     'group'      => null,    // Whether to allow grouping
    //     'value'      => null,    // What the submitted value will be
    // );
    
    protected static $_form_elements_cached = array();
    
    protected static $_forms_cached = array();
    
    /**
     * Allows for lazily adding has_one relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'profile',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function has_one($key, array $options)
    {
        isset(static::$_has_one) OR static::$_has_one = array();
        
        static::$_has_one[$key] = $options;
    }
    
    
    /**
     * Allows for lazily adding has_many relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'comments',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function has_many($key, array $options)
    {
        isset(static::$_has_many) OR static::$_has_many = array();
        
        static::$_has_many[$key] = $options;
    }
    
    
    /**
     * Allows for lazily adding belongs_to relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'group',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function belongs_to($key, array $options)
    {
        isset(static::$_belongs_to) OR static::$_belongs_to = array();
        
        static::$_belongs_to[$key] = $options;
    }
    
    
    /**
     * Allows for lazily adding many_many relations to the object
     * 
     * @param   string  $key        The key of the relation, e.g., 'rsvp',
     * @param   array   $options    Regular options array to use for the relation
     * 
     * @return  void
     */
    public static function many_many($key, array $options)
    {
        isset(static::$_many_many) OR static::$_many_many = array();
        
        static::$_many_many[$key] = $options;
    }
    
    
    /**
     * If $form !== null, then this will add a fieldset, otherwise create a new
     * form and return that
     * 
     * @param  [type] $form [description]
     * @return [type]       [description]
     */
    public function to_form($form = null)
    {
        $me = get_called_class();
        
        if ( isset(static::$_forms_cached[$me]) )
        {
            return static::$_forms_cached[$me]->populate($this->to_array());
        }
        
        \Package::load('gasform');
        
        $properties = static::properties();
        
        $form = new \Gasform\Form();
        
        if ( ! $this->is_new() )
        {
            foreach ( static::primary_key() as $pk )
            {
                $form[$pk] = \Gasform\Input_Hidden::forge($pk, $this->{$pk}, array());
            }
        }
        
        foreach ( $properties as $p => $options )
        {
            $label = \Arr::get($options, 'label');
            $label && $label = ( \Lang::get($label, array(), false) ? : $label );
            
            switch ( $type = \Arr::get($options, 'form.type', false) )
            {
                default:
                case false:
                case 'skip':
                    continue;
                break;
                case 'color':
                case 'date':
                case 'datetime':
                case 'datetime-local':
                case 'email':
                case 'month':
                case 'number':
                case 'password':
                case 'range':
                case 'text':
                case 'textarea':
                case 'time':
                case 'url':
                case 'week':
                    $class = '\\Gasform\\Input_' . ucwords($type);
                    $el = $class::forge();
                    
                    if ( in_array('required', \Arr::get($options, 'validation', array())) )
                    {
                        $el = $el->set_attribute('required', 'required');
                    }
                    
                    ( $help = \Arr::get($options, 'form.help', false) ) && $el->set_meta('help', \Lang::get($help, array(), false) ? : $help);
                    
                    \Arr::get($options, 'validation', false) && $el->set_meta('validation', \Arr::get($options, 'validation'));
                    
                    $form[$p] = $el->set_name($p)->set_label($label);
                break;
                
                case 'select':
                    // Check if the options are given or if it's a select for a
                    // belongs_to or has_many relation
                    if ( $relation = \Arr::get($options, 'relation', false) )
                    {
                        $rel_type = '_' . $relation[0];
                        $rel_prop = $relation[1];
                        $rel_opts = static::$$rel_type;
                        
                        $el = call_fuel_func_array(array($rel_opts[$rel_prop]['model_to'], 'to_form_element'), array());
                        
                        if ( $rel_type == '_has_many' )
                        {
                            $el->allow_multiple(true);
                        }
                    }
                    elseif ( $_options = \Arr::get($options, 'form.options', false) )
                    {
                        $el = \Gasform\Input_Select::forge();
                        
                        foreach ( $_options as $value => $content )
                        {
                            $el[$value] = \Gasform\Input_Option::forge( \Lang::get($content, array(), false) ? : $content , $value, array());
                        }
                        
                        if ( false !== ( $default = \Arr::get($options, 'default', false) ) )
                        {
                            $el->populate($default);
                        }
                    }
                    else
                    {
                        continue;
                    }
                    
                    /**
                     * @todo  We should fix this, it isn't correct to do it like this
                     */
                    if ( ! isset($el) )
                    {
                        continue;
                    }
                    
                    ( $help = \Arr::get($options, 'form.help', false) ) && $el->set_meta('help', $help);
                    
                    \Arr::get($options, 'validation', false) && $el->set_meta('validation', \Arr::get($options, 'validation'));
                    
                    if ( in_array('required', \Arr::get($options, 'validation', array())) )
                    {
                        $el = $el->set_attribute('required', 'required');
                    }
                    
                    $form[$p] = $el->set_name($p)->set_label($label);
                break;
                
                case 'checkbox':
                case 'radio':
                    if ( $_options = \Arr::get($options, 'form.options', false) )
                    {
                        $group_class = '\\Gasform\\Input_' . ucwords($type) . 'Group';
                        $toggle_class = '\\Gasform\\Input_' . ucwords($type);
                        $group = new $group_class();
                        
                        foreach ( $_options as $value => $content )
                        {
                            $group[$value] = $toggle_class::forge(null, $value, array())
                                ->set_label( \Lang::get($content, array(), false) ? : $content );
                        }
                        
                        $group->set_name($p)->set_label($label);
                        
                        ( $help = \Arr::get($options, 'form.help', false) ) && $group->set_meta('help', $help);
                        
                        \Arr::get($options, 'validation', false) && $group->set_meta('validation', \Arr::get($options, 'validation'));
                        
                        if ( false !== ( $default = \Arr::get($options, 'default', false) ) )
                        {
                            $group->populate(array($group->get_name() => $default));
                        }
                        
                        $form[$p] = $group;
                    }
                break;
            }
        }
        
        static::$_forms_cached[$me] =& $form;
        
        return $form->populate($this->to_array());
    }
    
    
    /**
     * Makes the data for the object to a form element (a select) if allowed
     * 
     * @access  public
     * @static
     * @param   string  $content    Column of data to use as the displayed value
     * @param   string  $value      Column of data to use as the value submitted
     * 
     * @return  \Gasform\Input_Select
     */
    public static function to_form_element($content = null, $value = null)
    {
        $me = get_called_class();
        
        if ( ! ( isset(static::$_form_element_support) && static::$_form_element_support === true ) )
        {
            throw new \RuntimeException('Model ' . $me . ' does not support method to_form_element');
        }
        
        $content OR $content = static::$_form_element_options['content'];
        $value   OR $value   = static::$_form_element_options['value'];
        
        // Check if we already processed the form element with the given content and value
        $hash = md5($me . $content . $value);
        if ( isset(static::$_form_elements_cached[$hash]) )
        {
            return static::$_form_elements_cached[$hash];
        }
        
        // Load the gasform package and create a new form select item
        \Package::load('gasform');
        $select = \Gasform\Input_Select::forge();
        
        // Get the rows from the table
        $query = \DB::select($content, $value)
            ->from(static::table());
        
        // Default order by on the model? Then apply it
        if ( $order_by = static::condition('order_by') )
        {
            foreach ( $order_by as $col => $dirn )
            {
                $query = $query->order_by($col, $dirn);
            }
        }
        
        // Default where on the model? Apply it here
        if ( $where = static::condition('where') )
        {
            foreach ( $where as $_where )
            {
                $query = $query->where($_where[0], $_where[1], $_where[2]);
            }
        }
        
        // Get the options
        if ( $options = $query->execute() )
        {
            $options = $options->as_array($content, $value);
            
            // And parse them
            foreach ( $options as $_content => $_value )
            {
                $select[] = \Gasform\Input_Option::forge($_content, $_value, array());
            }
        }
        
        return static::$_form_elements_cached[$hash] = $select;
    }
    
}
