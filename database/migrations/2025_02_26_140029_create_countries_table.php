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
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255)->unique();
            $table->string('iso3', 255)->unique();
            $table->string('numeric_code', 255)->nullable();
            $table->string('iso2', 255)->nullable();
            $table->string('phonecode', 255)->nullable();
            $table->string('capital', 255)->nullable();
            $table->string('currency', 255)->nullable();

            $table->string('currency_name', 255)->nullable();
            $table->string('currency_symbol', 255)->nullable();
            $table->string('tld', 255)->nullable();
            $table->string('native', 255)->nullable();
            $table->string('region', 255)->nullable();
            $table->string('subregion', 255)->nullable();

            $table->text('timezones')->nullable();
            $table->text('translations')->nullable();

            $table->float('latitude', 10, 8)->nullable();
            $table->float('longitude', 11, 8)->nullable();

            $table->string('emoji', 255)->nullable();
            $table->string('emojiU', 255)->nullable();

            $table->timestamps();
            $table->tinyInteger('flag');
            $table->string('wikiDataId', 255)->nullable()->comment('Rapid API GeoDB Cities');
            $table->softDeletes();
        });

        # Import sql files
        DB::unprepared( file_get_contents( database_path("/sql/locations/countries.sql") ) );

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
