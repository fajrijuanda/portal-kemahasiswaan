<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        return view('master.semester.index', ['semesters' => Semester::orderByDesc('id')->paginate(request('limit', 10))->withQueryString()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($data['is_active'] ?? false) {
            Semester::query()->update(['is_active' => false]);
        }
        Semester::create($data);

        return back()->with('status', 'Semester berhasil ditambahkan.');
    }

    public function update(Request $request, Semester $semester)
    {
        $data = $this->validated($request);
        if ($data['is_active'] ?? false) {
            Semester::whereKeyNot($semester->id)->update(['is_active' => false]);
        }
        $semester->update($data);

        return back()->with('status', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();

        return back()->with('status', 'Semester berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tahun_akademik' => ['required', 'string', 'max:20'],
            'periode' => ['required', 'in:Ganjil,Genap'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
