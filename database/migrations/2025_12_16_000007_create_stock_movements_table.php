<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['inbound', 'outbound', 'transfer', 'adjustment']);
            $table->foreignId('variant_id')->constrained('variants')->onDelete('cascade');
            $table->foreignId('rak_id')->constrained('raks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Who did it
            $table->integer('quantity'); // Can be + or - depending on implementation, or just Absolute value with Type determining.
            // Let's assume quantity is always positive, and type determines In/Out logic usually, but sometimes +/- is stored.
            // We will store Absolute Quantity here for simplicity, logic handles +/-.
            // OR store Signed Quantity. Let's store Absolute here to avoid confusion, Type tells direction.
            $table->string('reference_code')->nullable(); // PO Number, Invoice, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
