<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }

    public function query(Request $request)
    {
        $request->validate([
            'request'  => 'required|string|max:5000',
            'limit'    => 'integer|in:10,50,100,250,500,1000,5000,10000',
            'lang'     => 'string|in:en,ru,de,fr,es,it,pt,zh,ar',
            'bot_name' => 'nullable|string|max:100',
        ]);

        // Token priority: per-user token → global .env token
        $user     = auth()->user();
        $apiToken = $user->api_token ?? config('leakosint.api_token');

        if (empty($apiToken)) {
            return response()->json([
                'error' => 'API token belum dikonfigurasi. Hubungi administrator.',
            ], 500);
        }

        $payload = [
            'token'   => $apiToken,
            'request' => $request->input('request'),
            'limit'   => (int) $request->input('limit', 100),
            'lang'    => $request->input('lang', 'en'),
        ];

        if ($request->filled('bot_name')) {
            $payload['bot_name'] = $request->input('bot_name');
        }

        try {
            // LeakOSINT API hanya menerima request dari Python client.
            // Header di bawah meniru python-requests library agar tidak ditolak.
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent'      => 'python-requests/2.31.0',
                    'Accept'          => '*/*',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection'      => 'keep-alive',
                    'Content-Type'    => 'application/json',
                ])
                ->post(config('leakosint.api_url'), $payload);

            $data = $response->json();

            SearchLog::create([
                'user_id'     => $user->id,
                'query'       => $request->input('request'),
                'limit_count' => $payload['limit'],
                'lang'        => $payload['lang'],
                'num_results' => $data['NumOfResults'] ?? 0,
                'num_sources' => $data['NumOfDatabase'] ?? 0,
                'search_time' => $data['search time'] ?? null,
                'ip_address'  => $request->ip(),
            ]);

            return response()->json($data, $response->status());

        } catch (\Exception $e) {
            Log::error('LeakOSINT API Error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Gagal terhubung ke API LeakOSINT.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function history()
    {
        $logs = SearchLog::with('user')
            ->when(!auth()->user()->isAdmin(), function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(30);

        return view('search.history', compact('logs'));
    }
}
