<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        return view('master.prodi.index', ['prodis' => Prodi::orderBy('nama')->paginate(request('limit', 10))->withQueryString()]);
    }

    public function store(Request $request)
    {
        Prodi::create($request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50'],
            'fakultas' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Prodi berhasil ditambahkan.');
    }

    public function update(Request $request, Prodi $prodi)
    {
        $prodi->update($request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50'],
            'fakultas' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Prodi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();

        return back()->with('status', 'Prodi berhasil dihapus.');
    }
}
