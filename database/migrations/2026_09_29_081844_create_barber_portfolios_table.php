// database/migrations/xxxx_create_barber_portfolios_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barber_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained('barber_profiles')->onDelete('cascade');
            $table->string('image');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_portfolios');
    }
};