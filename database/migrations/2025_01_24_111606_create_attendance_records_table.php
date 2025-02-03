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
    Schema::create('attendance_records', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->date('date');
        $table->time('check_in_time')->nullable();
        $table->time('check_out_time')->nullable();
        $table->decimal('check_in_latitude', 10, 8)->nullable();
        $table->decimal('check_in_longitude', 11, 8)->nullable();
        $table->decimal('check_out_latitude', 10, 8)->nullable();
        $table->decimal('check_out_longitude', 11, 8)->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
