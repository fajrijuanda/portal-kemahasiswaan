<?php

namespace App\Http\Controllers;

use App\Models\Beasiswa;
use App\Models\Competition;
use App\Models\Prestasi;
use App\Models\ScholarshipType;
use App\Models\Semester;
use Illuminate\Http\Request;

class StudentSubmissionController extends Controller
{
    public function index(Request $request)
    {
        return view('student.submissions.index', [
            'semester' => $this->activeSemester(),
            'scholarshipTypes' => ScholarshipType::where('is_active', true)->orderBy('nama')->get(),
            'competitions' => Competition::where('is_active', true)->orderBy('nama')->get(),
            'beasiswa' => Beasiswa::with(['semester', 'prodi', 'scholarshipType'])->where('created_by', $request->user()->id)->latest()->get(),
            'prestasi' => Prestasi::with(['semester', 'prodi', 'competition'])->where('created_by', $request->user()->id)->latest()->get(),
        ]);
    }

    public function storeBeasiswa(Request $request)
    {
        $user = $request->user();
        abort_unless($user->prodi_id, 422, 'Akun mahasiswa belum memiliki prodi.');

        $data = $request->validate([
            'scholarship_type_id' => ['required', 'exists:scholarship_types,id'],
            'nominal' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        $type = ScholarshipType::findOrFail($data['scholarship_type_id']);
        Beasiswa::create($data + [
            'semester_id' => $this->activeSemester()->id,
            'prodi_id' => $user->prodi_id,
            'nama_mahasiswa' => $user->name,
            'nim' => $user->nim,
            'jenis_beasiswa' => $type->nama,
            'status' => 'Diajukan',
            'created_by' => $user->id,
        ]);

        return back()->with('status', 'Pengajuan beasiswa berhasil dikirim.');
    }

    public function storePrestasi(Request $request)
    {
        $user = $request->user();
        abort_unless($user->prodi_id, 422, 'Akun mahasiswa belum memiliki prodi.');

        $data = $request->validate([
            'competition_id' => ['required', 'exists:competitions,id'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'kategori_event' => ['required', 'string', 'in:Kelompok,Perorangan'],
            'scope' => ['required', 'string', 'in:Lokal,Regional,Nasional,Internasional'],
            'juara' => ['required', 'string', 'in:1,2,3,Favorit,Finalis,Harapan'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'publikasi_url' => ['nullable', 'url'],
            'catatan' => ['nullable', 'string'],
        ]);

        Prestasi::create($data + [
            'semester_id' => $this->activeSemester()->id,
            'prodi_id' => $user->prodi_id,
            'nama_mahasiswa' => $user->name,
            'nim' => $user->nim,
            'tingkat' => $data['scope'],
            'peringkat' => 'Juara '.$data['juara'],
            'status' => 'Diajukan',
            'created_by' => $user->id,
        ]);

        return back()->with('status', 'Pengajuan lomba berhasil dikirim.');
    }

    private function activeSemester(): Semester
    {
        return Semester::where('is_active', true)->latest()->first() ?? Semester::latest()->firstOrFail();
    }
}
