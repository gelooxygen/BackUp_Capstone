<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Subject;

class CurriculumController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Curriculum::class, 'curriculum');
    }

    public function index()
    {
        $curricula = Curriculum::with('subjects')->orderBy('grade_level')->get();
        return view('curriculum.index', compact('curricula'));
    }

    public function create()
    {
        return view('curriculum.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Curriculum::create($request->only(['grade_level', 'description']));
        return redirect()->route('curriculum.index')->with('success', 'Curriculum created successfully.');
    }

    public function show($id)
    {
        $curriculum = Curriculum::with('subjects')->findOrFail($id);
        $allSubjects = Subject::orderBy('subject_name')->get();
        return view('curriculum.show', compact('curriculum', 'allSubjects'));
    }

    public function edit($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        return view('curriculum.edit', compact('curriculum'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->update($request->only(['grade_level', 'description']));
        return redirect()->route('curriculum.index')->with('success', 'Curriculum updated successfully.');
    }

    public function destroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->delete();
        return redirect()->route('curriculum.index')->with('success', 'Curriculum deleted successfully.');
    }

    public function assignSubjectsForm($id)
    {
        $curriculum = Curriculum::with('subjects')->findOrFail($id);
        $this->authorize('update', $curriculum);
        $subjects = Subject::orderBy('subject_name')->get();
        $assigned = $curriculum->subjects->pluck('id')->toArray();
        return view('curriculum.assign_subjects', compact('curriculum', 'subjects', 'assigned'));
    }

    public function assignSubjects(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $this->authorize('update', $curriculum);
        $subjectIds = $request->input('subject_ids', []);
        $curriculum->subjects()->sync($subjectIds);
        return redirect()->route('curriculum.show', $id)->with('success', 'Subjects assigned successfully.');
    }
}
