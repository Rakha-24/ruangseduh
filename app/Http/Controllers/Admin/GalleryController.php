<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Models\Gallery;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private readonly CloudinaryService $cloudinary)
    {
    }

    /**
     * Galeri foto (upload & hapus saja — tanpa edit).
     * Foto disimpan di Cloudinary.
     */
    public function index(): View
    {
        $photos = Gallery::query()
            ->latest()
            ->paginate(12);

        return view('admin.galeri.index', compact('photos'));
    }

    /**
     * Simpan foto baru beserta keterangan opsional.
     */
    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        try {
            $image = $this->cloudinary->upload($request->file('image')->getRealPath(), 'galeri');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Upload foto gagal. Periksa koneksi dan konfigurasi Cloudinary.');
        }

        Gallery::create([
            'image' => $image,
            'caption' => $request->input('caption'),
        ]);

        return back()
            ->with('status', 'Foto berhasil ditambahkan.');
    }

    /**
     * Hapus foto beserta file-nya dari Cloudinary.
     */
    public function destroy(Gallery $galeri): RedirectResponse
    {
        $galeri->deleteStoredImage();

        $galeri->delete();

        return back()
            ->with('status', 'Foto berhasil dihapus.');
    }
}