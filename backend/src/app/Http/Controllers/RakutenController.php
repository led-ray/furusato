<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RakutenController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // 環境変数からAPIキーを取得
        $appId = env('RAKUTEN_APP_ID');
        $affiliateId = env('RAKUTEN_AFFILIATE_ID');

        $url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601';

        $client = new Client();
        $response = $client->get($url, [
            'query' => [
                'format' => 'json',
                'applicationId' => $appId,
                'affiliateId' => $affiliateId,
                'keyword' => $keyword,
                'hits' => 20,
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return response()->json($data);
    }
}
