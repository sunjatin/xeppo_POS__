<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view('admin.areas.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'capacity' => 'required|integer']);
        Area::create($request->all());
        return back()->with('success', 'Area berhasil ditambahkan');
    }

    public function update(Request $request, Area $area)
    {
        $request->validate(['name' => 'required', 'capacity' => 'required|integer']);
        $area->update($request->all());
        return back()->with('success', 'Area berhasil diupdate');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area berhasil dihapus');
    }
}
