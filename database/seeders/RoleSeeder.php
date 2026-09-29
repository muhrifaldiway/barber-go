<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Booking
            'booking.create', 'booking.view', 'booking.update', 'booking.cancel',
            'booking.view-all',
            // Service
            'service.create', 'service.view', 'service.update', 'service.delete',
            // Barber
            'barber.manage-schedule', 'barber.manage-portfolio',
            'barber.accept-booking', 'barber.update-status',
            // Review
            'review.create', 'review.reply',
            // Admin
            'admin.dashboard', 'admin.manage-users', 'admin.manage-services',
            'admin.manage-bookings', 'admin.manage-vouchers', 'admin.reports',
            'admin.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $barber = Role::create(['name' => 'barber']);
        $barber->givePermissionTo([
            'booking.view', 'booking.update',
            'barber.manage-schedule', 'barber.manage-portfolio',
            'barber.accept-booking', 'barber.update-status',
            'review.reply',
        ]);

        $customer = Role::create(['name' => 'customer']);
        $customer->givePermissionTo([
            'booking.create', 'booking.view', 'booking.cancel',
            'review.create',
        ]);
    }
}