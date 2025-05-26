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
    Schema::table('comentarios', function (Blueprint $table) {
        $table->unsignedBigInteger('conversacion_id')->after('id'); // o donde corresponda
        $table->foreign('conversacion_id')->references('id')->on('conversaciones')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            //
        });
    }
};
