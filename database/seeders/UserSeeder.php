<?php

namespace Database\Seeders;

use App\Models\Crm\Permission;
use App\Models\Crm\PermissionGroup;
use App\Models\Crm\PermissionGroupPermission;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permission_group = PermissionGroup::firstOrCreate([
            'name' => 'Super admin',
        ]);

        $permission_group_user = PermissionGroup::firstOrCreate([
            'name' => 'user',
        ]);

        $user = User::firstOrCreate(
            [
                'username' => 'superadmin@gmail.com',
            ],

            [
                'password' => bcrypt('admin2022_library'),
                'status' => 1,
                'permission_group_id' => $permission_group->id,
                'role' => 'admin'
            ]
        );

        foreach (Permission::get() as $item) {
            PermissionGroupPermission::updateOrCreate(
                [
                    'permission_group_id' => $permission_group->id,
                    'permission_id' => $item->id,
                ]);
        }
    }
}
