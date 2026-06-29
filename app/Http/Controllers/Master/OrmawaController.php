<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Ormawa;
use App\Models\User;
use Illuminate\Http\Request;

class OrmawaController extends Controller
{
    public function index()
    {
        return view('master.ormawa.index', [
            'ormawas' => Ormawa::with(['user', 'activities', 'proposals', 'reimbursements'])->orderBy('nama')->paginate(request('limit', 10))->withQueryString(),
            'users' => User::role('ormawa')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Ormawa::create($this->validated($request));

        return back()->with('status', 'Ormawa berhasil ditambahkan.');
    }

    public function update(Request $request, Ormawa $ormawa)
    {
        $ormawa->update($this->validated($request, $ormawa->id));

        return back()->with('status', 'Ormawa berhasil diperbarui.');
    }

    public function destroy(Ormawa $ormawa)
    {
        $ormawa->delete();

        return back()->with('status', 'Ormawa berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'nama' => ['required', 'string', 'max:255', 'unique:ormawas,nama,'.($id ?: 'NULL').',id'],
            'jenis' => ['nullable', 'string', 'max:255'],
            'pembina' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Aktif,Nonaktif'],
        ]);
    }
}
