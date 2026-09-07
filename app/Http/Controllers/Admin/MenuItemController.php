<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function __construct(private readonly CloudinaryService $cloudinary)
    {
    }

    /**
     * Daftar item menu dengan filter kategori & pencarian.
     */
    public function index(Request $request): View
    {
        $menuItems = MenuItem::query()
            ->with('category')
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    /**
     * Form tambah item menu.
     */
    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.menu.create', compact('categories'));
    }

    /**
     * Simpan item menu baru beserta gambar (disimpan di Cloudinary).
     */
    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            try {
                $data['image'] = $this->cloudinary->upload($request->file('image')->getRealPath(), 'menus');
            } catch (\Throwable $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Upload gambar gagal. Periksa koneksi dan konfigurasi Cloudinary.');
            }
        }

        $data['is_available'] = $request->boolean('is_available');

        MenuItem::create($data);

        return redirect()
            ->route('admin.menu.index')
            ->with('status', 'Menu berhasil ditambahkan.');
    }

    /**
     * Form edit item menu.
     */
    public function edit(MenuItem $menu): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    /**
     * Perbarui item menu, hapus gambar lama bila diganti.
     */
    public function update(UpdateMenuItemRequest $request, MenuItem $menu): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            try {
                $menu->deleteStoredImage();
                $data['image'] = $this->cloudinary->upload($request->file('image')->getRealPath(), 'menus');
            } catch (\Throwable $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Upload gambar gagal. Periksa koneksi dan konfigurasi Cloudinary.');
            }
        }

        $data['is_available'] = $request->boolean('is_available');

        $menu->update($data);

        return redirect()
            ->route('admin.menu.index')
            ->with('status', 'Menu berhasil diperbarui.');
    }

    /**
     * Hapus item menu beserta gambarnya.
     */
    public function destroy(MenuItem $menu): RedirectResponse
    {
        $menu->deleteStoredImage();

        $menu->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('status', 'Menu berhasil dihapus.');
    }
}