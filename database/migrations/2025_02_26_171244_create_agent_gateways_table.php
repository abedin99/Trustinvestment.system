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
        Schema::create('agent_gateways', function (Blueprint $table) {
            $table->integer('id', true);
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->integer('gateway_code')->unsigned();
            $table->string('account_number');
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            
            $table->foreign('gateway_code')
                ->references('code')->on('gateways')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_gateways');
    }
};
