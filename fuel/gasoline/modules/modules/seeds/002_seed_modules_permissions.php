<?php namespace Seeds;

class Seed_modules_permissions {
    
    protected static $permissions = array(
        'modules' => array(
            'admin' => array(
                'list',
                'upload',
                'read',
                'enable',
                'disable',
                'delete',
                'download',
            ),
        ),
    );

    public function up()
    {
        foreach ( static::$permissions as $area => $permissions )
        {
            foreach ( $permissions as $permission => $actions )
            {
                $perms = \Model\Auth_Permission::forge(array(
                    'area'          => $area,
                    'permission'    => $permission,
                    'description'   => 'modules::permissions.' . $permission . '._description',
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
    }
    
}

/* End of file 002_seed_modules_permissions.php */
/* Location: ./fuel/gasoline/modules/modules/seeds/002_seed_modules_permissions.php */
