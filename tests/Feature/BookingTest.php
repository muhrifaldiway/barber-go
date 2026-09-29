<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BarberProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\BarberSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_customer_can_create_booking()
    {
        // Create customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        // Create barber
        $barberUser = User::factory()->create();
        $barberUser->assignRole('barber');
        $barber = BarberProfile::create([
            'user_id' => $barberUser->id,
            'status' => 'active',
            'is_available' => true,
        ]);

        // Create schedule
        BarberSchedule::create([
            'barber_id' => $barber->id,
            'day_of_week' => now()->addDay()->dayOfWeek,
            'start_time' => '08:00',
            'end_time' => '17:00',
            'is_available' => true,
        ]);

        // Create service
        $category = ServiceCategory::create(['name' => 'Test', 'slug' => 'test']);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Potong Rambut',
            'slug' => 'potong-rambut',
            'price' => 50000,
            'duration_minutes' => 30,
        ]);

        $response = $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'barber_id' => $barber->id,
            'services' => [$service->id],
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Jl. Test No. 1',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'barber_id' => $barber->id,
            'status' => 'pending',
        ]);
    }

    public function test_barber_can_confirm_booking()
    {
        // ... similar setup ...
        // Assert status changes from pending to confirmed
    }

    public function test_customer_can_cancel_pending_booking()
    {
        // ... test cancellation logic ...
    }
}