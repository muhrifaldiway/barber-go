// database/migrations/xxxx_create_barber_profiles_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barber_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('speciality')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_completed_bookings')->default(0);
            $table->boolean('is_available')->default(true);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('current_address')->nullable();
            $table->integer('service_radius_km')->default(10);
            $table->decimal('commission_rate', 5, 2)->default(80.00); // 80% untuk barber
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_profiles');
    }
};