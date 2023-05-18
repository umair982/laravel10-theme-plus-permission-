<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin=Admin::create([
            'name'=>'admin',
            'email'=>'admin@domain.com',
            'active'=>1,
            'password'=>Hash::make('12345678'),
        ]);
        $role = Role::create(['guard_name' => 'admin', 'name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $admin->assignRole([$role->id]);
        $manager=Admin::create([
            'name'=>'manager',
            'email'=>'manager@domain.com',
            'active'=>1,
            'password'=>Hash::make('12345678'),
        ]);
        $role = Role::create(['guard_name' => 'admin', 'name' => 'Manager']);
        $role->givePermissionTo(['setting-list', 'setting-create','setting-edit','setting-delete']);
        $admin->assignRole([$role->id]);
    }
}
