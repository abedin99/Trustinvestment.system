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
        Schema::create('gateway_currencies', function (Blueprint $table) {$table->bigIncrements('id');
            $table->string('name', 191)->nullable();
            $table->string('currency', 191);
            $table->string('symbol', 191);
            $table->integer('method_code')->unsigned();
            $table->decimal('min_amount', 18, 8);
            $table->decimal('max_amount', 18, 8);
            $table->decimal('percent_charge', 8, 4)->default(0);
            $table->decimal('fixed_charge', 18, 8)->default(0);
            $table->decimal('rate', 18, 8);
            $table->json('wallet_rates')->nullable();
            $table->string('image', 191)->nullable();
            $table->json('parameter')->nullable();
            $table->integer('serial_id')->nullable();
            $table->timestamps();
            
            $table->foreign('method_code')
                ->references('code')->on('gateways')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_currencies');
    }
};
