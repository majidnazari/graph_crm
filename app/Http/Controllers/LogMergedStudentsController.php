<?php

namespace App\Http\Controllers;

use App\LogMergedStudents;
use App\Http\Requests\StoreLogMergedStudentsRequest;
use App\Http\Requests\UpdateLogMergedStudentsRequest;
use App\LogMergedStudent;
use App\Student;
use Illuminate\Http\Request;
use App\User;

use Log;


class LogMergedStudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merged_students = LogMergedStudent::all()->take(10);
        

        return view('log_merge_students.index', [
            'merged_students' => $merged_students,
            "supporter" =>[],
            "main_student" =>[],
            "merged_student" =>[],
            'msg_success' => request()->session()->get('msg_success'),
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }
    public function indexOfAjax(Request $request)
    {
        $supporter= User::where('is_deleted', false)->where('groups_id', env('USER_ROLE'))->get();
        $supporter= User::where('is_deleted', false)->where('groups_id', env('USER_ROLE'))->get();
        // $main_student 
        //     "main_student" =>[],
        //     "merged_student" =>[],
        $all_logs = LogMergedStudent::where('id','>',0);

        if ($request->input('supporter_id') != 0) {
            //Log::info("add to log");              
            $support_id = $request->input('supporter_id');
            $all_logs = $all_logs->where('user_id_updater', $support_id);
        }
        if ($request->input('main_student_id') != 0) {
            //Log::info("add to log");              
            $main_student_id = $request->input('main_student_id');
            $all_logs = $all_logs->where('current_student_id', $main_student_id);
        }
        if ($request->input('merged_student_id') != 0) {
            //Log::info("add to log");              
            $merged_student_id = $request->input('merged_student_id');
            $all_logs = $all_logs->where('old_student_id', $merged_student_id);
        }


        $count_all_logs=count($all_logs->get());
        $req = request()->all();
        if (!isset($req['start'])) {
            $req['start'] = 0;
            $req['length'] = 10;
            $req['draw'] = 1;
        }

        $all_logs = $all_logs->with('userUpdater')
            ->skip($req['start'])
            ->take($req['length'])
            ->get();

        $users = User::where('is_deleted', false)->where('groups_id', env('USER_ROLE'))->get();

        foreach ($all_logs as $index => $item) {

            $data[] = [
                "row" =>  $req['start'] + $index + 1,
                "id" => $item->id,
                // "supporter" => $item->supporter->first_name . ' ' . $item->supporter->last_name,
                
                "current_student_fullname" => ($item->current_student_fullname),
                "current_student_phone" => ($item->current_student_phone),
                "current_student_phone1" => ($item->current_student_phone1),
                "current_student_phone2" => ($item->current_student_phone2),
                "current_student_phone3" => ($item->current_student_phone3),
                "current_student_phone4" => ($item->current_student_phone4),
                "current_student_student_phone" => ($item->current_student_student_phone),
                "current_student_mother_phone" => ($item->current_student_mother_phone),
                "current_student_father_phone" => ($item->current_student_father_phone),

                "old_student_fullname" => ($item->old_student_fullname),
                "old_student_phone" => ($item->old_student_phone),
                "old_student_phone1" => ($item->old_student_phone1),
                "old_student_phone2" => ($item->old_student_phone2),
                "old_student_phone3" => ($item->old_student_phone3),
                "old_student_phone4" => ($item->old_student_phone4),
                "old_student_student_phone" => ($item->old_student_student_phone),
                "old_student_mother_phone" => ($item->old_student_mother_phone),
                "old_student_father_phone" => ($item->old_student_father_phone),

                "user_fullname_updater" => ($item->user_fullname_updater),

                //"student_fullname" => $item->student_fullname,
                //"updated_at" => jdate($item->updated_at)->format("Y/m/d"),
                //"receipt_date" => ($item->receipt_date != null) ? jdate($item->receipt_date)->format("Y/m/d") : "",                
                //"end" => $btn

            ];
        }

        $result = [
            "draw" => $req['draw'],
            "data" => $data,
            "request" => $request->all(),
            "users" =>$users,
            "recordsTotal" =>  $count_all_logs,
            "recordsFiltered" =>   $count_all_logs,
        ];

        return $result;
    }
    public function AllAJAXMainStudent(Request $request) 
    {
        $data=[];
        //Log::info("ajax is:" . $request['q']);
        $results = Student::select('id','first_name','last_name','phone')->where('first_name', 'like', '%' . $request['q'] . '%')
            ->orWhere('last_name', 'like', '%' . $request['q'] . '%')->get();
            foreach($results as $result){

                $data[]=[
                    "id" => $result->id,
                    "text" => $result->first_name . ' ' .$result->last_name . ' ' .$result->phone,
                ];
            }
        return $data;
    }

    public function AllAJAXMergedStudent(Request $request) 
    {
        $data=[];
        //Log::info("ajax is:" . $request['q']);
        $results = Student::select('id','first_name','last_name','phone')->where('first_name', 'like', '%' . $request['q'] . '%')
            ->orWhere('last_name', 'like', '%' . $request['q'] . '%')->get();
            foreach($results as $result){

                $data[]=[
                    "id" => $result->id,
                    "text" => $result->first_name . ' ' .$result->last_name . ' ' .$result->phone,
                ];
            }
        return $data;
    }
    public function AllAJAXSupporter(Request $request)
    {
        $data=[];
        //Log::info("ajax is:" . $request['q']);
        $results = User::select('id','first_name','last_name')
        ->where('first_name', 'like', '%' . $request['q'] . '%')
        ->orWhere('last_name', 'like', '%' . $request['q'] . '%')
        ->where('is_deleted', false)
        ->where('groups_id', env('USER_ROLE'))
        ->get();
        // ->where('is_deleted', false)->where('groups_id', env('USER_ROLE'))->get();
        // Student::select('id','first_name','last_name','phone')->where('last_name', 'like', '%' . $request['q'] . '%')
        //     ->orWhere('last_name', 'like', '%' . $request['q'] . '%')->get();
            foreach($results as $result){

                $data[]=[
                    "id" => $result->id,
                    "text" => $result->first_name . ' ' .$result->last_name ,
                ];
            }
        return $data;
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLogMergedStudentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLogMergedStudentsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LogMergedStudents  $logMergedStudents
     * @return \Illuminate\Http\Response
     */
    public function show(LogMergedStudents $logMergedStudents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LogMergedStudents  $logMergedStudents
     * @return \Illuminate\Http\Response
     */
    public function edit(LogMergedStudents $logMergedStudents)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLogMergedStudentsRequest  $request
     * @param  \App\LogMergedStudents  $logMergedStudents
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLogMergedStudentsRequest $request, LogMergedStudents $logMergedStudents)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LogMergedStudents  $logMergedStudents
     * @return \Illuminate\Http\Response
     */
    public function destroy(LogMergedStudents $logMergedStudents)
    {
        //
    }
}
