<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            
            foreach (config('permission.permissions') as $role => $permissions) {
                $role = Role::firstOrCreate(['name' => $role, 'guard_name' => 'sanctum']);

                foreach ($permissions as $permission) {
                    $permission = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
                    $role->givePermissionTo($permission);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
