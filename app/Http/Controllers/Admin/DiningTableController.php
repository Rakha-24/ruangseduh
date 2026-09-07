<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiningTableRequest;
use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiningTableController extends Controller
{
    public function index(): View
    {
        $tables = DiningTable::query()
            ->withCount('reservations')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.dining-tables.index', compact('tables'));
    }

    public function create(): View
    {
        return view('admin.dining-tables.create');
    }

    public function store(DiningTableRequest $request): RedirectResponse
    {
        DiningTable::create([
            'name' => $request->input('name'),
            'capacity' => $request->integer('capacity'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.dining-tables.index')
            ->with('status', 'Meja berhasil ditambahkan.');
    }

    public function edit(DiningTable $diningTable): View
    {
        return view('admin.dining-tables.edit', compact('diningTable'));
    }

    public function update(DiningTableRequest $request, DiningTable $diningTable): RedirectResponse
    {
        $diningTable->update([
            'name' => $request->input('name'),
            'capacity' => $request->integer('capacity'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.dining-tables.index')
            ->with('status', 'Meja berhasil diperbarui.');
    }

    /**
     * Hapus meja. Diblokir bila masih ada riwayat reservasi
     * (disarankan menonaktifkan meja untuk kasus rusak).
     */
    public function destroy(DiningTable $diningTable): RedirectResponse
    {
        if ($diningTable->reservations()->exists()) {
            return back()->withErrors([
                'delete' => 'Meja ini memiliki riwayat reservasi sehingga tidak dapat dihapus. Nonaktifkan meja saja jika sedang tidak tersedia.',
            ]);
        }

        $diningTable->delete();

        return redirect()
            ->route('admin.dining-tables.index')
            ->with('status', 'Meja berhasil dihapus.');
    }

    public function toggle(DiningTable $diningTable): RedirectResponse
    {
        $diningTable->update(['is_active' => ! $diningTable->is_active]);

        return back()->with('status', $diningTable->is_active
            ? "Meja {$diningTable->name} diaktifkan kembali."
            : "Meja {$diningTable->name} dinonaktifkan.");
    }
}