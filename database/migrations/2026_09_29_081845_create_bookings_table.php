// database/migrations/xxxx_create_bookings_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('barber_id')->constrained('barber_profiles')->onDelete('cascade');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->time('estimated_end_time')->nullable();
            $table->text('address');
            $table->string('address_detail')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('notes')->nullable();
            $table->enum('status', [
                'pending',        // Menunggu konfirmasi barber
                'confirmed',      // Barber sudah konfirmasi
                'on_the_way',     // Barber dalam perjalanan
                'arrived',        // Barber sudah sampai
                'in_progress',    // Sedang dikerjakan
                'completed',      // Selesai
                'cancelled',      // Dibatalkan
            ])->default('pending');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('voucher_code')->nullable();
            $table->decimal('travel_fee', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->enum('payment_method', ['cod', 'bank_transfer', 'ewallet'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->string('cancelled_by')->nullable(); // customer / barber / admin
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['barber_id', 'status']);
            $table->index(['booking_date', 'booking_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};