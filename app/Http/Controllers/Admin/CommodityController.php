<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CONTROLLER: the administrator manages the list of monitored commodities.
 * (Commodities are switched off instead of deleted so old prices stay valid.)
 */
class CommodityController extends Controller
{
    public function index(Request $request)
    {
        $commodities = Commodity::query()
            ->when($request->query('category'), fn ($q, $c) => $q->where('category', $c))
            ->when($request->query('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('category')->orderBy('name')
            ->get();

        return view('admin.commodities.index', [
            'commodities' => $commodities,
            'categories' => config('tindapresyo.sections'),
            'filters' => $request->only(['category', 'q']),
        ]);
    }

    public function create()
    {
        return view('admin.commodities.form', ['commodity' => new Commodity(['unit' => 'kg', 'is_active' => true])] + $this->options());
    }

    public function store(Request $request)
    {
        Commodity::create($this->validated($request));

        return redirect()->route('admin.commodities.index')->with('success', 'Commodity added.');
    }

    public function edit(Commodity $commodity)
    {
        return view('admin.commodities.form', ['commodity' => $commodity] + $this->options());
    }

    public function update(Request $request, Commodity $commodity)
    {
        $commodity->update($this->validated($request, $commodity));

        return redirect()->route('admin.commodities.index')->with('success', 'Commodity updated.');
    }

    private function options(): array
    {
        return ['categories' => config('tindapresyo.sections'), 'units' => config('tindapresyo.units')];
    }

    private function validated(Request $request, ?Commodity $commodity = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('commodities', 'name')->ignore($commodity?->id)],
            'category' => ['required', Rule::in(config('tindapresyo.sections'))],
            'unit' => ['required', Rule::in(config('tindapresyo.units'))],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
