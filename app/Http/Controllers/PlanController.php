<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function publicIndex()
    {
        $plans = DB::table('plans')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('plans', compact('plans'));
    }

    public function index()
    {
        $plans = DB::table('plans')->orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.form', ['plan' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request);
        $slug = Str::slug($data['slug'] ?: $data['title']);

        DB::table('plans')->insert([
            'title' => $data['title'],
            'slug' => $slug,
            'service_type' => $data['service_type'],
            'price_label' => $data['price_label'],
            'duration' => $data['duration'],
            'features' => $data['features'],
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(int $id)
    {
        $plan = DB::table('plans')->find($id);
        abort_unless($plan, 404);

        return view('admin.plans.form', compact('plan'));
    }

    public function update(Request $request, int $id)
    {
        abort_unless(DB::table('plans')->where('id', $id)->exists(), 404);
        $data = $this->validatePlan($request, $id);

        DB::table('plans')->where('id', $id)->update([
            'title' => $data['title'],
            'slug' => Str::slug($data['slug'] ?: $data['title']),
            'service_type' => $data['service_type'],
            'price_label' => $data['price_label'],
            'duration' => $data['duration'],
            'features' => $data['features'],
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(int $id)
    {
        DB::table('plans')->where('id', $id)->delete();
        return back()->with('success', 'Plan deleted.');
    }

    private function validatePlan(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', Rule::unique('plans', 'slug')->ignore($id)],
            'service_type' => ['required', 'string', 'max:100'],
            'price_label' => ['required', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:100'],
            'features' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
    }
}
