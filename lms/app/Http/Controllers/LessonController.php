<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Activity;
use App\Models\Subject;
use App\Models\Section;
use App\Models\CurriculumObjective;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::with(['teacher', 'subject', 'section', 'curriculumObjective', 'academicYear', 'semester'])
            ->active();

        // Filter by teacher (if teacher role)
        if (Auth::user()->role_name === 'Teacher') {
            $teacher = Auth::user()->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $lessons = $query->orderBy('lesson_date', 'desc')->paginate(15);

        $subjects = Subject::all();
        $sections = Section::all();
        $curriculumObjectives = CurriculumObjective::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('lessons.index', compact(
            'lessons',
            'subjects',
            'sections',
            'curriculumObjectives',
            'academicYears',
            'semesters'
        ));
    }

    public function create()
    {
        $subjects = Subject::all();
        $sections = Section::all();
        $curriculumObjectives = CurriculumObjective::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('lessons.create', compact(
            'subjects',
            'sections',
            'curriculumObjectives',
            'academicYears',
            'semesters'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'curriculum_objective_id' => 'required|exists:curriculum_objectives,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'lesson_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $data = $request->all();
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }
        $data['teacher_id'] = $teacher->id;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $fileName;
        }

        Lesson::create($data);

        return redirect()->route('lessons.index')
            ->with('success', 'Lesson created successfully.');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load(['teacher', 'subject', 'section', 'curriculumObjective', 'academicYear', 'semester', 'activities']);
        
        return view('lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        // Check if teacher owns this lesson
        if (Auth::user()->role_name === 'Teacher') {
            $teacher = Auth::user()->teacher;
            if ($teacher && $lesson->teacher_id !== $teacher->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $subjects = Subject::all();
        $sections = Section::all();
        $curriculumObjectives = CurriculumObjective::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('lessons.edit', compact(
            'lesson',
            'subjects',
            'sections',
            'curriculumObjectives',
            'academicYears',
            'semesters'
        ));
    }

    public function update(Request $request, Lesson $lesson)
    {
        // Check if teacher owns this lesson
        if (Auth::user()->role_name === 'Teacher') {
            $teacher = Auth::user()->teacher;
            if ($teacher && $lesson->teacher_id !== $teacher->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'curriculum_objective_id' => 'required|exists:curriculum_objectives,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'lesson_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $fileName;
        }

        $lesson->update($data);

        return redirect()->route('lessons.index')
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Lesson $lesson)
    {
        // Check if teacher owns this lesson
        if (Auth::user()->role_name === 'Teacher') {
            $teacher = Auth::user()->teacher;
            if ($teacher && $lesson->teacher_id !== $teacher->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Delete file if exists
        if ($lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('lessons.index')
            ->with('success', 'Lesson deleted successfully.');
    }

    public function publish(Lesson $lesson)
    {
        // Check if teacher owns this lesson
        if (Auth::user()->role_name === 'Teacher') {
            $teacher = Auth::user()->teacher;
            if ($teacher && $lesson->teacher_id !== $teacher->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $lesson->update(['status' => 'published']);

        return redirect()->route('lessons.index')
            ->with('success', 'Lesson published successfully.');
    }

    public function complete(Lesson $lesson)
    {
        // Check if teacher owns this lesson
        if (Auth::user()->role_name === 'Teacher') {
            $teacher = Auth::user()->teacher;
            if ($teacher && $lesson->teacher_id !== $teacher->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $lesson->update(['status' => 'completed']);

        return redirect()->route('lessons.index')
            ->with('success', 'Lesson marked as completed.');
    }
} 