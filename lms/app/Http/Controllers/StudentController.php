<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class StudentController extends Controller
{
    /** index page student list */
    public function student()
    {
        $query = Student::query();
        if (request()->has('archived') && request('archived') == 1) {
            $query = $query->onlyTrashed();
            $showingArchived = true;
        } else {
            $showingArchived = false;
        }
        // Search/Filter logic
        if ($id = request('search_id')) {
            $query->where('id', 'like', "%$id%");
        }
        if ($name = request('search_name')) {
            $query->where(function($q) use ($name) {
                $q->where('first_name', 'like', "%$name%")
                  ->orWhere('last_name', 'like', "%$name%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$name%"]);
            });
        }
        if ($phone = request('search_phone')) {
            $query->where('phone_number', 'like', "%$phone%");
        }
        if ($class = request('search_class')) {
            $query->where('class', 'like', "%$class%");
        }
        if ($year = request('search_year_level')) {
            $query->where('year_level', 'like', "%$year%");
        }
        $studentList = $query->get();
        return view('student.student',compact('studentList', 'showingArchived'));
    }

    /** index page student grid */
    public function studentGrid()
    {
        $query = Student::query();
        if (request()->has('archived') && request('archived') == 1) {
            $query = $query->onlyTrashed();
            $showingArchived = true;
        } else {
            $showingArchived = false;
        }
        // Search/Filter logic
        if ($id = request('search_id')) {
            $query->where('id', 'like', "%$id%");
        }
        if ($name = request('search_name')) {
            $query->where(function($q) use ($name) {
                $q->where('first_name', 'like', "%$name%")
                  ->orWhere('last_name', 'like', "%$name%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$name%"]);
            });
        }
        if ($phone = request('search_phone')) {
            $query->where('phone_number', 'like', "%$phone%");
        }
        if ($class = request('search_class')) {
            $query->where('class', 'like', "%$class%");
        }
        if ($year = request('search_year_level')) {
            $query->where('year_level', 'like', "%$year%");
        }
        $studentList = $query->get();
        return view('student.student-grid',compact('studentList', 'showingArchived'));
    }

    /** student add page */
    public function studentAdd()
    {
        return view('student.add-student');
    }
    
    /** student save record */
    public function studentSave(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'gender'        => 'required|not_in:0',
            'date_of_birth' => 'required|string',
            'roll'          => 'required|string',
            'blood_group'   => 'required|string',
            'religion'      => 'required|string',
            'email'         => 'required|email',
            'class'         => 'required|string',
            'section'       => 'required|string',
            'admission_id'  => 'required|string',
            'phone_number'  => 'required',
            'upload'        => 'required|image',
        ]);
        
        DB::beginTransaction();
        try {
           
            $upload_file = rand() . '.' . $request->upload->extension();
            $request->upload->move(storage_path('app/public/student-photos/'), $upload_file);
            if(!empty($request->upload)) {
                $student = new Student;
                $student->first_name   = $request->first_name;
                $student->last_name    = $request->last_name;
                $student->gender       = $request->gender;
                $student->date_of_birth= $request->date_of_birth;
                $student->roll         = $request->roll;
                $student->blood_group  = $request->blood_group;
                $student->religion     = $request->religion;
                $student->email        = $request->email;
                $student->class        = $request->class;
                $student->section      = $request->section;
                $student->admission_id = $request->admission_id;
                $student->phone_number = $request->phone_number;
                $student->upload = $upload_file;
                $student->save();

                Toastr::success('Has been add successfully :)','Success');
                DB::commit();
            }

            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('fail, Add new student  :)','Error');
            return redirect()->back();
        }
    }

    /** view for edit student */
    public function studentEdit($id)
    {
        $studentEdit = Student::where('id',$id)->first();
        return view('student.edit-student',compact('studentEdit'));
    }

    /** update record */
    public function studentUpdate(Request $request)
    {
        DB::beginTransaction();
        try {

            if (!empty($request->upload)) {
                unlink(storage_path('app/public/student-photos/'.$request->image_hidden));
                $upload_file = rand() . '.' . $request->upload->extension();
                $request->upload->move(storage_path('app/public/student-photos/'), $upload_file);
            } else {
                $upload_file = $request->image_hidden;
            }
           
            $updateRecord = [
                'upload' => $upload_file,
            ];
            Student::where('id',$request->id)->update($updateRecord);
            
            Toastr::success('Has been update successfully :)','Success');
            DB::commit();
            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('fail, update student  :)','Error');
            return redirect()->back();
        }
    }

    /** student delete */
    public function studentDelete(Request $request)
    {
        DB::beginTransaction();
        try {
           
            if (!empty($request->id)) {
                Student::destroy($request->id);
                unlink(storage_path('app/public/student-photos/'.$request->avatar));
                DB::commit();
                Toastr::success('Student deleted successfully :)','Success');
                return redirect()->back();
            }
    
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Student deleted fail :)','Error');
            return redirect()->back();
        }
    }

    /** Restore archived student */
    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();
        \Brian2694\Toastr\Facades\Toastr::success('Student restored successfully :)','Success');
        return redirect()->back();
    }

    /** student profile page */
    public function studentProfile($id)
    {
        $student = Student::findOrFail($id);
        return view('student.student-profile', compact('student'));
    }

    /** student my classes page */
    public function myClasses()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        
        // Get student's enrollments with related data
        $enrollments = $student->enrollments()
            ->with(['subject', 'academicYear', 'semester'])
            ->where('status', 'active')
            ->get();
        
        return view('student.my-classes', compact('student', 'enrollments'));
    }

    /** student class detail page */
    public function classDetail($enrollmentId)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        
        // Get the specific enrollment with all related data
        $enrollment = $student->enrollments()
            ->with(['subject', 'academicYear', 'semester'])
            ->where('id', $enrollmentId)
            ->where('status', 'active')
            ->first();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Class not found or access denied.');
        }
        
        // Get the active tab from request
        $activeTab = request('tab', 'assignments');
        
        return view('student.class-detail', compact('student', 'enrollment', 'activeTab'));
    }

    /** student grades page */
    public function grades()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        
        // Get student's grades with related data
        $grades = $student->grades()
            ->with(['subject', 'teacher', 'component', 'academicYear', 'semester'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get GPA records
        $gpaRecords = $student->gpaRecords()
            ->with(['academicYear', 'semester'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get grade alerts
        $gradeAlerts = $student->gradeAlerts()
            ->with(['subject'])
            ->where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.grades', compact('student', 'grades', 'gpaRecords', 'gradeAlerts'));
    }

    /** student attendance page */
    public function attendance()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        
        // Get student's subjects
        $subjects = $student->subjects;
        
        // Get attendance records for current month
        $month = request('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        
        $query = \App\Models\Attendance::where('student_id', $student->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->with(['subject', 'teacher']);
        
        if ($subjectId = request('subject_id')) {
            $query->where('subject_id', $subjectId);
        }
        
        $attendances = $query->orderBy('date', 'desc')->get();
        
        // Calculate summary
        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $total - $present;
        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
        
        $summary = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $percentage,
        ];
        
        return view('student.attendance', compact('student', 'subjects', 'attendances', 'summary'));
    }
}
