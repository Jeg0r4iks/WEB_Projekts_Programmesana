<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();  // Столбец id (auto increment)
            $table->string('title');  // Столбец title
            $table->text('content');  // Столбец content
            $table->timestamps();
            // Столбцы created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');  // Удаление таблицы при откате миграции
    }
}
