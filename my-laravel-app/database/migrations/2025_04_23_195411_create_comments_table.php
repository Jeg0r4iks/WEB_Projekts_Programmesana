<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');  // Текст комментария
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Связь с пользователем
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');  // Связь с публикацией
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}

