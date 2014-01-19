<?php namespace Seeds;

class Rename_auth_permissions {
    
    protected static $permissions = array(
        'auth' => array(
            'admin:users' => array(
                'list',
                'create',
                'read',
                'update',
                'delete',
            ),
            'admin:groups' => array(
                'list',
                'create',
                'read',
                'update',
                'delete',
            ),
            'admin:roles' => array(
                'list',
                'create',
                'read',
                'update',
                'delete',
            ),
            'admin:permissions' => array(
                'user',
                'user:create',
                'user:update',
                'user:delete',
                'group',
                'group:create',
                'group:update',
                'group:delete',
                'role',
                'role:create',
                'role:update',
                'role:delete',
            ),
        ),
    );
    
    protected static $old = array(
        'auth' => array(
            'admin' => array(
                'users[list]',
                'users[create]',
                'users[read]',
                'users[update]',
                'users[delete]',
                
                
                'roles[list]',
                'roles[create]',
                'roles[read]',
                'roles[update]',
                'roles[delete]',
                
                
                'groups[list]',
                'groups[create]',
                'groups[read]',
                'groups[update]',
                'groups[delete]',
                
                
                'permissions[user]',
                'permissions[user[create]]',
                'permissions[user[update]]',
                'permissions[user[delete]]',
                'permissions[roles]',
                'permissions[roles[create]]',
                'permissions[roles[update]]',
                'permissions[roles[delete]]',
                'permissions[groups]',
                'permissions[groups[create]]',
                'permissions[groups[update]]',
                'permissions[groups[delete]]',
            ),
        ),
    );

    public function up()
    {
        \DB::delete()
            ->table(\Model\Auth_Permission::table())
            ->where('area', '=', 'auth')
            ->where('permission', '=', 'admin')
            ->execute();
        
        foreach ( static::$permissions as $area => $permissions )
        {
            foreach ( $permissions as $permission => $actions )
            {
                $perms = \Model\Auth_Permission::forge(array(
                    'area'          => $area,
                    'permission'    => $permission,
                    'description'   => 'auth::permissions.' . $area . '.' . $permission . '._description',
                    'actions'       => $actions,
                ));
                
                $perms->save();
            }
        }
    }
    
    public function down()
    {
        foreach ( static::$permissions as $area => $permissions )
        {
            foreach ( $permissions as $permission => $actions )
            {
                $perm = \Model\Auth_Permission::query()
                    ->where('area', '=', $area)
                    ->where('permission', '=', $permission)
                    ->get_one();
                
                $perm && $perm->delete();
            }
        }
        
        foreach ( static::$old as $area => $permissions )
        {
            foreach ( $permissions as $permission => $actions )
            {
                $perms = \Model\Auth_Permission::forge(array(
                    'area'          => $area,
                    'permission'    => $permission,
                    'description'   => 'auth::permissions.' . $permission . '._description',
                    'actions'       => $actions,
                ));
                
                $perms->save();
            }
        }
    }
    
}

/* End of file 003_seed_auth_permissions.php */
/* Location: ./fuel/gasoline/modules/auth/seeds/003_seed_auth_permissions.php */
