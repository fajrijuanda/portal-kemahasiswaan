<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\OrmawaProposal;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrmawaPanelController extends Controller
{
    public function index(Request $request)
    {
        $ormawa = $request->user()->ormawa;
        abort_unless($ormawa, 403);

        return view('ormawa.panel.index', [
            'ormawa' => $ormawa->load(['proposals.semester', 'reimbursements.semester', 'reimbursements.prodi']),
            'semester' => $this->activeSemester(),
        ]);
    }

    public function storeProposal(Request $request)
    {
        $ormawa = $request->user()->ormawa;
        abort_unless($ormawa, 403);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'proposal_path' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        unset($data['proposal_path']);
        if ($request->hasFile('proposal_path')) {
            $data['proposal_path'] = $request->file('proposal_path')->store('ormawa-proposals', 'public');
        }

        OrmawaProposal::create($data + [
            'ormawa_id' => $ormawa->id,
            'semester_id' => $this->activeSemester()->id,
            'status' => 'Diajukan',
            'created_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Proposal kegiatan berhasil diajukan.');
    }

    public function storeReimbursement(Request $request)
    {
        $user = $request->user();
        $ormawa = $user->ormawa;
        abort_unless($ormawa, 403);
        abort_unless($user->prodi_id, 422, 'Akun Ormawa belum memiliki prodi penanggung jawab.');

        $data = $request->validate([
            'jenis_reimbursement' => ['required', 'string', 'in:Akomodasi,Pendaftaran,Transport,Fasilitas,Lainnya'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'nominal' => ['nullable', 'numeric', 'min:0'],
            'foto_path' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'surat_tugas_path' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'sertifikat_path' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'link_penyelenggara' => ['required', 'url'],
            'catatan' => ['nullable', 'string'],
        ]);

        foreach (['foto_path', 'surat_tugas_path', 'sertifikat_path'] as $fileField) {
            $data[$fileField] = $request->file($fileField)->store($fileField, 'public');
        }

        Event::create($data + [
            'semester_id' => $this->activeSemester()->id,
            'prodi_id' => $user->prodi_id,
            'ormawa_id' => $ormawa->id,
            'nama_pengaju' => $ormawa->nama,
            'nim' => null,
            'status' => 'Diajukan',
            'created_by' => $user->id,
        ]);

        return back()->with('status', 'Reimbursement Ormawa berhasil diajukan.');
    }

    private function activeSemester(): Semester
    {
        return Semester::where('is_active', true)->latest()->first() ?? Semester::latest()->firstOrFail();
    }
}
