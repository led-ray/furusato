<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // デバッグ用にログにキーワードを出力
        Log::info('Keyword: ' . $keyword);

        // キーワードが空でないことを確認
        if (empty($keyword)) {
            return response()->json(['error' => 'Keyword is required'], 400);
        }

        // 環境変数からAPIキーを取得
        $appId = env('RAKUTEN_APP_ID');
        $affiliateId = env('RAKUTEN_AFFILIATE_ID');

        // APIのエンドポイントURL
        $url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601';

        // Guzzleクライアントを使用してリクエストを送信
        try {
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

            // レスポンスを取得
            $data = json_decode($response->getBody()->getContents(), true);

            // デバッグ用にログにレスポンスを出力
            Log::info('Rakuten API Response: ', $data);

            // エラーチェック
            if (isset($data['error'])) {
                Log::error('Rakuten API Error: ' . $data['error_description']);
                return response()->json(['error' => 'Rakuten API Error: ' . $data['error_description']], 500);
            }

            // レスポンスを返す
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching Rakuten API: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching Rakuten API'], 500);
        }
    }
}
