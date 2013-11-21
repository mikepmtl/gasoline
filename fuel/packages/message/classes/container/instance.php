<?php namespace Message;

class Container_Instance extends \Gasoline\DataContainer {
    
    /**
     * Keeps new flash messages that will be stored on shutdown
     * 
     * @access  protected
     * @var     array
     */
    protected $flash = array();
    
    /**
     * Storage for regular messages and flash messages read on construct
     * 
     * @access  protected
     * @var     array
     */
    protected $messages = array();
    
    /**
     * The name of the container
     * 
     * Since every container knows just about the messages of itself, the object
     * must also know about its name
     * 
     * @access  protected
     * @var     string
     */
    protected $name;
    
    
    
    
    
    /**
     * The class constructor
     * 
     * @access  public
     * @param   string  $name       Name of the container (the same as used on ::forge())
     * @param   array   $data       Initial set of items to set
     * @param   boolean $readonly   Whether the container is read-only or not
     * 
     * @return  void
     */
    public function __construct($name, array $data = array(), $readonly = false)
    {
        $this->name = $name;
        
        // Get the items from our storage driver
        $flash = Container::storage()->get_flash($this->name, array());
        
        logger(\Fuel::L_DEBUG, '[' . $this->name . '] created with ' . count($flash) . ' flash messages retrieved from storage', __METHOD__);
        
        // Loop over all flash messages
        foreach ( $flash as $message )
        {
            // And convert them to message item objects, with these defaults
            // $message = \Arr::merge(array('type' => null, 'message' => null, 'heading' => null, 'attributes' => array()), $message);
            
            if ( $message['type'] && $message['message'] )
            {
                $this->messages[] = new Item($message['type'], $message['message'], $message['heading'], $message['attributes']);
            }
        }
        
        // Finally, call the parent's constructor with the items as the container's data
        parent::__construct($data, $readonly);
    }
    
    
    /**
     * Render the container by rendering all messages one by one
     * 
     * @access  public
     * @param   string  $tag        A tag to wrap the result in. Only applicable
     *                              if theme support is disabled
     * @param   array  $attributes  Attributes to set on the tag
     * 
     * @return  string              Returns parsed string of messages
     */
    public function render($tag = null, $attributes = array())
    {
        // Get a theme instance
        $theme = \Theme::instance();
        
        // That'll keep the parsed messages
        $parsed = '';
        
        // Merge the already set attributes with the provided attributes
        $attributes = \Arr::merge($this->get_data(), $attributes);
        
        // Get the messages of the given type
        if ( count($this->messages) )
        {
            // Use views?
            if ( \Config::get('message.render_html', false) === false )
            {
                // Then pass messages to the theme to find the view
                $parsed = $theme->view(
                    \Config::get('message.view_file', '_templates/messages'),
                    array(
                        'messages'      => $this->messages,
                        'attributes'    => $attributes,
                    ),
                    false
                );
            }
            // No views, pure HTML! 4tw
            else
            {
                // Html to use for the container and message
                $html_container = \Config::get('message.html.container', '<div id="messages">:messages</div>');
                $html_item      = \Config::get('message.html.item', '<div class="message :type">{<h5>:heading</h5>}<p>:message</p></div>');
                
                // Loop over all messages
                foreach ( $this->messages as $message )
                {
                    // We will do some regex to see if the heading is requested
                    // in the message HTML. If so, get the tags and replace just
                    // :heading with the heading of the message
                    $msg    = preg_replace('/{(.*)?(:heading)(.*)?}(.*)/', ( ( $heading = $message->get_heading() ) ? '$1' . $heading . '$3$4' : '$4' ), $html_item);
                    
                    // And parse the message
                    $parsed .= str_replace(array(':message', ':type'), array($message->get_message(), $message->get_type()), $msg);
                }
                
                // Use html_container to parse the type and all messages
                $parsed = str_replace(':messages', $parsed, $html_container);
            }
            
            // And unset the messages that we have just parsed
            \Config::get('message.unset_after_render', true) === true && $this->messages = array();
        }
        
        return ( $tag ? html_tag($tag, $attributes, $parsed) : $parsed );
    }
    
    
    /**
     * Shutdown event that will store all messages
     * 
     * @access  public
     * @return  void
     */
    public function shutdown()
    {
        logger(\Fuel::L_DEBUG, '[' . $this->name . ']', __METHOD__);
        if ( $this->flash ) 
        {
            $to_store = array();
            
            foreach ( $this->flash as $flash )
            {
                $to_store[] = $flash->to_array();
            }
            
            Container::storage()->set_flash($this->name, $to_store);
        }
    }
    
    
    /**
     * Push a message to the stack of messages
     * 
     * 
     * @access  public
     * @param   Item    $item   Message item to push onto the stack
     * 
     * @return  Container_Instance
     */
    public function push(Item $item)
    {
        return $this->enqueue($item, false);
    }
    
    
    /**
     * Prepend a message to the stack of messages
     * 
     * 
     * @access  public
     * @param   Item    $item   Message item to prepend to the stack
     * 
     * @return  Container_Instance
     */
    public function unshift(Item $item)
    {
        return $this->enqueue($item, true);
    }
    
    
    /**
     * General enqueue method to append or prepend to the storage
     * 
     * 
     * @access  public
     * 
     * @param   Item    $item       Message item to prepend or append to the storage
     * @param   boolean $prepend    Whether to prepend.  Defaults to false (== append)
     * 
     * @return  Message_Container
     */
    public function enqueue(Item $item, $prepend = false)
    {
        if ( $item->is_flash() )
        {
            $prepend ? array_unshift($this->flash, $item) : array_push($this->flash, $item);
        }
        else
        {
            $prepend ? array_unshift($this->messages, $item) : array_push($this->messages, $item);
        }
        
        return $this;
    }
    
}
