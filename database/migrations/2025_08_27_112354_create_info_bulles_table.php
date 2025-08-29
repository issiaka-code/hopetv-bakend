<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('info_bulles', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('texte');
            $table->boolean('is_active')->default(true);
            $table->foreignId('insert_by')->constrained('users');
            $table->foreignId('update_by')->constrained('users');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('info_bulles');
    }
};