<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Call;
use App\Student;
use Illuminate\Support\Facades\DB;

use Exception;

class ReminderController extends Controller
{

    public function index($date="null"){
        $recalls = Call::where('calls_id', '!=', null)->pluck('calls_id');
        $calls = Call::where('next_call', '!=', null)->where('users_id', Auth::user()->id)->whereNotIn('id', $recalls)->where('is_deleted',false);
        $today = false;
        if($date == "today"){
            $today = true;
            $calls = $calls->where('next_call', '>=', date("Y-m-d 00:00:00"));
        }
        $calls = $calls->with('student')->with('product')->orderBy('next_call', 'desc')->get();
        if(request()->getMethod() == "GET"){
            return view('reminders.index',[
                'calls' => $calls,
                'date' => $date,
                'today' => $today,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        }
    }
    public function indexPost($date=null){

        $student_ids = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', Auth::user()->id)->pluck('id');
        $recalls = Call::where('calls_id', '!=', null)->pluck('calls_id');
        $calls = Call::where('next_call', '!=', null)->where('next_call', '>=', date("Y-m-d 00:00:00"))->where('users_id', Auth::user()->id)->whereNotIn('id', $recalls)->where('is_deleted',false);
        $theCalls = $calls->whereIn('students_id',$student_ids)->with('student')->with('product');
        $allCalls = $theCalls->count();
        $sw = null;
        $count = 0;
        $persons = [
            "student"=>"دانش آموز",
            "father"=>"پدر",
            "mother"=>"مادر",
            "other"=>"غیره"
        ];
        $req =  request()->all();
        if (!isset($req['start'])) {
            $req['start'] = 0;
            $req['length'] = 10;
            $req['draw'] = 1;
        }       
        if(request()->input('date') == "all") {
            $theCalls = Call::where('next_call', '!=', null)->where('users_id', Auth::user()->id)->whereNotIn('id', $recalls)->where('is_deleted',false);
            $allCalls = $theCalls->count();
        }
        $columnIndex_arr = $req['order'];
        $columnName_arr = $req['columns'];
        $order_arr = $req['order'];
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc

        if ($columnName != 'row' && $columnName != 'end') {
            $sw = "all";
            $theCalls = $theCalls->orderBy($columnName, $columnSortOrder)
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        } else {
            $sw = "other";
            $theCalls = $theCalls
                ->skip($req['start'])
                ->take($req['length'])
                ->orderBy('next_call', 'desc')
                ->get();
        }

        $data = [];
        $del_btn="";
        if($theCalls){
            foreach ($theCalls as $index => $item) {
                $route_of_reminder_delete = route('reminder_delete',['id' => $item->id]);
                $route_of_supporters_students = route('supporter_students');
                if( auth()->user()->group->type!=='support')
                {
                    $del_btn="<a class='btn btn-danger' href='$route_of_reminder_delete ' onclick='destroy(event)'>
                                        حذف 
                                          </a>";
                }
                $data[] = [
                    "row" => $index + 1,
                    "id" => $item->id,
                    "students_id" => ($item->student)?$item->student->first_name . ' ' . $item->student->last_name:'-',
                    "products_id" => ($item->product)?(($item->product->parents!='')?$item->product->parents . '->':'') . $item->product->name:'-',
                    "replier" => $persons[$item->replier],
                    "call_results_id" => ($item->callresult)?$item->callresult->title:'-',
                    "next_call" => ($item->next_call)?jdate($item->next_call)->format("Y/m/d"):'-' ,
                    "next_to_call" => ($item->next_to_call)?$persons[$item->next_to_call]:'-',
                    "description" => $item->description,
                    "end" => $del_btn . 
                            "<form method='get' action='$route_of_supporters_students' >
                            <input type='hidden' name='students_id' value='$item->students_id' />
                            <input type='hidden' name='calls_id' value='$item->id' />
                            <button class='btn btn-primary'>
                               تماس
                            </button>
                           </form>"
                ];
            }
        }
        if($sw == "all" || $sw == "other"){
            $count = $allCalls;
        }


        $result = [
            "draw" => $req['draw'],
            "data" => $data,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
        ];

        return $result;

    }

    public function delete($id)
    {
        $call = Call::find($id);
        if($call==null) {
            request()->session()->flash("msg_error", "تماس مورد نظر پیدا نشد!");
            return redirect()->route('reminders_get');
        }
        $call->is_deleted = 1;
        try{
            $call->save();
            request()->session()->flash("msg_success", "تماس با موفقیت حذف شد.");
            return redirect()->route('reminders_get');
        }catch(Exception $e){
            dd($e);
        }
    }
}
