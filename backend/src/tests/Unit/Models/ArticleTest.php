<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function have_comments()
    {
        $article = Article::factory()->create();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $article->comments);
    }
}
