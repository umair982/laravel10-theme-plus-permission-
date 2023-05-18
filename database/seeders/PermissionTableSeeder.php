<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'admin-list',
            'admin-create',
            'admin-edit',
            'admin-delete',
            'setting-list',
            'setting-create',
            'setting-edit',
            'setting-delete'
         ];

         foreach ($permissions as $permission) {
              $permission = Permission::create(['guard_name' => 'admin', 'name' => $permission]);
         }
    }
}
