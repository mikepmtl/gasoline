<?php namespace Profile\Controller;

class Profile extends \Controller\Authenticated {
    
    protected static $me;
    
    protected static $user;
    
    public $default_action = 'view';
    
    
    public function before()
    {
        parent::before();
        
        static::$me = \Auth::get_user();
        
        \Breadcrumb\Container::instance()->set_crumb('profile', 'Profile');
    }
    
    
    public function router($method, $arguments)
    {
        $controller_method = \Input::method() . '_' . $method;
        
        if ( ! method_exists($this, $controller_method) )
        {
            $controller_method = 'action_' . $method;
        }
        
        if ( method_exists($this, $controller_method) )
        {
            static::$user = static::$me;
            
            \Breadcrumb\Container::instance()->set_crumb('profile', 'Me');
            
            return call_fuel_func_array(array($this, $controller_method), $arguments);
        }
        
        $query = \Model\Auth_User::query()
            ->where('id', '!=', '0')
            ->where_open()
                ->where('username', '=', $method)
                ->or_where('id', '=', $method)
            ->where_close();
        
        if ( $user = $query->get_one() )
        {
            /**
             * @todo Make this check if $method might be a module, so that we can
             * delegate this to the module to display e.g., the gallery of the
             * given user.
             * 
             */
            $method = 'view';
            
            $arguments OR $arguments = array();
            
            $controller_method = \Input::method() . '_' . $method;
            
            if ( ! method_exists($this, $controller_method) )
            {
                $controller_method = 'action_' . $method;
            }
            
            if ( method_exists($this, $controller_method) )
            {
                static::$user = $user;
                
                return call_fuel_func_array(array($this, $controller_method), $arguments);
            }
        }
        
        throw new \HttpNotFoundException();
    }
    
    
    public function action_view()
    {
        if ( static::$user->id == static::$me->id )
        {
            $this->view = static::$theme
                ->view('profile/view/me')
                ->set('user', static::$user);
        }
        else
        {
            \Breadcrumb\Container::instance()->set_crumb('profile/' . static::$user->username, e(static::$user->username));
            
            try
            {
                $widgets = \Cache::get('profile.user_' . static::$user->id);
            }
            catch ( \CacheNotFoundException $e )
            {
                $widgets = array();
                
                // Loop through all modules and display their dashboard widget
                foreach ( \Config::get('module_paths') as $module_path )
                {
                    if ( ! ( $controller = glob($module_path . '*/classes/controller/widgets/profile*') ) )
                    {
                        continue;
                    }
                    
                    foreach ( $controller as $module )
                    {
                        $path = explode(DS, str_replace($module_path, '', $module));
                        
                        $_module = $path[0];
                        
                        try
                        {
                            $response = \Request::forge($_module . '/widgets/profile/user', false)->execute()->response();
                            
                            $widgets[] = array(
                                'module'    => $_module,
                                'body'      => $response->body,
                            );
                        }
                        catch ( \Exception $e ) {}
                    }
                }
                
                \Cache::set('profile.user_' . static::$user->id, $widgets);
            }
            
            $this->view = static::$theme
                ->view('profile/view')
                ->set('user', static::$user)
                ->set('widgets', $widgets);
        }
    }
    
    
    public function action_password()
    {
        static::restrict('profile.user[password]');
        
        \Breadcrumb\Container::instance()->set_crumb('profile/password', 'Password');
        
        return microtime();
    }
    
    
    // public function action_me($scope = null)
    // {
    //     if ( ! is_null($scope) )
    //     {
    //         $arguments = func_get_args();
            
    //         $method = '_me_' . \Input::method() . '_' . $scope;
            
    //         if ( ! method_exists($this, $method) )
    //         {
    //             $method = '_me_action_' . $scope;
    //         }
            
    //         if ( method_exists($this, $method) )
    //         {
    //             return call_fuel_func_array(array($this, $method), array_splice($arguments, 1));
    //         }
            
    //         throw new \HttpNotFoundException();
    //     }
        
    //     $this->view = static::$theme
    //         ->view('profile/me')
    //         ->set('user', static::$me);
    // }
    
    
    // public function _me_action_password()
    // {
    //     static::restrict('user.update[password]');
        
    //     $form = \Gasform\Form::forge();
        
    //     $form['id'] = \Gasform\Input_Hidden::forge('id', static::$me->id);
        
    //     $form['new_password'] = \Gasform\Input_Password::forge('new_password')
    //         ->set_label('New Password')
    //         ->set_validation(array(
    //             'required',
    //             'match_field' => array('new_password_repeat'),
    //         ));
    //     $form['new_password_repeat'] = \Gasform\Input_Password::forge('new_password_repeat')
    //         ->set_label('New Password (repeat)')
    //         ->set_validation(array(
    //             'required',
    //             'match_field' => array('new_password'),
    //         ));
    //     $form['current_password'] = \Gasform\Input_Password::forge('current_password')
    //         ->set_label('Current password')
    //         ->set_validation(array(
    //             'required',
    //             'match_users_password',
    //         ));
        
    //     $btn_group = new \Gasform\Input_ButtonGroup();
    //     $btn_group['submit'] = \Gasform\Input_Submit::forge('submit', __('button.update'), array());;
        
    //     $form['btn-group'] = $btn_group;
        
    //     $this->view = static::$theme
    //         ->view('user/account/password')
    //         ->set('scope', 'password')
    //         ->set('form', $form)
    //         ->set('user', static::$me);
    // }
    
    
    // public function router($method, $arguments)
    // {
    //     \Debug::dump(func_get_args());
    //     die();
        
    //     // Accessible via
    //     // /profile/<username_or_id>
    //     // /profile/<username_or_id>/gallery (also something like this? Then make this an HMVC request to gallery/profile/<username_or_id>)
    //     // /profile/me
    //     // /profile/me/password
    //     // /profile/me/essentials
        
    //     if ( $method == 'me' )
    //     {
    //         $user = static::$me;
    //     }
    //     else
    //     {
    //         $user = null;
    //     }
        
    //     // $username_or_id = ( $method ? ;
    //     // $method = 'display';
        
    //     // if ( ! $user = \Model\Auth_User::find_by_id_or_username($username_or_id) )
    //     // {
    //     //     throw new \HttpNotFoundException();
    //     // }
        
    //     // // check if a input specific method exists
    //     // $controller_method = strtolower(\Input::method()) . '_' . $method;
        
    //     // // fall back to action_ if no rest method is provided
    //     // if ( ! method_exists($this, $controller_method) )
    //     // {
    //     //     $controller_method = 'action_' . $method;
    //     // }
        
    //     // // check if the action method exists
    //     // if ( method_exists($this, $controller_method) )
    //     // {
    //     //     return call_fuel_func_array(array($this, $controller_method), $arguments);
    //     // }
        
    //     // throw new \HttpNotFoundException();
    // }
    
}
