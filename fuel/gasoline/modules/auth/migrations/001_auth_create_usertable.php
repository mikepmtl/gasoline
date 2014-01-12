<?php namespace Fuel\Migrations;

class Auth_create_usertable {
    
    function up()
    {
        // get the driver used
        \Config::load('auth', true);
        
        $drivers = \Config::get('auth.driver', array());
        is_array($drivers) OR $drivers = array($drivers);
        
        if ( in_array('Gasauth', $drivers) )
        {
            // get the tablename
            \Config::load('gasauth', true);
            $table = \Config::get('gasauth.table_name', 'users');
            
            // table users
            \DBUtil::create_table($table, array(
                'id'                => array('type' => 'bigint', 'constraint' => 16, 'auto_increment' => true),
                'username'          => array('type' => 'varchar', 'constraint' => 50),
                'password'          => array('type' => 'varchar', 'constraint' => 255),
                'group_id'          => array('type' => 'int', 'constraint' => 11, 'default' => 1, 'unsigned' => true),
                'email'             => array('type' => 'varchar', 'constraint' => 255),
                'last_login'        => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
                'previous_login'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
                'login_hash'        => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
                'user_id'           => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'created_at'        => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'        => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('id'));
            
            // add a unique index on username and email
            \DBUtil::create_index($table, array('username', 'email'), 'username', 'UNIQUE');
            
            // table users_meta
            \DBUtil::create_table($table . '_metadata', array(
                'id'            => array('type' => 'bigint', 'constraint' => 16, 'auto_increment' => true, 'unsigned' => true),
                'parent_id'     => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'attribute'     => array('type' => 'varchar', 'constraint' => 255),
                'value'         => array('type' => 'text'),
                'user_id'       => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('id'));
        }
    }
    
    
    function down()
    {
        // get the driver used
        \Config::load('auth', true);
        
        $drivers = \Config::get('auth.driver', array());
        is_array($drivers) OR $drivers = array($drivers);
        
        if ( in_array('gasauth', $drivers) )
        {
            // get the tablename
            \Config::load('gasauth', true);
            $table = \Config::get('gasauth.table_name', 'users');
            
            // drop the admin_users table
            \DBUtil::drop_table($table);
            
            // drop the admin_users_meta table
            \DBUtil::drop_table($table . '_metadata');
            
            // drop the admin_users_user_role table
            \DBUtil::drop_table($table . '_user_roles');
            
            // drop the admin_users_user_perms table
            \DBUtil::drop_table($table . '_user_permissions');
        }
    }
    
}
