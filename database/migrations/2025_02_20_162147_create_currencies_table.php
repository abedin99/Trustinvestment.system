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
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 511);
            $table->string('slug', 255)->unique();
            $table->string('symbol', 255);
            $table->string('currency', 255);
            $table->foreignId('created_by')->constrained()->on('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained()->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
