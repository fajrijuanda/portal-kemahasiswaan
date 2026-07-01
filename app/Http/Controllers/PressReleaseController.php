<?php

namespace App\Http\Controllers;

use App\Models\PressRelease;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PressReleaseController extends Controller
{
    public function index()
    {
        return view('content.press-releases.index', [
            'records' => PressRelease::with('creator')->latest()->paginate(request('limit', 10))->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('content.press-releases.form', [
            'record' => null,
            'title' => 'Tambah Berita',
            'subtitle' => 'Tulis berita dengan editor lengkap seperti dokumen.',
            'action' => route('press-releases.store'),
            'method' => null,
        ]);
    }

    public function edit(PressRelease $pressRelease)
    {
        return view('content.press-releases.form', [
            'record' => $pressRelease,
            'title' => 'Edit Berita',
            'subtitle' => 'Perbarui judul, ringkasan, cover, status, dan isi berita.',
            'action' => route('press-releases.update', $pressRelease),
            'method' => 'PUT',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->prepare($request, $data);
        $data['created_by'] = $request->user()->id;
        PressRelease::create($data);

        return redirect()->route('publications.index', 'berita')->with('status', 'Berita berhasil ditambahkan.');
    }

    public function update(Request $request, PressRelease $pressRelease)
    {
        $data = $this->validated($request, $pressRelease->id);
        $this->prepare($request, $data, $pressRelease);
        $pressRelease->update($data);

        return back()->with('status', 'Berita berhasil diperbarui.');
    }

    public function destroy(PressRelease $pressRelease)
    {
        if ($pressRelease->cover_path) {
            Storage::disk('public')->delete($pressRelease->cover_path);
        }
        $pressRelease->delete();

        return back()->with('status', 'Berita berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'cover_path' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'string', 'in:Draft,Published'],
        ]);
    }

    private function prepare(Request $request, array &$data, ?PressRelease $record = null): void
    {
        $data['slug'] = $this->uniqueSlug($data['title'], $record?->id);
        $data['content'] = $this->sanitizeContent($data['content'] ?? '');
        $data['excerpt'] = trim(strip_tags($data['excerpt'] ?? ''));
        $data['published_at'] = $data['status'] === 'Published' ? now() : null;
        unset($data['cover_path']);

        if ($request->hasFile('cover_path')) {
            if ($record?->cover_path) {
                Storage::disk('public')->delete($record->cover_path);
            }
            $data['cover_path'] = $request->file('cover_path')->store('press-releases', 'public');
        }
    }

    private function sanitizeContent(string $content): string
    {
        return RichTextSanitizer::clean($content);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 2;

        while (PressRelease::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
