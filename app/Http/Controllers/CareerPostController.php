<?php

namespace App\Http\Controllers;

use App\Models\CareerPost;
use Illuminate\Http\Request;

class CareerPostController extends Controller
{
    public function index()
    {
        return view('content.careers.index', [
            'records' => CareerPost::with('creator')->latest()->paginate(request('limit', 10))->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['published_at'] = $data['status'] === 'Published' ? now() : null;
        $data['created_by'] = $request->user()->id;
        CareerPost::create($data);

        return back()->with('status', 'Konten karir berhasil ditambahkan.');
    }

    public function update(Request $request, CareerPost $careerPost)
    {
        $data = $this->validated($request);
        $data['published_at'] = $data['status'] === 'Published' ? ($careerPost->published_at ?: now()) : null;
        $careerPost->update($data);

        return back()->with('status', 'Konten karir berhasil diperbarui.');
    }

    public function destroy(CareerPost $careerPost)
    {
        $careerPost->delete();

        return back()->with('status', 'Konten karir berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:Loker,Job Fair'],
            'title' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'external_url' => ['nullable', 'url'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Draft,Published'],
        ]);
    }
}
