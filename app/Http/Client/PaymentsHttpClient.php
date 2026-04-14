<?php

namespace App\Http\Client;

use Exception;
use Illuminate\Support\Facades\Http;

class PaymentsHttpClient
{
    protected string $token;

    protected string $baseUrl;

    protected string $paymentReceipt;

    public function __construct()
    {
        $this->token = config('app.dalle_payments_access_token');
        $this->baseUrl = config('app.dalle_payments_url');
        $this->paymentReceipt = config('app.payment_receipt');
    }

    public function request(string $method, string $uri, array $data = [])
    {
        $base = rtrim($this->baseUrl, '/');
        $receipt = trim($this->paymentReceipt, '/');
        $endpoint = ltrim($uri, '/');

        $url = "{$base}/{$receipt}/{$endpoint}";

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'access_token' => $this->token,
        ])->{$method}($url, $data);

        $json = $response->json();

        if (isset($json['errors'])) {
            throw new Exception(json_encode($json['errors'], JSON_UNESCAPED_UNICODE));
        }

        if ($response->failed()) {
            throw new Exception($response->body());
        }

        return $json;
    }

    public function post(string $uri, array $data = [])
    {
        return $this->request('post', $uri, $data);
    }

    public function get(string $uri)
    {
        return $this->request('get', $uri);
    }

    public function put(string $uri)
    {
        return $this->request('put', $uri);
    }

    public function delete(string $uri)
    {
        return $this->request('delete', $uri);
    }
}
