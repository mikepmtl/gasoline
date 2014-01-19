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

class Auth extends \Controller\Base {
    
    public function before()
    {
        parent::before();
        
        \Lang::load('messages/auth', 'auth.messages.auth');
    }
    
    
    public function action_login()
    {
        if ( \Auth::check() )
        {
            return \Response::redirect_back(\Input::get('redirect', \Router::get('dashboard.user') ? : '/'));
        }
        
        $form = new \Gasform\Form();
        
        if ( \Input::get('redirect') )
        {
            $form->set_attribute('action', \Uri::create(null, array(), array('redirect' => \Input::get('redirect'))));
            
            $form['redirect'] = \Gasform\Input_Hidden::forge('redirect', \Input::get('redirect', ''), array());
        }
        
        $form['remember_default'] = \Gasform\Input_Hidden::forge('remember', '', array());
        
        $form['identity'] = \Gasform\Input_Text::forge('identity')->set_label('Identity');
        $form['password'] = \Gasform\Input_Password::forge('password')->set_label('Password');
        $cbx_group = \Gasform\Input_CheckboxGroup::forge();
        $cbx_group['remember'] = \Gasform\Input_Checkbox::forge('remember', 'me', array())
            ->set_label('Remember me');
        $form['remember'] = $cbx_group;
        
        $btngroup = new \Gasform\Input_ButtonGroup();
        $btngroup['submit'] = \Gasform\Input_Submit::forge('submit', 'Login', array());
        $btngroup['recover'] = \Gasform\Input_Button::forge('recover', 'Recover', array());
        
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
            return \Response::redirect_back(\Input::get('redirect', \Router::get('dashboard.user') ? : '/'));
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
            
            \Message\Container::push(\Message\Item::forge('success', __('auth.messages.auth.login.success.message', array('username' => \Auth::get_screen_name())), __('auth.messages.auth.login.success.heading'))->is_flash(true));
            
            return \Response::redirect(\Input::post('redirect', \Router::get('dashboard.user') ? : '/'));
        }
        
        \Message\Container::instance('login-form')->push(\Message\Item::forge('warning', __('auth.messages.auth.login.invalid.message'), __('auth.messages.auth.login.invalid.heading')));
        
        $this->action_login();
    }
    
    
    public function action_logout()
    {
        if ( ! \Auth::check() )
        {
            return \Response::redirect_back('/');
        }
        
        $username = \Auth::get_screen_name();
        
        \Auth::dont_remember_me();
        
        \Auth::logout();
        
        \Message\Container::push(\Message\Item::forge('success', __('auth.messages.auth.logout.success.message', array('username' => $username)), __('auth.messages.auth.logout.success.heading'))->is_flash(true));
        
        return \Response::redirect('/');
    }
    
}

/* End of file auth.php */
/* Location: ./fuel/gasoline/modules/auth/classes/controller/auth.php */
