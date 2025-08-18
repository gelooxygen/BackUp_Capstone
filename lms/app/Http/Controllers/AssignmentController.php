<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role_name, ['Admin', 'Teacher'])) {
                abort(403, 'Only teachers and administrators can manage assignments.');
            }
            return $next($request);
        });
    }

    /**
     * Check if user can create assignments (only teachers)
     */
    private function canCreateAssignment()
    {
        if (auth()->user()->role_name === 'Admin') {
            return false; // Admins cannot create assignments
        }
        return auth()->user()->role_name === 'Teacher';
    }

    /**
     * Display a listing of assignments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Assignment::with(['teacher', 'subject', 'section', 'academicYear', 'semester']);

        // Filter by teacher if not admin
        if ($user->role_name === 'Teacher') {
            $teacher = $user->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        $subjects = Subject::all();
        $sections = Section::all();

        return view('assignments.index', compact('assignments', 'subjects', 'sections'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create()
    {
        if (!$this->canCreateAssignment()) {
            abort(403, 'Only teachers can create assignments.');
        }

        $user = Auth::user();
        $subjects = Subject::all();
        $sections = Section::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        // Filter subjects by teacher
        $teacher = $user->teacher;
        if ($teacher) {
            $subjects = $teacher->subjects;
        }

        return view('assignments.create', compact('subjects', 'sections', 'academicYears', 'semesters'));
    }

    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        if (!$this->canCreateAssignment()) {
            abort(403, 'Only teachers can create assignments.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'due_date' => 'required|date|after_or_equal:today',
            'due_time' => 'nullable|date_format:H:i',
            'max_score' => 'required|numeric|min:0|max:1000',
            'allows_late_submission' => 'boolean',
            'late_submission_penalty' => 'nullable|numeric|min:0|max:100',
            'requires_file_upload' => 'boolean',
            'submission_instructions' => 'nullable|string',
            'allowed_file_types' => 'nullable|array',
            'max_file_size' => 'nullable|numeric|min:1|max:50',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:10240'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        $data = $request->all();
        $data['teacher_id'] = $teacher->id;
        $data['allows_late_submission'] = $request->has('allows_late_submission');
        $data['requires_file_upload'] = $request->has('requires_file_upload');

        // Handle file upload
        if ($request->hasFile('assignment_file')) {
            $file = $request->file('assignment_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        Assignment::create($data);

        return redirect()->route('assignments.index')->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified assignment
     */
    public function show(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $assignment->load(['teacher', 'subject', 'section', 'academicYear', 'semester', 'submissions.student']);
        
        return view('assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified assignment
     */
    public function edit(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $subjects = Subject::all();
        $sections = Section::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        
        return view('assignments.edit', compact('assignment', 'subjects', 'sections', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified assignment
     */
    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'due_date' => 'required|date',
            'due_time' => 'nullable|date_format:H:i',
            'max_score' => 'required|numeric|min:0|max:1000',
            'allows_late_submission' => 'boolean',
            'late_submission_penalty' => 'nullable|numeric|min:0|max:100',
            'requires_file_upload' => 'boolean',
            'submission_instructions' => 'nullable|string',
            'allowed_file_types' => 'nullable|array',
            'max_file_size' => 'nullable|numeric|min:1|max:50',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:10240'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['allows_late_submission'] = $request->has('allows_late_submission');
        $data['requires_file_upload'] = $request->has('requires_file_upload');

        // Handle file upload
        if ($request->hasFile('assignment_file')) {
            // Delete old file if exists
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            
            $file = $request->file('assignment_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        $assignment->update($data);

        return redirect()->route('assignments.show', $assignment)->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        // Delete associated file
        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Publish assignment
     */
    public function publish(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $assignment->update(['status' => 'published']);
        
        return redirect()->back()->with('success', 'Assignment published successfully.');
    }

    /**
     * Close assignment
     */
    public function close(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $assignment->update(['status' => 'closed']);
        
        return redirect()->back()->with('success', 'Assignment closed successfully.');
    }

    /**
     * Show submissions for an assignment
     */
    public function submissions(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $submissions = $assignment->submissions()
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);
        
        return view('assignments.submissions', compact('assignment', 'submissions'));
    }

    /**
     * Grade a submission
     */
    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $this->authorizeAssignment($submission->assignment);

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->assignment->max_score,
            'feedback' => 'nullable|string'
        ]);

        $submission->markAsGraded($request->score, $request->feedback);

        return redirect()->back()->with('success', 'Submission graded successfully.');
    }

    /**
     * Export assignments to PDF
     */
    public function exportPdf(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $assignment->load(['teacher', 'subject', 'section', 'submissions.student']);
        
        $pdf = PDF::loadView('assignments.pdf', compact('assignment'));
        
        return $pdf->download("assignment_{$assignment->id}.pdf");
    }

    /**
     * Export assignments to Excel
     */
    public function exportExcel()
    {
        $user = Auth::user();
        $query = Assignment::with(['teacher', 'subject', 'section']);

        if ($user->role_name === 'Teacher') {
            $teacher = $user->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        $assignments = $query->get();

        // TODO: Create AssignmentsExport class
        return redirect()->back()->with('info', 'Excel export feature coming soon.');
    }

    /**
     * Authorize assignment access
     */
    private function authorizeAssignment(Assignment $assignment)
    {
        $user = Auth::user();
        
        if ($user->role_name === 'Admin') {
            return true;
        }
        
        if ($user->role_name === 'Teacher') {
            $teacher = $user->teacher;
            if ($teacher && $teacher->id === $assignment->teacher_id) {
                return true;
            }
        }
        
        abort(403, 'Unauthorized action.');
    }
}
