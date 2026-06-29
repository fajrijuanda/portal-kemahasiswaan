<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Ormawa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class OrmawaController extends Controller
{
    public function index()
    {
        $query = $this->hasTable('ormawas') ? Ormawa::query()->orderBy('nama') : null;
        if ($query && $this->hasOrmawaUserColumn()) {
            $query->with('user');
        }

        return view('master.ormawa.index', [
            'ormawas' => $query ? $query->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
            'users' => $this->hasOrmawaUserColumn() && $this->hasPermissionTables() ? User::role('ormawa')->orderBy('name')->get() : collect(),
            'canLinkUser' => $this->hasOrmawaUserColumn(),
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
        $data = $request->validate([
            'user_id' => $this->hasOrmawaUserColumn() ? ['nullable', 'exists:users,id'] : ['exclude'],
            'nama' => ['required', 'string', 'max:255', 'unique:ormawas,nama,'.($id ?: 'NULL').',id'],
            'jenis' => ['nullable', 'string', 'max:255'],
            'pembina' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Aktif,Nonaktif'],
        ]);

        if (! $this->hasOrmawaUserColumn()) {
            unset($data['user_id']);
        }

        return $data;
    }

    private function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function hasOrmawaUserColumn(): bool
    {
        return $this->hasTable('ormawas')
            && $this->hasTable('users')
            && Schema::hasColumn('ormawas', 'user_id');
    }

    private function hasPermissionTables(): bool
    {
        return $this->hasTable('roles')
            && $this->hasTable('model_has_roles');
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, request('limit', 10), 1, ['path' => request()->url()]);
    }
}
