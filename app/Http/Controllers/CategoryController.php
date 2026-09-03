<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $kategori = Category::orderBy('type')->orderBy('name')->get();

        return view('categories.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pemasukan,pengeluaran'],
        ]);

        Category::create($data);

        return back()->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function destroy(Category $category)
    {
        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai di transaksi.');
        }

        $category->delete();

        return back()->with('status', 'Kategori berhasil dihapus.');
    }
}
