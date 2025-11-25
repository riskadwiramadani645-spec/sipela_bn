<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $data = TahunAjaran::all();
        return view('admin.master-data.tahun-ajaran', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_tahun' => 'required|unique:tahun_ajaran',
            'tahun_ajaran' => 'required',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        TahunAjaran::create($request->all());
        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        $data = TahunAjaran::findOrFail($id);
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($data);
        }
        
        return redirect()->route('admin.master-data.tahun-ajaran');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_tahun' => 'required|unique:tahun_ajaran,kode_tahun,' . $id . ',tahun_ajaran_id',
            'tahun_ajaran' => 'required',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($request->all());
        
        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();
        
        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus');
    }
}