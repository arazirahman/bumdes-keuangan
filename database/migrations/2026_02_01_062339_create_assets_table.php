<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->date('acquired_date')->nullable(); // tanggal perolehan
            $table->unsignedBigInteger('unit_cost')->default(0); // harga satuan
            $table->unsignedInteger('qty')->default(1); // jumlah
            $table->string('condition')->nullable(); // Baik/Rusak ringan/dll
            $table->string('location')->nullable(); // lokasi aset
            $table->text('note')->nullable();

            $table->foreignId('unit_usaha_id')->nullable()->constrained('unit_usahas')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['unit_usaha_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
