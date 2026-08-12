<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kitchen;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        $kitchens = Kitchen::withCount('menus')->get();
        return view('admin.kitchens.index', compact('kitchens'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Kitchen::create($request->all());
        return back()->with('success', 'Dapur berhasil ditambahkan');
    }

    public function update(Request $request, Kitchen $kitchen)
    {
        $request->validate(['name' => 'required']);
        $kitchen->update($request->all());
        return back()->with('success', 'Dapur berhasil diupdate');
    }

    public function destroy(Kitchen $kitchen)
    {
        $kitchen->delete();
        return back()->with('success', 'Dapur berhasil dihapus');
    }
}