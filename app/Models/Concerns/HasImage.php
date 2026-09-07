<?php

namespace App\Models\Concerns;

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HasImage
{
    /**
     * Absolute URL gambar — mendukung path storage lokal maupun URL absolut.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * Hapus gambar dari Cloudinary bila disimpan sebagai URL resolusi tersebut.
     * Aman dipanggil walau gambar kosong atau masih berupa path storage lokal.
     */
    public function deleteStoredImage(?string $url = null): void
    {
        $url ??= $this->image;

        if (empty($url) || ! str_contains($url, 'res.cloudinary.com')) {
            return;
        }

        try {
            resolve(CloudinaryService::class)->destroy($url);
        } catch (\Throwable $e) {
            Log::warning('Gagal menghapus gambar Cloudinary: '.$e->getMessage());
        }
    }
}