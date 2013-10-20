<?php namespace Fuel\Migrations;

class Auth_create_permissiontables {
    
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
            
            // table users_perms
            \DBUtil::create_table($table . '_permissions', array(
                'id'            => array('type' => 'bigint', 'constraint' => 16, 'auto_increment' => true),
                'area'          => array('type' => 'varchar', 'constraint' => 255),
                'permission'    => array('type' => 'varchar', 'constraint' => 255),
                'description'   => array('type' => 'varchar', 'constraint' => 255),
                'user_id'       => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('id'));
            
            // add a unique index on group and permission
            \DBUtil::create_index($table . '_permissions', array('area', 'permission'), 'permission', 'UNIQUE');
            
            // table users_user_perms
            \DBUtil::create_table($table . '_user_permissions', array(
                'user_id'       => array('type' => 'bigint', 'constraint' => 16, 'unsigned' => true),
                'perms_id'      => array('type' => 'bigint', 'constraint' => 16, 'unsigned' => true),
                'actions'       => arary('type' => 'text', 'null' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('user_id', 'perms_id'));
            
            // table users_group_perms
            \DBUtil::create_table($table . '_group_permissions', array(
                'group_id'      => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
                'perms_id'      => array('type' => 'bigint', 'constraint' => 16, 'unsigned' => true),
                'actions'       => arary('type' => 'text', 'null' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('group_id', 'perms_id'));
            
            // table users_role_perms
            \DBUtil::create_table($table . '_role_permissions', array(
                'role_id'       => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
                'perms_id'      => array('type' => 'bigint', 'constraint' => 16, 'unsigned' => true),
                'actions'       => arary('type' => 'text', 'null' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('role_id', 'perms_id'));
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
            
            // drop the admin_users_perms table
            \DBUtil::drop_table($table . '_permissions');
        }
    }
    
}
