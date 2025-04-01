<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
        });
    }

    public function down() {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'video_path']);
        });
    }
};

