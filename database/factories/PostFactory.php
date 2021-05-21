<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->realText(rand(10,40));//title будет случайным текстом от 10 до 40 символов
        $short_title = mb_strlen($title)>30 ? mb_substr($title,0,30).'...':$title;//укороченный title равняется обычному title, если title <= 30 символам
        $created = $this->faker->dateTimeBetween('-30 days','-1 days');//случайная дата минимум на день меньше текущей даты, максимум на 30 дней больше
        return [
            'title' => $title,
            'short_title' =>$short_title,
            'author_id' => rand(1,4),
            'description' => $this->faker->realText(rand(100,500)),//случайный текст от 100 до 500 символов
            'created_at' => $created,
            'updated_at' => $created,
            //
        ];
    }
}
