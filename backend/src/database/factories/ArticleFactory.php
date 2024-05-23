<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        $title = $this->faker->sentence;

        return [
            'title' => $title,
            'description' => $this->faker->realText(30),
            'body' => $this->faker->paragraph,
            'slug' => Str::slug($title, '-')
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Article $article) {
            $tags = collect(['dolorem', 'exampleTag1', 'exampleTag2'])->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });
            
            // ArticleとTagの関連を設定
            $article->tags()->attach($tags->pluck('id'));
        });
    }
}
