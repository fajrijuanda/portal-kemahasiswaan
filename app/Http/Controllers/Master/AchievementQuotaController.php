<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AchievementQuota;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Http\Request;

class AchievementQuotaController extends Controller
{
    public function index()
    {
        return view('master.quotas.index', [
            'quotas' => AchievementQuota::with(['semester', 'prodi'])->latest()->paginate(request('limit', 10))->withQueryString(),
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        AchievementQuota::updateOrCreate(
            ['semester_id' => $data['semester_id'], 'prodi_id' => $data['prodi_id']],
            ['slot_prestasi' => $data['slot_prestasi']]
        );

        return back()->with('status', 'Kuota prestasi berhasil disimpan.');
    }

    public function update(Request $request, AchievementQuota $quota)
    {
        $quota->update($this->validated($request));

        return back()->with('status', 'Kuota prestasi berhasil diperbarui.');
    }

    public function destroy(AchievementQuota $quota)
    {
        $quota->delete();

        return back()->with('status', 'Kuota prestasi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'semester_id' => ['required', 'exists:semesters,id'],
            'prodi_id' => ['required', 'exists:prodis,id'],
            'slot_prestasi' => ['required', 'integer', 'min:0'],
        ]);
    }
}
