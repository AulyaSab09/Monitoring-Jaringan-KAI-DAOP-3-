<?php

namespace App\Http\Controllers;

use App\Models\Stasiun;
use Illuminate\Http\Request;

class StasiunController extends Controller
{
    // Menampilkan daftar stasiun
    public function index()
    {
        $stasiuns = Stasiun::all();
        return view('stasiun', compact('stasiuns'));
    }

    // Menampilkan form tambah (opsional jika tidak pakai modal)
    public function create()
    {
        return view('stasiun.create');
    }

    // Menyimpan stasiun baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_stasiun' => 'required',
            'kode_stasiun' => 'required|unique:stasiun,kode_stasiun',
        ]);

        Stasiun::create($request->all());

        return redirect()->route('stasiun.index')->with('success', 'Stasiun berhasil ditambahkan!');
    }

    // Menghapus stasiun
    public function destroy(Stasiun $stasiun)
    {
        $stasiun->delete();
        return redirect()->route('stasiun.index')->with('success', 'Stasiun berhasil dihapus!');
    }
}