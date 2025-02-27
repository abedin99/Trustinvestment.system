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
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('method_code')->unsigned();
            $table->string('method_currency', 191);
            $table->decimal('amount', 18, 8);
            $table->decimal('rate', 18, 8);
            $table->decimal('charge', 18, 8);
            $table->decimal('final_amo', 11)->nullable();
            $table->string('btc_amo', 191)->nullable();
            $table->string('btc_wallet', 191)->nullable();
            $table->string('trx', 191)->nullable();
            $table->integer('try')->default(0);
            $table->text('detail')->nullable();
            $table->string('verify_image', 191)->nullable();
            $table->tinyInteger('status')->default(0)->comment('1=Approved; 2=Pending; 3=Rejected;');
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
        Schema::dropIfExists('deposits');
    }
};
