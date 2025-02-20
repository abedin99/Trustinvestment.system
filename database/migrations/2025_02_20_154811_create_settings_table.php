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
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 255)->unique();
            $table->string('option', 511);
            $table->longText('value')->nullable();
            $table->longText('validation');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained()->on('admins')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained()->on('admins')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
