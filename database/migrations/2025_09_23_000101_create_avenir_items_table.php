<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('avenir_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_avenir')->constrained('avenirs');
            $table->foreignId('id_video')->constrained('videos');
            $table->time('duree_video')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('position')->default(0);
            $table->foreignId('insert_by')->constrained('users');
            $table->foreignId('update_by')->constrained('users');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avenir_items');
    }
};



