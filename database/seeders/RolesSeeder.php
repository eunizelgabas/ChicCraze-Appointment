<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin     =   Role::create(['name' =>'Admin']);
        $role_standard    =   Role::create(['name' =>'Standard']);

        $manage_user = Permission::create(['name' => 'manage-user']);
        $manage_service = Permission::create(['name' => 'manage-service']);
        $view_appointment = Permission::create(['name' => 'view-appointment']);
        $add_appointment =  Permission::create(['name' => 'add-appointment']);
        $delete_appointment = Permission::create(['name' => 'delete-appointment']);
        $manage_appointment = Permission::create(['name' => 'manage-appointment']);


        $permission_admin =  [ $manage_user, $manage_service,  $manage_appointment, $view_appointment, $delete_appointment
                                 ];

        $permission_doctor =  [$add_appointment, $manage_appointment];

        $role_admin->syncPermissions($permission_admin);
        $role_standard->syncPermissions($permission_doctor);
        // $role_patient->givePermissionTo($add_appointment, $edit_appointment, $delete_appointment);

        User::find(1)->assignRole($role_admin);
        User::find(2)->assignRole($role_standard);

    }
}
