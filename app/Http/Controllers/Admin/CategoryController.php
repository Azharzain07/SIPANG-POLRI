<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua "Bagian".
     */
    public function index()
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat "Bagian" baru.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Menyimpan "Bagian" baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories',
        ]);

        Category::create($request->only('nama_kategori'));
        
        return redirect()->route('categories.index')->with('success', 'Bagian baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit "Bagian".
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Memperbarui "Bagian" di database.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
        ]);
        
        $category->update($request->only('nama_kategori'));

        return redirect()->route('categories.index')->with('success', 'Bagian berhasil diperbarui!');
    }

    /**
     * Menghapus "Bagian" dari database.
     */
    public function destroy(Category $category)
    {
        // Di masa depan, Anda bisa menambahkan pengecekan apakah "Bagian" ini sedang digunakan
        // dalam sebuah pengajuan sebelum dihapus. Untuk sekarang, kita langsung hapus.
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Bagian berhasil dihapus!');
    }
}

