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
    Schema::table('users', function (Blueprint $table) {
        // Remove existing columns first if they exist
        if (Schema::hasColumn('users', 'check_in_time')) {
            $table->dropColumn('check_in_time');
        }
        if (Schema::hasColumn('users', 'check_in_latitude')) {
            $table->dropColumn('check_in_latitude');
        }
        if (Schema::hasColumn('users', 'check_in_longitude')) {
            $table->dropColumn('check_in_longitude');
        }
        if (Schema::hasColumn('users', 'check_out_time')) {
            $table->dropColumn('check_out_time');
        }
        if (Schema::hasColumn('users', 'check_out_latitude')) {
            $table->dropColumn('check_out_latitude');
        }
        if (Schema::hasColumn('users', 'check_out_longitude')) {
            $table->dropColumn('check_out_longitude');
        }

        // Add columns back with the same names
        $table->timestamp('check_in_time')->nullable();
        $table->decimal('check_in_latitude', 10, 8)->nullable();
        $table->decimal('check_in_longitude', 11, 8)->nullable();
        $table->timestamp('check_out_time')->nullable();
        $table->decimal('check_out_latitude', 10, 8)->nullable();
        $table->decimal('check_out_longitude', 11, 8)->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'check_in_time', 
            'check_in_latitude', 
            'check_in_longitude',
            'check_out_time',
            'check_out_latitude', 
            'check_out_longitude'
        ]);
    });
}
};
