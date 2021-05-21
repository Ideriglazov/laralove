<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()//метод для создания таблицы
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->unsignedbigInteger('author_id');
            $table->string('title');
            $table->string('short_title');
            $table->string('img')->nullable();//nullable значит, что картинку можно и не загружать
            $table->text('description');//text выбрано вместо string потому что описание может быть длинным
            $table->timestamps();//здесь зашифрованы created at(создание поста) и updated at(обновление поста)
            $table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()//метод для удаления таблицы
    {
        Schema::dropIfExists('posts');
    }
}
