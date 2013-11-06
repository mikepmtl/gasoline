<?php namespace Model;

class Post extends Base {
    
    protected static $_table_name = 'posts';
    
    protected static $_primary_key = array('id');
    
    protected static $_properties = array(
        'id',
        'title' => array(
            'label' => 'post.title',
            'form'  => array(
                'type'  => 'text',
            ),
            'validation' => array(
                'required',
                'max_length' => array(255),
            ),
        ),
        'author' => array(
            'label' => 'post.author',
            'form'  => array(
                'type'  => 'select',
            ),
            'validation' => array(
                'required',
                'max_length' => array(255),
            ),
            'relation' => array(
                'belongs_to',
                'author',
            ),
        ),
        'content' => array(
            'label' => 'post.content',
            'form'  => array(
                'type'  => 'textarea',
            ),
            // 'validation' => array(
            //     'required',
            // ),
        ),
        'status' => array(
            'label' => 'post.status',
            'form'  => array(
                'type'  => 'radio',
                'options' => array(
                    '-1'    => 'post.options.status.-1',
                    '0'     => 'post.options.status.0',
                    '1'     => 'post.options.status.1',
                ),
            ),
            'default' => -1,
            'validation' => array(
                'required',
                'max_length' => array(255),
            ),
        ),
        'created_at' => array(
            'label' => 'post.created_at',
            'form'  => array(
                'type'  => false,
            ),
        ),
        'updated_at' => array(
            'label' => 'post.updated_at',
            'form'  => array(
                'type'  => false,
            ),
        ),
    );
    
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );
    
    protected static $_belongs_to = array(
        'author'    => array(
            'key_from'  => 'author',
            'key_to'    => 'id',
            'model_to'  => '\\Model\\Auth_User',
        ),
    );
    
    
    public static function _init()
    {
        \Lang::load('post', 'post');
    }
    
}
