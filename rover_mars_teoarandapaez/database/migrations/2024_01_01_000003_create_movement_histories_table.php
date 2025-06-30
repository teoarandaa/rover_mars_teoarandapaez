<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movement_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('x');
            $table->unsignedSmallInteger('y');
            $table->string('direction', 1);
            $table->string('commands');
            $table->json('obstacles')->nullable();
            $table->string('result_status');
            $table->unsignedSmallInteger('result_x');
            $table->unsignedSmallInteger('result_y');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movement_histories');
    }
}; 