<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Daftar kategori (dengan jumlah item menu).
     */
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('menuItems')
            ->latest()
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Form tambah kategori.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Simpan kategori baru.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Perbarui kategori (slug dihasilkan ulang mengikuti nama).
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori. Diblokir bila masih memiliki item menu
     * (agar tidak menghapus menu secara tidak sengaja).
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->menuItems()->exists()) {
            return back()->withErrors([
                'delete' => 'Kategori ini masih memiliki item menu dan tidak dapat dihapus.',
            ]);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}