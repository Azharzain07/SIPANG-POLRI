<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.programs.index', ['programs' => Program::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
     {
        return view('admin.programs.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['nama_program' => 'required|string|max:255']);
        Program::create($validated);
        return redirect()->route('programs.index')->with('success', 'Program berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
     {
        return view('admin.programs.form', compact('program'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $validated = $request->validate(['nama_program' => 'required|string|max:255']);
        $program->update($validated);
        return redirect()->route('programs.index')->with('success', 'Program berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('programs.index')->with('success', 'Program berhasil dihapus.');
    }
}
