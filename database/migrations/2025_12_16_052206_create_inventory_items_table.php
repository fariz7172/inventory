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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('variants')->onDelete('cascade');
            $table->foreignId('rak_id')->nullable()->constrained('raks')->onDelete('set null');
            $table->string('serial_number')->unique();
            $table->enum('status', ['available', 'sold', 'missing', 'reserved'])->default('available');
            $table->foreignId('inbound_id')->nullable()->constrained('stock_movements')->onDelete('set null'); // Origin movement
            $table->foreignId('outbound_id')->nullable()->constrained('stock_movements')->onDelete('set null'); // Exit movement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
