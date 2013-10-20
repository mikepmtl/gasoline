<?php namespace Auth\Controller;

class Auth extends \Gasoline\Controller\Base {
    
    public function action_login()
    {
        if ( \Auth::check() )
        {
            return \Response::redirect_back(\Router::get('dashboard') ? : '/');
        }
        
        echo \Form::open();
        echo \Form::input('identity', '');
        echo \Form::password('password');
        echo \Form::submit('Submit');
        echo \Form::close();
    }
    
    public function post_login()
    {
        if ( \Auth::check() )
        {
            return \Response::redirect_back(\Router::get('dashboard') ? : '/');
        }
        
        if ( \Auth::login(\Input::post('identity'), \Input::post('password')) )
        {
            return \Response::redirect(\Input::post('redirect', \Router::get('dashboard') ? : '/'));
        }
        
        $this->action_login();
    }
    
    public function action_logout()
    {
        if ( ! \Auth::check() )
        {
            return \Response::redirect_back('/');
        }
        
        \Auth::logout();
        
        return \Response::redirect_back('/');
    }
    
}
