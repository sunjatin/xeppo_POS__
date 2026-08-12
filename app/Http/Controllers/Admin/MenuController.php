<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Kitchen;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->paginate(10);
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $kitchens = Kitchen::all(); // Ambil data dapur
        return view('admin.menus.create', compact('kitchens'));
        // return view('admin.menus.create');
    }

    public function store(Request $request)
    {


        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'kitchen_id' => 'nullable|exists:kitchens,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $kitchens = Kitchen::all(); // Ambil data dapur
        return view('admin.menus.edit', compact('menu', 'kitchens'));
        // return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'kitchen_id' => 'nullable|exists:kitchens,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diupdate');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image) Storage::disk('public')->delete($menu->image);
        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus');
    }
}
