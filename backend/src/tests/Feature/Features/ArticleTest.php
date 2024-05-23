<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_an_article()
    {

        $attributes = [
            'title' => 'Test Article',
            'description' => 'Article description',
            'body' => 'Article body',
        ];

        $this->post('/articles', $attributes);

        $this->assertDatabaseHas('articles', $attributes);
    }
}