<?php namespace Fuel\Migrations;

class Auth_create_roletable {
    
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
            
            // table users_role
            \DBUtil::create_table($table . '_roles', array(
                'id'            => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
                'name'          => array('type' => 'varchar', 'constraint' => 255),
                'filter'        => array('type' => 'enum', 'constraint' => "'', 'A', 'D', 'R'", 'default' => ''),
                'user_id'       => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('id'));
            
            // table users_user_role
            \DBUtil::create_table($table . '_user_roles', array(
                'user_id' => array('type' => 'bigint', 'constraint' => 16, 'unsigned' => true),
                'role_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            ), array('user_id', 'role_id'));
            
            // table users_group_role
            \DBUtil::create_table($table . '_group_roles', array(
                'group_id'  => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
                'role_id'   => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            ), array('group_id', 'role_id'));
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
            
            // drop the admin_users_role table
            \DBUtil::drop_table($table . '_roles');
            
            // drop the admin_users_role_perms table
            \DBUtil::drop_table($table . '_role_permissions');
        }
    }
    
}
