<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_register()
    {
        // テスト用のユーザーデータ
        $userData = [
            'user' => [
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password', // パスワード確認用
            ],
        ];

        // ユーザー登録エンドポイントへのPOSTリクエストを送信
        $response = $this->postJson('/api/users', $userData);

        // レスポンスの検証
        $response->assertStatus(201) 
                ->assertJson([
                    'user' => [
                        'username' => 'testuser',
                        'email' => 'test@example.com',
                    ]
                ]);

        // データベースにユーザーが保存されているか確認
        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }
}