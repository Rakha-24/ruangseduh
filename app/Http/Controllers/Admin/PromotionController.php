<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromotionRequest;
use App\Http\Requests\Admin\UpdatePromotionRequest;
use App\Models\Promotion;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function __construct(private readonly CloudinaryService $cloudinary)
    {
    }

    /**
     * Daftar promo (aktif & non-aktif).
     * Gambar promo disimpan di Cloudinary.
     */
    public function index(): View
    {
        $promotions = Promotion::query()
            ->latest()
            ->paginate(10);

        return view('admin.promo.index', compact('promotions'));
    }

    /**
     * Form tambah promo.
     */
    public function create(): View
    {
        return view('admin.promo.create');
    }

    /**
     * Simpan promo baru beserta gambar (disimpan di Cloudinary).
     */
    public function store(StorePromotionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            try {
                $data['image'] = $this->cloudinary->upload($request->file('image')->getRealPath(), 'promos');
            } catch (\Throwable $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Upload gambar gagal. Periksa koneksi dan konfigurasi Cloudinary.');
            }
        }

        $data['is_active'] = $request->boolean('is_active');

        Promotion::create($data);

        return redirect()
            ->route('admin.promo.index')
            ->with('status', 'Promo berhasil ditambahkan.');
    }

    /**
     * Form edit promo.
     */
    public function edit(Promotion $promo): View
    {
        return view('admin.promo.edit', compact('promo'));
    }

    /**
     * Perbarui promo, hapus gambar lama bila diganti.
     */
    public function update(UpdatePromotionRequest $request, Promotion $promo): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            try {
                $promo->deleteStoredImage();
                $data['image'] = $this->cloudinary->upload($request->file('image')->getRealPath(), 'promos');
            } catch (\Throwable $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Upload gambar gagal. Periksa koneksi dan konfigurasi Cloudinary.');
            }
        }

        $data['is_active'] = $request->boolean('is_active');

        $promo->update($data);

        return redirect()
            ->route('admin.promo.index')
            ->with('status', 'Promo berhasil diperbarui.');
    }

    /**
     * Hapus promo beserta gambarnya.
     */
    public function destroy(Promotion $promo): RedirectResponse
    {
        $promo->deleteStoredImage();

        $promo->delete();

        return redirect()
            ->route('admin.promo.index')
            ->with('status', 'Promo berhasil dihapus.');
    }
}