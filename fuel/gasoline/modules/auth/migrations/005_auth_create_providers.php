<?php namespace Fuel\Migrations;

class Auth_create_providers {
    
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
            $table = \Config::get('gasauth.table_name', 'users') . '_providers';
        }
        
        if ( isset($table) )
        {
            \DBUtil::create_table($table, array(
                'id'            => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
                'parent_id'     => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'provider'      => array('type' => 'varchar', 'constraint' => 255),
                'uid'           => array('type' => 'varchar', 'constraint' => 255),
                'secret'        => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
                'access_token'  => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
                'expires'       => array( 'type' => 'int', 'constraint' => 12, 'default' => 0, 'null' => true),
                'refresh_token' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
                'user_id'       => array('type' => 'bigint', 'constraint' => 16, 'default' => 0, 'unsigned' => true),
                'created_at'    => array('type' => 'int', 'constraint' => 11, 'default' => 0, 'unsigned' => true),
                'updated_at'    => array('type' => 'int', 'constraint' => 11, 'null' => true, 'unsigned' => true),
            ), array('id'));
            
            \DBUtil::create_index($table, 'parent_id', 'parent_id');
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
            $table = \Config::get('gasauth.table_name', 'users') . '_providers';
        }
        
        if ( isset($table) )
        {
            // drop the users remote table
            \DBUtil::drop_table($table);
        }
    }
    
}
