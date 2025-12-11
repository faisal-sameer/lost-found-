<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->string('map_url')->nullable()->after('address');
        });
    }

    public function down()
    {
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->dropColumn('map_url');
        });
    }
};
