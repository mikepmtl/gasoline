<?php namespace Fuel\Migrations;

class Auth_create_grouptable {
    
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
            
            // table users_group
            \DBUtil::create_table($table . '_groups', array(
                'id'            => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
                'name'          => array('type' => 'varchar', 'constraint' => 255),
                'slug'          => array('type' => 'varchar', 'constraint' => 255),
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
        
        if ( in_array('Gasauth', $drivers) )
        {
            // get the tablename
            \Config::load('gasauth', true);
            $table = \Config::get('gasauth.table_name', 'users');
            
            // drop the admin_users_group table
            \DBUtil::drop_table($table . '_groups');
            
            // drop the admin_users_group_role table
            \DBUtil::drop_table($table . '_group_roles');
            
            // drop the admin_users_group_perms table
            \DBUtil::drop_table($table . '_group_permissions');
        }
    }

}
