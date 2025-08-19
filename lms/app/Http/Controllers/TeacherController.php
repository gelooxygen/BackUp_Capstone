<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Teacher;
use Brian2694\Toastr\Facades\Toastr;

class TeacherController extends Controller
{
    /** add teacher page */
    public function teacherAdd()
    {
        $users = User::where('role_name','Teachers')->get();
        return view('teacher.add-teacher',compact('users'));
    }

    /** teacher list */
    public function teacherList()
    {
        $query = Teacher::join('users', 'teachers.user_id','users.user_id')
                    ->select('users.date_of_birth','users.join_date','users.phone_number as user_phone','teachers.*')
                    ->where('users.role_name', 'Teacher'); // Only show teachers with valid Teacher role
        
        // Search/Filter logic
        if ($id = request('search_id')) {
            $query->where('teachers.user_id', 'like', "%$id%");
        }
        if ($name = request('search_name')) {
            $query->where('teachers.full_name', 'like', "%$name%");
        }
        if ($phone = request('search_phone')) {
            $query->where('teachers.phone_number', 'like', "%$phone%");
        }
        
        $listTeacher = $query->get();
        return view('teacher.list-teachers',compact('listTeacher'));
    }

    /** teacher Grid */
    public function teacherGrid()
    {
        $teacherGrid = Teacher::with('user')
            ->whereHas('user', function($query) {
                $query->where('role_name', 'Teacher');
            })
            ->get();
        return view('teacher.teachers-grid',compact('teacherGrid'));
    }

    /** save record */
    public function saveRecord(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string',
            'gender'        => 'required|string',
            'experience'    => 'required|string',
            'date_of_birth' => 'required|string',
            'qualification' => 'required|string',
            'phone_number'  => 'required|string',
            'address'       => 'required|string',
            'city'          => 'required|string',
            'state'         => 'required|string',
            'zip_code'      => 'required|string',
            'country'       => 'required|string',
        ]);

        try {

            $saveRecord = new Teacher;
            $saveRecord->full_name     = $request->full_name;
            $saveRecord->user_id       = $request->user_id;
            $saveRecord->gender        = $request->gender;
            $saveRecord->experience    = $request->experience;
            $saveRecord->qualification = $request->qualification;
            $saveRecord->date_of_birth = $request->date_of_birth;
            $saveRecord->phone_number  = $request->phone_number;
            $saveRecord->address       = $request->address;
            $saveRecord->city          = $request->city;
            $saveRecord->state         = $request->state;
            $saveRecord->zip_code      = $request->zip_code;
            $saveRecord->country       = $request->country;
            $saveRecord->save();
   
            Toastr::success('Has been add successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('fail, Add new record  :)','Error');
            return redirect()->back();
        }
    }

    /** edit record */
    public function editRecord($user_id)
    {
        $teacher = Teacher::join('users', 'teachers.user_id','users.user_id')
                    ->select('users.date_of_birth','users.join_date','users.phone_number','teachers.*')
                    ->where('users.user_id', $user_id)->first();
        return view('teacher.edit-teacher',compact('teacher'));
    }

    /** update record teacher */
    public function updateRecordTeacher(Request $request)
    {
        DB::beginTransaction();
        try {

            $updateRecord = [
                'full_name'     => $request->full_name,
                'gender'        => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'qualification' => $request->qualification,
                'experience'    => $request->experience,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'zip_code'      => $request->zip_code,
                'country'      => $request->country,
            ];
            Teacher::where('id',$request->id)->update($updateRecord);
            
            Toastr::success('Has been update successfully :)','Success');
            DB::commit();
            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            \Log::info($e);
            Toastr::error('fail, update record  :)','Error');
            return redirect()->back();
        }
    }

    /** delete record */
    public function teacherDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            // Find teacher by user_id and delete
            $teacher = Teacher::where('user_id', $request->id)->first();
            if ($teacher) {
                $teacher->delete();
                DB::commit();
                Toastr::success('Deleted record successfully :)','Success');
            } else {
                Toastr::error('Teacher not found :)','Error');
            }
            return redirect()->back();
        } catch(\Exception $e) {
            DB::rollback();
            \Log::info($e);
            Toastr::error('Deleted record fail :)','Error');
            return redirect()->back();
        }
    }

    /** Show form to assign grade levels to a teacher */
    public function assignGradeLevelsForm($id)
    {
        $teacher = Teacher::findOrFail($id);
        $assigned = $teacher->gradeLevels->pluck('grade_level')->toArray();
        // Example grade levels, you may want to fetch from a config or table
        $gradeLevels = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        return view('teacher.assign_grade_levels', compact('teacher', 'gradeLevels', 'assigned'));
    }

    /** Handle assignment of grade levels to a teacher */
    public function assignGradeLevels(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $gradeLevels = $request->input('grade_levels', []);
        // Remove all and re-add
        $teacher->gradeLevels()->delete();
        foreach ($gradeLevels as $level) {
            $teacher->gradeLevels()->create(['grade_level' => $level]);
        }
        return redirect()->route('teacher/list/page')->with('success', 'Grade levels assigned successfully.');
    }

    /** Sync all teacher users with teachers table */
    public function syncTeacherUsers()
    {
        try {
            // Get all users with Teacher role
            $teacherUsers = User::where('role_name', 'Teacher')->get();
            $syncedCount = 0;
            
            foreach ($teacherUsers as $user) {
                // Check if teacher record already exists
                $existingTeacher = Teacher::where('user_id', $user->user_id)->first();
                
                if (!$existingTeacher) {
                    // Create teacher record
                    Teacher::create([
                        'user_id' => $user->user_id,
                        'full_name' => $user->name,
                        'phone_number' => $user->phone_number ?? 'Not specified',
                        'address' => 'Not specified',
                        'city' => 'Not specified',
                        'state' => 'Not specified',
                        'zip_code' => 'Not specified',
                        'country' => 'Not specified',
                        'gender' => 'Not specified',
                        'date_of_birth' => 'Not specified',
                        'qualification' => 'Not specified',
                        'experience' => 'Not specified'
                    ]);
                    $syncedCount++;
                }
            }
            
            Toastr::success("Successfully synced $syncedCount teacher users", 'Success');
            return redirect()->back();
            
        } catch (\Exception $e) {
            Toastr::error('Failed to sync teacher users: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }
}
