<?php

namespace Database\Seeders;

use App\Models\Crm\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /*configs*/

        $parent = Permission::updateOrCreate(
            [
                'name' => 'crm_configs'
            ],
            [
                'display_name' => 'Sozlash ma\'lumotlari',
            ]
        );

        Permission::updateOrCreate(
            [
                'name' => 'crm_config_index',
            ],
            [
                'display_name' => 'Sozlash ma\'lumotlari ro\'yxati',
                'parent_id' => $parent->id
            ]
        );

        Permission::updateOrCreate(
            [
                'name' => 'crm_config_update',
            ],
            [
                'display_name' => 'Sozlash ma\'lumotini o\'zgartirish',
                'parent_id' => $parent->id
            ]
        );

        Permission::updateOrCreate(
            [
                'name' => 'crm_config_show',
            ],
            [
                'display_name' => 'Sozlash ma\'lumotlar',
                'parent_id' => $parent->id
            ]
        );


        /*i18n_sources*/

        $parent = Permission::updateOrCreate(
            [
                'name' => 'crm_cbr_sources',
            ],
            [
                'display_name' => 'Sbr'
            ]
        );

        Permission::updateOrCreate(
            [
                'name' => 'crm_cbr_index',
            ],
            [
                'display_name' => 'Cbr ro\'yxati',
                'parent_id' => $parent->id
            ]
        );

        Permission::updateOrCreate(
            [
                'name' => 'crm_cbr_store',
            ],
            [
                'display_name' => 'Cbr saqlash',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_cbr_update',
            ],
            [
                'display_name' => 'Cbr tahrirlash',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_cbr_show',
            ],
            [
                'display_name' => 'Cbr ko\'rish',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_cbr_destroy',
            ],
            [
                'display_name' => 'Cbr o\'chirish',
                'parent_id' => $parent->id
            ]);

        /*permission_groups*/

        $parent = Permission::updateOrCreate(
            [
                'name' => 'crm_permission_groups',
            ],
            [
                'display_name' => 'Huquqlar',
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_permission_group_index',
            ],
            [
                'display_name' => 'Huquq guruhi',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_permission_group_store',
            ],
            [
                'display_name' => 'Huquq guruhini qo\'shish',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_permission_group_update',
            ],
            [
                'display_name' => 'Huquq guruhini tahrirlash',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_permission_group_show',
            ],
            [
                'display_name' => 'Huquq guruhini ko\'rish',
                'parent_id' => $parent->id
            ]);

        Permission::updateOrCreate(
            [
                'name' => 'crm_permission_group_destroy',
            ],
            [
                'display_name' => 'Huquq guruhini o\'chirish',
                'parent_id' => $parent->id
            ]);
    }
}
