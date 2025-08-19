<?php

namespace App\Http\Controllers;

use App\Models\Funder;
use Illuminate\Http\Request;

class FunderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Funder::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
        }

        $funders = $query->latest()->paginate(10);
        return view('funders.index', compact('funders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('funders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Funder::create($request->all());

        return redirect()->route('funders.index')
                         ->with('success', __('main.funder_added_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Funder $funder)
    {
        $funder->load('projects');
        return view('funders.show', compact('funder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Funder $funder)
    {
        return view('funders.edit', compact('funder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Funder $funder)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $funder->update($request->all());

        return redirect()->route('funders.index')
                         ->with('success', __('main.funder_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funder $funder)
    {
        $funder->delete();

        return redirect()->route('funders.index')
                         ->with('success', __('main.funder_deleted_successfully'));
    }
}
