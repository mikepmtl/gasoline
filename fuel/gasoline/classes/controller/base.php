<?php namespace Gasoline\Controller;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

abstract class Base extends \Controller {
    
    /**
     * Keeps the theme instance for easier access
     * 
     * @static
     * @access  public
     * @var     \Fuel\Core\Theme
     */
    public static $theme = null;
    
    
    /**
     * Restrict access to the current controller action
     * 
     * @access  protected
     * 
     * @param   string|array    $permission     A string, or an array of permissions
     *                                          to check
     * @param   string          $redirect       Where to redirect to if access
     *                                          cannot be granted. Defaults to
     *                                          \Session::get('last_page')
     * 
     * @return  \Fuel\Core\Response             Returns a response object that
     *                                          redirects back to the page last
     *                                          accesed
     */
    protected static function restrict($permission, $redirect = null)
    {
        is_array($permission) OR $permission = array($permission);
        
        $permitted = false;
        
        foreach ( $permission as $perm )
        {
            $permitted = ( $permitted OR \Auth::has_access($perm) );
        }
        
        if ( ! $permitted )
        {
            \Message::flash('danger', __('message.access_denied.heading'), __('message.acccess_denied.body'));
            
            return \Response::redirect($redirect !== null ? $redirect : \Session::get('last_page', $redirect));
        }
    }
    
    
    /**
     * Initialize the theme instance
     * 
     * @access  protected
     * 
     * @return  void
     */
    protected function _init_theme()
    {
        $active = ( is_string(static::$theme) ? static::$theme : null );
        
        // Assign a theme-instance
        static::$theme = \Theme::instance();
        
        try
        {
            // Get the active theme's info
            $theme_info = static::$theme->active($active);
            
            // Set the template
            static::$theme->set_template($this->template);
            
            // Require the theme's bootstrap file (if any)
            if ( @file_exists($theme_info['path'] . 'bootstrap.php') )
            {
                // Then require it
                require_once($theme_info['path'] . 'bootstrap.php');
            }
        }
        catch ( \Exception $e )
        {
            static::$theme->set_template('_templates/default');
        }
        
        // Get the web-paths to the themes assets
        $css = static::$theme->asset_path('css');
        $img = static::$theme->asset_path('img');
        $js  = static::$theme->asset_path('js');
        
        // And assign them to be used inside the template
        static::$theme->get_template()->set('asset_paths', (object) compact('css', 'js', 'img'));
    }
    
    
    
    
    
    /**
     * Template to use for the controller. Can be any template desired as long
     * as it exists.
     * Must be a path relative to the theme's root dir.
     * 
     * @access  public
     * @var     string
     */
    public $template = '_templates/default';
    
    /**
     * This keeps the final view of the request. It will be set as content of
     * the theme automatically on theme rendering
     * 
     * @access  public
     * @var     \Fuel\Core\View
     */
    protected $view = '';
    
    
    /**
     * Runs before the controller action is run
     * 
     * Basically, what this does is set some default options (not yet, but in the
     * near future) and will also initialize the theme and populate it with default
     * partials.
     * 
     * @access  public
     * 
     * @return  void
     */
    public function before()
    {
        parent::before();
        
        // Initialize the theme
        $this->_init_theme();
        
        // This always annoyed me when debugging code, so we're seting the debug
        //  class to toggle open by default but only on the dev-environment
        \Fuel::$env == \Fuel::DEVELOPMENT && \Debug::$js_toggle_open = true;
    }
    
    
    /**
     * Runs after the controller action was run
     * 
     * It automatically detectes AJAX requests and sets the template accordingly
     * to the AJAX template (@todo)
     * 
     * @access  public
     * 
     * @param   \Fuel\Core\Response $response   A response object or null if the
     *                                          view of the controller shall be used
     *                                          and sent to the browser
     * 
     * @return [type]           [description]
     */
    public function after($response)
    {
        // Check the type of the given response. If response is empty, then the
        // controller called did not return anything, so we will set $response to
        // the view set by the controller;
        $response = ( is_null($response) ? $this->view : $response );
        
        // If the response is no \Response object...
        if ( ! $response instanceof \Response )
        {
            // Got an AJAX request?
            if ( \Input::is_ajax() )
            {
                // Set the template accordigly (The used template should just echo
                // the variable $content)
                static::$theme
                    ->set_template('_templates/ajax');
            }
            
            // Request was no HVMC request?
            if ( ! \Request::is_hmvc() )
            {
                if ( ! is_string($response) )
                {
                    // Check we have an object and a render method on that object
                    if ( ! ( is_object($response) && is_callable(array($response, 'render') ) ) )
                    {
                        // Nope, don't have one
                        throw new \FuelException(get_class(\Request::main()->controller_instance).' must return a string or an object with a render() method .');
                    }
                    
                    $response = $response->render();
                }
                
                // Set variable $content of the template to have the content of
                // the controller's view already rendered
                static::$theme
                    ->get_template()
                    ->set('content', $response , false);
                
                // Create the response to be the rendered theme
                $response = static::$theme->render();
            }
            
            // Create a new response object with whatever we determined above (a
            // view partial or the full page)
            $response = \Response::forge($response);
        }
        
        return parent::after($response);
    }
    
}

/* End of file base.php */
/* Location: ./fuel/gasoline/classes/controller/base.php */
