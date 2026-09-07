<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Klien minimal untuk Cloudinary Upload API v2.
 *
 * Dipakai karena SDK resmi (cloudinary-labs/cloudinary-laravel) belum
 * mendukung guzzle 8 yang dikunci Laravel 13. Upload API v2 menerima
 * HTTP Basic Auth sehingga tidak perlu membuat signature manual.
 */
class CloudinaryService
{
    protected Client $client;

    protected string $cloudName;

    protected string $apiKey;

    protected string $apiSecret;

    public function __construct()
    {
        $url = config('services.cloudinary.url');

        if (empty($url)) {
            throw new RuntimeException('CLOUDINARY_URL belum dikonfigurasi.');
        }

        $parsed = parse_url($url);

        $this->cloudName = $parsed['host'] ?? '';
        $this->apiKey = $parsed['user'] ?? '';
        $this->apiSecret = $parsed['pass'] ?? '';

        $this->client = new Client([
            'base_uri' => 'https://api.cloudinary.com',
            'http_errors' => true,
            'timeout' => 30,
        ]);
    }

    /**
     * Unggah gambar dan kembalikan URL aman (https) hasil upload.
     */
    public function upload(string $filePath, string $folder): string
    {
        $response = $this->client->post('/v1_1/'.$this->cloudName.'/image/upload', [
            'auth' => [$this->apiKey, $this->apiSecret],
            'multipart' => [
                ['name' => 'file', 'contents' => fopen($filePath, 'r')],
                ['name' => 'folder', 'contents' => $folder],
                ['name' => 'resource_type', 'contents' => 'image'],
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true);

        $url = $payload['secure_url'] ?? $payload['url'] ?? null;

        if (! $url) {
            throw new RuntimeException('Cloudinary tidak mengembalikan URL gambar.');
        }

        return $url;
    }

    /**
     * Hapus aset di Cloudinary berdasarkan public id hasil ekstraksi URL.
     */
    public function destroy(string $url): bool
    {
        $publicId = $this->publicIdFromUrl($url);

        if (! $publicId) {
            return false;
        }

        try {
            $response = $this->client->post('/v1_1/'.$this->cloudName.'/image/destroy', [
                'auth' => [$this->apiKey, $this->apiSecret],
                'form_params' => [
                    'public_id' => $publicId,
                    'type' => 'upload',
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
        } catch (\Throwable $e) {
            Log::warning('Gagal menghapus aset Cloudinary: '.$e->getMessage());

            return false;
        }

        return ($result['result'] ?? null) === 'ok';
    }

    /**
     * Ambil public id dari URL Cloudinary, mis.:
     * https://res.cloudinary.com/cloud/image/upload/v169.../menus/abc.png
     * -> menus/abc
     */
    protected function publicIdFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if (! str_contains($path, '/image/upload/')) {
            return null;
        }

        $publicId = preg_replace('#^.*?/image/upload/(?:v\d+/)?#', '', $path);

        return preg_replace('#\.(jpe?g|png|webp|gif|avif|bmp|svg)$#i', '', $publicId) ?: null;
    }
}