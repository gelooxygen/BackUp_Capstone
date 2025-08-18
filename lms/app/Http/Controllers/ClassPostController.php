<?php

namespace App\Http\Controllers;

use App\Models\ClassPost;
use App\Models\ClassPostComment;
use App\Models\Subject;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClassPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role_name, ['Admin', 'Teacher'])) {
                abort(403, 'Only teachers and administrators can create class posts.');
            }
            return $next($request);
        });
    }

    /**
     * Check if user can create class posts (only teachers)
     */
    private function canCreateClassPost()
    {
        if (auth()->user()->role_name === 'Admin') {
            return false; // Admins cannot create class posts
        }
        return auth()->user()->role_name === 'Teacher';
    }

    /**
     * Display a listing of class posts
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ClassPost::with(['teacher', 'subject', 'section', 'academicYear', 'semester']);

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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('is_pinned', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);
        
        $subjects = Subject::all();
        $sections = Section::all();

        return view('class-posts.index', compact('posts', 'subjects', 'sections'));
    }

    /**
     * Show the form for creating a new class post
     */
    public function create()
    {
        if (!$this->canCreateClassPost()) {
            abort(403, 'Only teachers can create class posts.');
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

        return view('class-posts.create', compact('subjects', 'sections', 'academicYears', 'semesters'));
    }

    /**
     * Store a newly created class post
     */
    public function store(Request $request)
    {
        if (!$this->canCreateClassPost()) {
            abort(403, 'Only teachers can create class posts.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'type' => 'required|in:announcement,resource,discussion,reminder',
            'priority' => 'required|in:low,normal,high,urgent',
            'is_pinned' => 'boolean',
            'allows_comments' => 'boolean',
            'requires_confirmation' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
            'post_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:10240'
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
        $data['is_pinned'] = $request->has('is_pinned');
        $data['allows_comments'] = $request->has('allows_comments');
        $data['requires_confirmation'] = $request->has('requires_confirmation');
        $data['is_active'] = true;
        $data['published_at'] = now();

        // Handle file upload
        if ($request->hasFile('post_file')) {
            $file = $request->file('post_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('class-posts', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        ClassPost::create($data);

        return redirect()->route('class-posts.index')->with('success', 'Class post created successfully.');
    }

    /**
     * Display the specified class post
     */
    public function show(ClassPost $classPost)
    {
        $this->authorizePost($classPost);
        
        $classPost->load(['teacher', 'subject', 'section', 'academicYear', 'semester', 'comments.user']);
        
        return view('class-posts.show', compact('classPost'));
    }

    /**
     * Show the form for editing the specified class post
     */
    public function edit(ClassPost $classPost)
    {
        $this->authorizePost($classPost);
        
        $subjects = Subject::all();
        $sections = Section::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        
        return view('class-posts.edit', compact('classPost', 'subjects', 'sections', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified class post
     */
    public function update(Request $request, ClassPost $classPost)
    {
        $this->authorizePost($classPost);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'type' => 'required|in:announcement,resource,discussion,reminder',
            'priority' => 'required|in:low,normal,high,urgent',
            'is_pinned' => 'boolean',
            'allows_comments' => 'boolean',
            'requires_confirmation' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
            'post_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:10240'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['is_pinned'] = $request->has('is_pinned');
        $data['allows_comments'] = $request->has('allows_comments');
        $data['requires_confirmation'] = $request->has('requires_confirmation');

        // Handle file upload
        if ($request->hasFile('post_file')) {
            // Delete old file if exists
            if ($classPost->file_path) {
                Storage::disk('public')->delete($classPost->file_path);
            }
            
            $file = $request->file('post_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('class-posts', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        $classPost->update($data);

        return redirect()->route('class-posts.show', $classPost)->with('success', 'Class post updated successfully.');
    }

    /**
     * Remove the specified class post
     */
    public function destroy(ClassPost $classPost)
    {
        $this->authorizePost($classPost);

        // Delete associated file
        if ($classPost->file_path) {
            Storage::disk('public')->delete($classPost->file_path);
        }

        $classPost->delete();

        return redirect()->route('class-posts.index')->with('success', 'Class post deleted successfully.');
    }

    /**
     * Toggle pin status
     */
    public function togglePin(ClassPost $classPost)
    {
        $this->authorizePost($classPost);
        
        $isPinned = $classPost->togglePin();
        $status = $isPinned ? 'pinned' : 'unpinned';
        
        return redirect()->back()->with('success', "Class post {$status} successfully.");
    }

    /**
     * Publish post
     */
    public function publish(ClassPost $classPost)
    {
        $this->authorizePost($classPost);
        
        $classPost->publish();
        
        return redirect()->back()->with('success', 'Class post published successfully.');
    }

    /**
     * Unpublish post
     */
    public function unpublish(ClassPost $classPost)
    {
        $this->authorizePost($classPost);
        
        $classPost->unpublish();
        
        return redirect()->back()->with('success', 'Class post unpublished successfully.');
    }

    /**
     * Store a comment
     */
    public function storeComment(Request $request, ClassPost $classPost)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:class_post_comments,id',
            'comment_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:5120'
        ]);

        if (!$classPost->canComment()) {
            return redirect()->back()->with('error', 'Comments are not allowed on this post.');
        }

        $data = [
            'class_post_id' => $classPost->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'is_approved' => true, // Auto-approve for now
            'is_active' => true
        ];

        // Handle file upload in comment
        if ($request->hasFile('comment_file')) {
            $file = $request->file('comment_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('class-post-comments', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        ClassPostComment::create($data);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Delete a comment
     */
    public function deleteComment(ClassPostComment $comment)
    {
        $user = Auth::user();
        
        if (!$comment->canDelete($user)) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    /**
     * Approve/disapprove comment
     */
    public function toggleCommentApproval(ClassPostComment $comment)
    {
        $user = Auth::user();
        
        if (!in_array($user->role_name, ['Admin', 'Teacher'])) {
            abort(403, 'Unauthorized action.');
        }

        $comment->update(['is_approved' => !$comment->is_approved]);
        
        $status = $comment->is_approved ? 'approved' : 'disapproved';
        
        return redirect()->back()->with('success', "Comment {$status} successfully.");
    }

    /**
     * Authorize post access
     */
    private function authorizePost(ClassPost $classPost)
    {
        $user = Auth::user();
        
        if ($user->role_name === 'Admin') {
            return true;
        }
        
        if ($user->role_name === 'Teacher') {
            $teacher = $user->teacher;
            if ($teacher && $teacher->id === $classPost->teacher_id) {
                return true;
            }
        }
        
        abort(403, 'Unauthorized action.');
    }
}
