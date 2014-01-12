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
    
    /**
     * Can be any of the following
     * 
     * boolean  Indicates whether this model supports "as checkbox", "as radio",
     *          and "as select" (true supports all, false supports none)
     * string   Indicates which type the model supports. Can be any of "checkbox",
     *          "radio, or "select"
     * array    Indicates which types the model supports. For example
     *          array('radio', 'select')
     * 
     */
    // protected static $_form_element_support = false;
    
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
                case 'file':
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
                    
                    \Arr::get($options, 'validation', false) && $el->set_validation(\Arr::get($options, 'validation'));
                    
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
                    
                    ( $help = \Arr::get($options, 'form.help', false) ) && $el->set_meta('help', \Lang::get($help, array(), false) ? : $help);
                    
                    \Arr::get($options, 'validation', false) && $el->set_validation(\Arr::get($options, 'validation'));
                    
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
                        
                        ( $help = \Arr::get($options, 'form.help', false) ) && $group->set_meta('help', \Lang::get($help, array(), false) ? : $help);
                        
                        \Arr::get($options, 'validation', false) && $group->set_validation(\Arr::get($options, 'validation'));
                        
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
    public static function to_form_element($type = 'select')
    {
        $me = get_called_class();
        
        // Check that the model supports form element output
        if ( ! isset(static::$_form_element_support) )
        {
            throw new \RuntimeException('Model ' . $me . ' does not support method to_form_element');
        }
        else
        {
            if ( ( is_bool(static::$_form_element_support) && static::$_form_element_support !== true )
                || ( is_string(static::$_form_element_support) && $type != static::$_form_element_support )
                || ( is_array(static::$_form_element_support) && ! in_array($type, static::$_form_element_support) )
                )
            {
                throw new \RuntimeException('Model ' . $me . ' does not support to_form_element with specified type [' . $type . ']');
            }
            elseif ( ! in_array($type, array('checkbox', 'radio', 'select')) )
            {
                throw new \RuntimeException('Invalid form type [' . $type .'] given when calling ' . $me . '::to_form_element()');
            }
            // else
            // {
            //     throw new \RuntimeException('Model ' . $me . ' has an invalid definiton for property $_form_element_support');
            // }
        }
        
        // Correct options defined?
        if ( ! ( isset(static::$_form_element_options) && is_array(static::$_form_element_options) ) )
        {
            throw new \RuntimeException('Model ' . $me . ' has an invalid definition for property $_form_element_options');
        }
        
        // Get the options
        $content = static::$_form_element_options['content'];
        $value   = static::$_form_element_options['value'];
        
        // See if we have already cached the form element
        $hash = md5($me . $type . $content . $value);
        
        if ( isset(static::$_form_elements_cached[$hash]) )
        {
            return static::$_form_elements_cached[$hash];
        }
        
        // Nothing cached, so load the gasform package and proceed with getting
        // data in
        \Package::load('gasform');
        
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
        
        // Query the database
        $options = $query->execute();
        
        // Make code easier and extract the container's element class and the
        // element's class
        $container  = ( $type == 'select' ? '\\Gasform\\Input_Select' : '\\Gasform\\Input_' . ucwords($type) . 'Group' );
        $element    = ( $type == 'select' ? '\\Gasform\\Input_Option' : '\\Gasform\\Input_' . ucwords($type) );
        
        // Forge a new container
        $el = $container::forge();
        
        // Got options?
        if ( $options )
        {
            // Get them as array with key as per $content and value as per $value
            $options = $options->as_array($content, $value);
            
            // Loop over them results
            foreach ( $options as $_content => $_value )
            {
                // Forge a new element and assign it to the element container
                $el[$_value] = $element::forge($_content, $_value)
                    ->set_label(e($_content));
            }
        }
        
        // Return the cached form element we just created
        return static::$_form_elements_cached[$hash] = $el;
    }
    
}
