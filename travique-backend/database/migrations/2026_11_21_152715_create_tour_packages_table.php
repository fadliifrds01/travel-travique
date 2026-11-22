<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedInteger('quota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
