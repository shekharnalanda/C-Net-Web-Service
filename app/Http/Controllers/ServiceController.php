<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function show(string $slug)
    {
        $service = DB::table('services')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        abort_unless($service, 404);

        return view('service-detail', compact('service'));
    }

    public function index()
    {
        $services = DB::table('services')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.form', ['service' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validateService($request);
        $slug = Str::slug($data['slug'] ?: $data['title']);

        DB::table('services')->insert([
            'title' => $data['title'],
            'slug' => $slug,
            'icon' => $data['icon'] ?: '🌐',
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(int $id)
    {
        $service = DB::table('services')->find($id);
        abort_unless($service, 404);

        return view('admin.services.form', compact('service'));
    }

    public function update(Request $request, int $id)
    {
        abort_unless(DB::table('services')->where('id', $id)->exists(), 404);

        $data = $this->validateService($request, $id);
        $slug = Str::slug($data['slug'] ?: $data['title']);

        DB::table('services')->where('id', $id)->update([
            'title' => $data['title'],
            'slug' => $slug,
            'icon' => $data['icon'] ?: '🌐',
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(int $id)
    {
        DB::table('services')->where('id', $id)->delete();

        return back()->with('success', 'Service deleted.');
    }

    private function validateService(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => [
                'nullable',
                'string',
                'max:170',
                Rule::unique('services', 'slug')->ignore($id),
            ],
            'icon' => ['nullable', 'string', 'max:20'],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
    }
}
