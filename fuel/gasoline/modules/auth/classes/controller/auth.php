<?php namespace Auth\Controller;

class Auth extends \Controller\Base {
    
    public function action_login()
    {
        if ( \Auth::check() )
        {
            return \Response::redirect_back(\Router::get('dashboard.user') ? : '/');
        }
        
        $form = new \Gasform\Form();
        
        $remember_default = new \Gasform\Input_Hidden('remember', array(), '');
        $form['remember_default'] = $remember_default;
        $redirect = new \Gasform\Input_Hidden('redirect', array(), \Input::get('redirect', ''));
        $form['redirect'] = $redirect;
        
        $identity = new \Gasform\Input_Text('identity');
        $form['identity'] = $identity->set_label('Identity');
        $password = new \Gasform\Input_Password('password');
        $form['password'] = $password->set_label('Password');
        $cbx_group = new \Gasform\Input_CheckboxGroup();
        $remember = new \Gasform\Input_Checkbox('remember', array(), 'me');
        $cbx_group['remember'] = $remember->set_label('Remember me');
        $form['remember'] = $cbx_group;
        
        $btngroup = new \Gasform\Input_ButtonGroup();
        $submit = new \Gasform\Input_Submit('submit', array(), 'Login');
        $btngroup['submit'] = $submit;
        $recover = new \Gasform\Input_Button('recover', array(), 'Recover');
        $btngroup['recover'] = $recover;
        
        $form['btngroup'] = $btngroup;
        
        static::$theme
            ->set_template('_templates/blank');
        
        $this->view = static::$theme
            ->view('auth/login')
            ->set('form', $form);
    }
    
    public function post_login()
    {
        if ( \Auth::check() )
        {
            return \Response::redirect_back(\Router::get('dashboard.user') ? : '/');
        }
        
        if ( \Auth::login(\Input::post('identity'), \Input::post('password')) )
        {
            if ( 'me' == \Input::post('remember', false) )
            {
                \Auth::remember_me();
            }
            else
            {
                \Auth::dont_remember_me();
            }
            
            return \Response::redirect(\Input::post('redirect', \Router::get('dashboard.user') ? : '/'));
        }
        
        $this->action_login();
    }
    
    public function action_logout()
    {
        if ( ! \Auth::check() )
        {
            return \Response::redirect_back('/');
        }
        
        \Auth::dont_remember_me();
        
        if ( \Auth::logout() )
        {
            \Message\Container::instance()->set(null, \Message\Item::forge('success', 'Logout successful', 'Bye bye'));
        }
        else
        {
            \Message\Container::instance()->set(null, \Message\Item::forge('danger', 'Logout failed', 'Somehow!'));
        }
        
        return \Response::redirect('/');
    }
    
    
    public function action_restricted()
    {
        if ( ! \Auth::check() )
        {
            return 'access denied';
        }
        
        return 'restricted';
    }
    
}
