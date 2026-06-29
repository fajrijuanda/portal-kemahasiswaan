<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\ScholarshipType;
use Illuminate\Http\Request;

class SimpleMasterController extends Controller
{
    private array $masters = [
        'competitions' => ['title' => 'Master Lomba', 'model' => Competition::class],
        'scholarship-types' => ['title' => 'Jenis Beasiswa', 'model' => ScholarshipType::class],
    ];

    public function index(string $master)
    {
        $config = $this->config($master);
        $model = $config['model'];

        return view('master.simple.index', [
            'master' => $master,
            'config' => $config,
            'records' => $model::orderBy('nama')->paginate(request('limit', 25))->withQueryString(),
        ]);
    }

    public function store(Request $request, string $master)
    {
        $config = $this->config($master);
        $config['model']::create($this->validated($request));

        return back()->with('status', $config['title'].' berhasil ditambahkan.');
    }

    public function update(Request $request, string $master, int $id)
    {
        $config = $this->config($master);
        $record = $config['model']::findOrFail($id);
        $record->update($this->validated($request, $id, $config['model']));

        return back()->with('status', $config['title'].' berhasil diperbarui.');
    }

    public function destroy(string $master, int $id)
    {
        $config = $this->config($master);
        $config['model']::findOrFail($id)->delete();

        return back()->with('status', $config['title'].' berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null, ?string $model = null): array
    {
        $table = $model ? (new $model)->getTable() : null;

        return $request->validate([
            'nama' => array_filter(['required', 'string', 'max:255', $table ? 'unique:'.$table.',nama,'.($id ?: 'NULL').',id' : null]),
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function config(string $master): array
    {
        abort_unless(isset($this->masters[$master]), 404);

        return $this->masters[$master];
    }
}
