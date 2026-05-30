<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Get Google OAuth2 Access Token using the Service Account JSON
     */
    private static function getAccessToken(): ?string
    {
        $path = storage_path('app/firebase-service-account.json');

        if (!file_exists($path)) {
            Log::warning("Firebase Service Account JSON file not found at: {$path}. FCM will be bypassed.");
            return null;
        }

        try {
            $config = json_decode(file_get_contents($path), true);
            $now = time();

            // Construct JWT payload
            $payload = [
                'iss'   => $config['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ];

            // JWT header
            $header = [
                'alg' => 'RS256',
                'typ' => 'JWT',
            ];

            // Base64Url encode
            $base64UrlHeader  = self::base64UrlEncode(json_encode($header));
            $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

            // Sign
            $signatureInput = $base64UrlHeader . '.' . $base64UrlPayload;
            $privateKey     = $config['private_key'];

            if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                throw new \Exception("Failed to sign JWT with openssl_sign.");
            }

            $base64UrlSignature = self::base64UrlEncode($signature);
            $jwt = $signatureInput . '.' . $base64UrlSignature;

            // Make POST request to Google OAuth2 token endpoint
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error("Google OAuth2 token request failed: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Error generating Google Access Token: " . $e->getMessage());
            return null;
        }
    }

    private static function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Send Push Notification using FCM HTTP v1 API
     */
    public static function sendPushNotification(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            return false;
        }

        $path = storage_path('app/firebase-service-account.json');
        if (!file_exists($path)) {
            return false;
        }

        $config = json_decode(file_get_contents($path), true);
        $projectId = $config['project_id'] ?? null;

        if (!$projectId) {
            Log::error("Firebase project_id not found in service account JSON.");
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Convert all elements in data array to string (FCM requirement)
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string) $key] = (string) $value;
        }

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'sound'        => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info("FCM push notification sent successfully to token: " . substr($fcmToken, 0, 15) . "...");
                return true;
            }

            Log::error("FCM v1 API send failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("FCM push notification exception: " . $e->getMessage());
            return false;
        }
    }
}
