<?php namespace Controller;
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * \Response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Welcome extends \Controller
{

    /**
     * The basic welcome message
     *
     * @access  public
     * @return  \Response
     */
    public function action_index()
    {
        \Debug::$js_toggle_open = true;
        
        $form = \Model\Post::to_form();
        $btngroup = new \Gasform\Input_ButtonGroup();
        $btn = new \Gasform\Input_Submit('submit');
        $btngroup[] = $btn->set_value('Submit');
        $btn = new \Gasform\Input_Button('cancel');
        $btngroup[] = $btn->set_value('Cancel');
        $form[] = $btngroup;
        $renderer = new \Gasform\Render_Bootstrap();
        
        \Input::method() === "POST" && $form->repopulate(\Input::post());
        
        return \Response::forge(\View::forge('welcome/index')->set('form', $form, false)->set('form_rendered', $renderer->render($form), false));
    }

    /**
     * A typical "Hello, Bob!" type example.  This uses a \ViewModel to
     * show how to use them.
     *
     * @access  public
     * @return  \Response
     */
    public function action_hello()
    {
        return \Response::forge(\ViewModel::forge('welcome/hello'));
    }

    /**
     * The 404 action for the application.
     *
     * @access  public
     * @return  \Response
     */
    public function action_404()
    {
        return \Response::forge(\ViewModel::forge('welcome/404'), 404);
    }
}
