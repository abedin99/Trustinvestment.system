<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->char('country_code', 2);
            $table->string('fips_code', 255)->nullable();
            $table->string('iso2', 255)->nullable();
            $table->string('type', 255)->nullable();

            $table->float('latitude', 10, 8)->nullable();
            $table->float('longitude', 11, 8)->nullable();

            $table->timestamps();
            $table->tinyInteger('flag');
            $table->string('wikiDataId', 255)->nullable()->comment('Rapid API GeoDB Cities');
            $table->softDeletes();
        });

        # Import sql files
        DB::unprepared( file_get_contents( database_path("/sql/locations/states.sql") ) );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('states');
    }
};
