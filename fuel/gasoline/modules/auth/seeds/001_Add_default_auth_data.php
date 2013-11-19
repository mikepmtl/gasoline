<?php namespace Seeds;

class Add_default_auth_data {
    
    public function up()
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
            
            // Create all roles
            $denied = \Model\Auth_Role::forge(array('name' => 'denied', 'filter' => 'D'));
            $denied->save();
            
            $public = \Model\Auth_Role::forge(array('name' => 'public'));
            $public->save();
            
            $user = \Model\Auth_Role::forge(array('name' => 'user'));
            $user->save();
            
            $admin = \Model\Auth_Role::forge(array('name' => 'administrator', 'filter' => 'A'));
            $admin->save();
            
            
            // Create all groups
            $guests = \Model\Auth_Group::forge(array('name' => 'Guests'));
            $guests->roles[] = $public;
            $guests->save();
            
            $banned = \Model\Auth_Group::forge(array('name' => 'Banned'));
            $banned->roles[] = $denied;
            $banned->save();
            
            $users = \Model\Auth_Group::forge(array('name' => 'Users'));
            $users->roles[] = $user;
            $users->save();
            
            $admins = \Model\Auth_Group::forge(array('name' => 'Administrators'));
            $admins->roles[] = $admin;
            $admins->save();
            
            // Create the guest user
            list($guest_id, $affected) = \DB::insert($table)->set(
                array(
                    'username'          => 'guest',
                    'password'          => 'YOU CAN NOT USE THIS TO LOGIN',
                    'email'             => '',
                    'group_id'          => $guests->id,
                    'last_login'        => 0,
                    'previous_login'    => 0,
                    'login_hash'        => '',
                    'user_id'           => 0,
                    'created_at'        => time(),
                )
            )->execute();
            
            // Adjust the guest user's ID to be 0 (auto-increment has set the value
            //  to something greater than 0)
            \DB::update($table)
                ->set(array('id' => 0))
                ->where('id', '=', $guest_id)
                ->execute();
            
            // Finally, create the admin user
            \Auth::instance()->create_user('admin', 'admin', 'admin@example.com', $admins->id, array('fullname' => 'System Administrator'));
        }
    }
    
    public function down()
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
            
            // empty the user, group and role tables
            \DBUtil::truncate_table($table);
            \DBUtil::truncate_table($table . '_groups');
            \DBUtil::truncate_table($table . '_roles');
            \DBUtil::truncate_table($table . '_group_roles');
        }
    }
    
}

/* End of file 001_Add_default_auth_data.php */
/* Location: ./fuel/app/seeds/001_Add_default_auth_data.php */
