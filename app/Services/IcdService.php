<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IcdService
{
    protected string $tokenUrl;
    protected string $apiUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->tokenUrl     = config('services.icd.token_url');
        $this->apiUrl       = config('services.icd.api_url');
        $this->clientId     = config('services.icd.client_id');
        $this->clientSecret = config('services.icd.client_secret');
    }

    // Ambil token otomatis, disimpan cache 1 jam
    protected function getToken(): string
    {
        return Cache::remember('icd_access_token', 3500, function () {
            $response = Http::asForm()->post($this->tokenUrl, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => 'icdapi_access',
            ]);

            if ($response->failed()) {
                throw new \Exception('Gagal mendapatkan token ICD: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    protected function headers(): array
    {
        return [
            'Authorization'  => 'Bearer ' . $this->getToken(),
            'Accept'         => 'application/json',
            'Accept-Language'=> 'en',
            'API-Version'    => 'v2',
        ];
    }

    // Cari penyakit berdasarkan kata kunci
    public function search(string $query): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->apiUrl}/icd/release/11/2024-01/mms/search", [
                'q' => $query,
            ]);

        return $response->json();
    }

    // Detail penyakit berdasarkan kode ICD-10
    public function getByCode(string $code): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->apiUrl}/icd/release/10/2016/{$code}");

        return $response->json();
    }
}
