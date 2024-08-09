<?php

namespace Tests\Feature;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\RakutenController;

class RakutenControllerTest extends TestCase
{
    public function testSearchWithRealApi()
    {
        $request = Request::create('/search', 'GET', ['keyword' => 'book']);

        $controller = new RakutenController(new Client());
        $response = $controller->search($request);

        // ステータスコードが200 OKであることを確認
        $this->assertEquals(200, $response->getStatusCode());

        // レスポンスがJSONであることを確認
        $this->assertJson($response->getContent());

        // レスポンスが期待した構造を持っているか確認
        $data = json_decode($response->getContent(), true);

        // Itemsキーが存在し、かつ空でないことを確認
        $this->assertArrayHasKey('Items', $data);
        $this->assertNotEmpty($data['Items']);
    }
}
