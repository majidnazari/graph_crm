<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Group;
use App\Marketer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\CalendarUtils;


use App\Imports\StudentsImport;
use App\Student;
use App\User;
use App\Source;
use App\StudentClassRoom;
use App\StudentTag;
use App\StudentTemperature;
use App\Tag;
use App\TagParentOne;
use App\TagParentTwo;
use App\TagParentThree;
use App\TagParentFour;
use App\NeedTagParentOne;
use App\NeedTagParentTwo;
use App\NeedTagParentThree;
use App\NeedTagParentFour;
use App\Temperature;
use App\StudentCollection;
use App\ClassRoom;
use App\City;
use App\Http\Traits\ChangeSupporterTrait;
use App\Utils\SearchStudent;
use App\Exports\StudentsExport;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Gate;
use App\MergeStudents as AppMergeStudents;
use App\Purchase;
use App\SupporterHistory;
use Illuminate\Support\Facades\Route;
use Exception;
use Log;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    use ChangeSupporterTrait;
    public function perToEn($inp)
    {
        $inp = str_replace(["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"], ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"], $inp);
        $inp = str_replace(["١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩", "٠"], ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"], $inp);
        return $inp;
    }

    public static function jalaliToGregorian($pdate)
    {
        $pdate = explode('/', SupporterController::persianToEnglishDigits($pdate));
        $date = "";
        if (count($pdate) == 3) {
            $y = (int)$pdate[0];
            $m = (int)$pdate[1];
            $d = (int)$pdate[2];
            if ($d > $y) {
                $tmp = $d;
                $d = $y;
                $y = $tmp;
            }
            $y = (($y < 1000) ? $y + 1300 : $y);
            $gregorian = CalendarUtils::toGregorian($y, $m, $d);
            $gregorian = $gregorian[0] . "-" . $gregorian[1] . "-" . $gregorian[2];
        }
        return $gregorian;
    }
    public function class(Request $request, $id)
    {

        $student = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('id', $id)->with('studentclasses.class')->first();
        if ($student == null) {
            $request->session()->flash("msg_error", "دانش آموز مورد نظر پیدا نشد!");
            return redirect()->route('student_all');
        }

        // dd($student);
        $classes = ClassRoom::where('is_deleted', false)->get();

        if ($request->getMethod() == 'GET') {
            return view('students.class', [
                "student" => $student,
                "classes" => $classes,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        }
    }

    public function classDelete(Request $request, $student_id, $id)
    {
        $student = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('id', $student_id)->with('studentclasses.class')->first();
        if ($student == null) {
            $request->session()->flash("msg_error", "دانش آموز مورد نظر پیدا نشد!");
            return redirect()->route('student_all');
        }

        $studentClass = StudentClassRoom::find($id);
        if ($studentClass == null) {
            $request->session()->flash("msg_error", "کلاس دانش آموز مورد نظر پیدا نشد!");
            return redirect()->route('student_class', ["id" => $student_id]);
        }

        $studentClass->delete();

        $request->session()->flash("msg_success", "کلاس دانش آموز مورد نظر حذف شد!");
        return redirect()->route('student_class', ["id" => $student_id]);
    }


    public function classAdd(Request $request, $student_id)
    {
        $student = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('id', $student_id)->with('studentclasses.class')->first();
        if ($student == null) {
            $request->session()->flash("msg_error", "دانش آموز مورد نظر پیدا نشد!");
            return redirect()->route('student_all');
        }

        $class_rooms_id = $request->input('class_rooms_id');
        $class = ClassRoom::find($class_rooms_id);
        if ($class == null) {
            $request->session()->flash("msg_error", "کلاس مورد نظر پیدا نشد!");
            return redirect()->route('student_class', ["id" => $student_id]);
        }

        $studentClass = StudentClassRoom::where("students_id", $student_id)->where('class_rooms_id', $class_rooms_id)->first();
        if ($studentClass == null) {
            $studentClass = new StudentClassRoom();
            $studentClass->students_id = $student_id;
            $studentClass->class_rooms_id = $class_rooms_id;
            $studentClass->users_id = Auth::user()->id;
            $studentClass->save();
        }

        $request->session()->flash("msg_success", "کلاس دانش آموز مورد نظر افزوده شد!");
        return redirect()->route('student_class', ["id" => $student_id]);
    }
    public function showStudents(Request $request, $level, $view, $route)
    {
        $searchStudent = new SearchStudent;
        $students = Student::where('students.is_deleted', false)->where('students.banned', false)->where('students.archived', false);
        if ($level != "all") {
            $students = $students->where('level', $level);
        }
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $supporters_id = null;
        $name = null;
        $sources_id = null;
        $theLevel = 0;
        $phone = null;
        $cities_id = null;
        $egucation_level = null;
        $concours_year = null;
        $school = null;
        $major = null;
        $tag_id = null;


        $moralTags = Tag::where('is_deleted', false)
            ->where('type', 'moral')
            ->get();
        $needTags = Tag::where('is_deleted', false)
            ->where('type', 'need')
            ->get();
        $parentOnes = TagParentOne::where('is_deleted', false)->has('tags')->get();
        $parentTwos = TagParentTwo::where('is_deleted', false)->has('tags')->get();
        $parentThrees = TagParentThree::where('is_deleted', false)->has('tags')->get();
        $parentFours = TagParentFour::where('is_deleted', false)->has('tags')->get();
        $needTagParentOnes = NeedTagParentOne::where('is_deleted', false)->has('tags')->get();
        $needTagParentTwos = NeedTagParentTwo::where('is_deleted', false)->has('tags')->get();
        $needTagParentThrees = NeedTagParentThree::where('is_deleted', false)->has('tags')->get();
        $needTagParentFours = NeedTagParentFour::where('is_deleted', false)->has('tags')->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();
        $cities = City::where('is_deleted', false)->get();
        $req =  request()->all();
        if (!isset($req['start'])) {
            $req['start'] = 0;
            $req['length'] = 10;
            $req['draw'] = 1;
        }
        $Students = $students
            ->with('user')
            ->with('studenttags.tag.parent_four')
            ->with('studentcollections.collection.parent')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->offset($req['start'])
            ->limit($req['length'])
            ->get();

        foreach ($Students as $index => $student_tmp) {
            $Students[$index]->pcreated_at = jdate(strtotime($student_tmp->created_at))->format("Y/m/d");
            // Log::info("the date is:". $Students[$index]->pcreated_at);
        }

        if (request()->getMethod() == 'GET') {
            //dd($Students);
            return view(
                $view,
                compact([
                    'route',
                    'Students',
                    'supports',
                    'sources',
                    'supporters_id',
                    'name',
                    'sources_id',
                    'phone',
                    'moralTags',
                    'needTags',
                    'hotTemperatures',
                    'coldTemperatures',
                    'parentOnes',
                    'parentTwos',
                    'parentThrees',
                    'parentFours',
                    'firstCollections',
                    'secondCollections',
                    'thirdCollections',
                    'cities',
                    'cities_id',
                    'egucation_level',
                    'concours_year',
                    'major',
                    'needTagParentOnes',
                    'needTagParentTwos',
                    'needTagParentThrees',
                    'needTagParentFours'
                ]),
                [
                    'msg_success' => request()->session()->get('msg_success'),
                    'msg_error' => request()->session()->get('msg_error')
                ]
            );
        } else {

            $students = Student::where('students.is_deleted', false)
                ->where('students.banned', false)
                ->where('students.archived', false);
            if ($level != "all") {
                $students = $students->where('level', $level);
            }
            if (request()->getMethod() == 'POST') {
                // dump(request()->all());           
                if (request()->input('supporters_id') != null) {
                    $supporters_id = (int)request()->input('supporters_id');
                    $students = $students->where('supporters_id', $supporters_id);
                }
                if (request()->input('name') != null) {
                    $name = trim(request()->input('name'));
                    $students = $searchStudent->search($students, $name);
                }
                if (request()->input('level') != null) {
                    $theLevel = request()->input('level');
                    $students = $students->where('level', $theLevel);
                }
                if (request()->input('sources_id') != null) {
                    $sources_id = (int)request()->input('sources_id');
                    $students = $students->where('sources_id', $sources_id);
                }
                if (request()->input('phone') != null) {
                    $phone = (int)request()->input('phone');
                    $students = $students->where('phone', $phone)->orWhere('father_phone', $phone)->orWhere('mother_phone', $phone)->orWhere('student_phone', $phone)
                        ->orWhere('phone1', $phone)->orWhere('phone2', $phone)->orWhere('phone3', $phone)->orWhere('phone4', $phone);
                }
                if (request()->input('cities_id') != null) {
                    $cities_id = (int)request()->input('cities_id');
                    $students = $students->where('cities_id', $cities_id);
                }
                if (request()->input('egucation_level') != null) {
                    $egucation_level = request()->input('egucation_level');
                    $students = $students->where('egucation_level', $egucation_level);
                }
                if (request()->input('concours_year') != null) {
                    $concours_year = request()->input('concours_year');
                    $students = $students->where('concours_year', $concours_year);
                }
                if (request()->input('major') != null) {
                    $major = request()->input('major');
                    $students = $students->where('major', $major);
                }
                if (request()->input('school') != null) {
                    $school = request()->input('school');
                    $students = $students->where('school', 'like',  '%' . $school . '%');
                }
                if (request()->input('nationality_code') != null) {
                    $nationality_code = request()->input('nationality_code');
                    $students = $students->where('nationality_code', 'like',  '%' . $nationality_code . '%');
                }
                if (request()->input('tag_id') != null) {
                    $tag_id = request()->input('tag_id');
                    $students->whereHas('studenttags', function ($q) use ($tag_id) {
                        $q->where('tags_id', $tag_id);
                    });
                }
            }
            $allStudents = $students->count();
            $x = 0;
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc

            // foreach ($allStudents as $index => $item) {
            //     $allStudents[$index]->foo = $index;
            // }
            if ($columnName != 'row' && $columnName != 'end' && $columnName != "temps" && $columnName != "tags") {
                $students = $students->orderBy($columnName, $columnSortOrder)
                    ->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            } else if ($columnName == "tags") {
                $sw = "tags";
                $students = $students
                    ->withCount('studenttags')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->orderBy('studenttags_count', $columnSortOrder)
                    ->get();
            } else {
                $students = $students->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            }

            $data = [];
            foreach ($students as $index => $item) {
                $tags = "";
                if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                    for ($i = 0; $i < count($item->studenttags); $i++) {
                        $tags .= '<span class="d-inline-block px-1 rounded small p-1 mt-2 ' . (($item->studenttags[$i]->tag->type == 'moral') ? 'bg-cyan' : 'bg-warning') . ' p-1">
                        ' . (($item->studenttags[$i]->tag->parent_four) ? $item->studenttags[$i]->tag->parent_four->name . '->' : '') . ' ' . $item->studenttags[$i]->tag->name . '
                    </span><br/>';
                    }
                }
                $registerer = "-";
                $user_editor = "-";
                if ($item->user_editor)
                    $user_editor =  $item->user_editor->first_name . ' ' . $item->user_editor->last_name;
                if ($item->user)
                    $registerer =  $item->user->first_name . ' ' . $item->user->last_name;
                elseif ($item->saloon)
                    $registerer = $item->saloon;
                elseif ($item->is_from_site)
                    $registerer =  'سایت';

                $temps = "";
                if ($item->studenttemperatures && count($item->studenttemperatures) > 0) {
                    foreach ($item->studenttemperatures as $sitem) {
                        if ($sitem->temperature->status == 'hot')
                            $temps .= '<span class="bg-danger d-inline-block px-1 rounded small p-1 mt-2">';
                        else
                            $temps .= '<span class="bg-info d-inline-block px-1 rounded small p-1 mt-2">';
                        $temps .= $sitem->temperature->name . '</span>';
                    }
                }
                $supportersToSelect = "";
                foreach ($supports as $sitem) {
                    $supportersToSelect .= '<option value="' . $sitem->id . '"';
                    if ($sitem->id == $item->supporters_id)
                        $supportersToSelect .= ' selected';
                    $supportersToSelect .= '>' . $sitem->first_name . ' ' . $sitem->last_name . '</option>';
                }
                $levelsToSelect = "";
                $levels = [1, 2, 3, 4];
                foreach ($levels as $l) {
                    $levelsToSelect .= '<option value="' . $l . '"';
                    if ($l == $item->level)
                        $levelsToSelect .= ' selected';
                    $levelsToSelect .= '>' . $l . '</option>';
                }
                if ($route == "student_all") {
                    $data[] = [
                        "row" => $index + 1,
                        "id" => $item->id,
                        "first_name" => $item->first_name,
                        "last_name" => $item->last_name,
                        "nationality_code" => $item->nationality_code,
                        "users_id" => $registerer,
                        "users_id_editor" => $user_editor,
                        "sources_id" => ($item->source) ? $item->source->name : '-',
                        "tags" => $tags,
                        "temps" => $temps,
                        "supporters_id" => '<select id="supporters_id_' . $index . '" class="form-control select2">
                            <option>-</option>
                            ' . $supportersToSelect . '
                            </select>
                            <a class="btn btn-success btn-sm" href="#" onclick="return changeSupporter(' . $index . "," . $item->id . ');">
                                ذخیره
                            </a>
                            <br/>
                            <img id="loading-' . $index . '" src="/dist/img/loading.gif" style="height: 20px;display: none;" />',
                        "level" => '<select id="level_' . $index . '" class="form-control select2">' .
                            $levelsToSelect
                            . '</select>
                         <a class="btn btn-success btn-sm" href="#" onclick="return changeLevel(' . $index . "," . $item->id . ');">
                             ذخیره
                         </a>
                         <br/>
                         <img id="loading-' . $index . '" src="/dist/img/loading.gif" style="height: 20px;display: none;" />',
                        "description" => $item->description,
                        "end" => '<div class="d-flex justify-content-between"><a class="btn btn-warning btn-sm" href="#" onclick="onClickTemperature(' . $item->id . ')">
                            داغ/سرد
                        </a>
                        <a class="btn btn-danger btn-sm mr-1" href="' . route('student_class', ['id' => $item->id]) . '" >
                            تخصیص کلاس
                        </a></div>'
                    ];
                } else {
                    $data[] = [
                        "row" => $index + 1,
                        "id" => $item->id,
                        "first_name" => $item->first_name,
                        "last_name" => $item->last_name,
                        "nationality_code" => $item->nationality_code,
                        "users_id" => $registerer,
                        "users_id_editor" => $user_editor,
                        "sources_id" => ($item->source) ? $item->source->name : '-',
                        "tags" => $tags,
                        "temps" => $temps,
                        "supporters_id" => '<select id="supporters_id_' . $index . '" class="form-control select2">
                            <option>-</option>
                            ' . $supportersToSelect . '
                            </select>
                            <a class="btn btn-success btn-sm" href="#" onclick="return changeSupporter(' . $index . "," . $item->id . ');">
                                ذخیره
                            </a>
                            <br/>
                            <img id="loading-' . $index . '" src="/dist/img/loading.gif" style="height: 20px;display: none;" />',
                        "description" => $item->description,
                        "end" => '<div class="d-flex justify-content-between"><a class="btn btn-warning btn-sm" href="#" onclick="onClickTemperature(' . $item->id . ')">
                            داغ/سرد
                        </a>
                        <a class="btn btn-danger btn-sm mr-1" href="' . route('student_class', ['id' => $item->id]) . '" >
                            تخصیص کلاس
                        </a></div>'
                    ];
                }
            }
            foreach ($students as $index => $student_tmp) {
                $students[$index]->pcreated_at = jdate(strtotime($student_tmp->created_at))->format("Y/m/d");
                // Log::info("the date is:". $Students[$index]->pcreated_at);
            }
            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "theStudents" => $students,
                "recordsTotal" => $allStudents,
                "recordsFiltered" => $allStudents,
            ];

            return $result;
        }
    }

    public function indexAll(Request $request)
    {

        return $this->showStudents($request, "all", "students.index", "student_all");
    }
    public function archived(Request $request)
    {

        $searchStudent = new SearchStudent;
        $students = Student::where('is_deleted', false)->where('archived', true);
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $supporters_id = null;
        $name = null;
        $sources_id = null;
        $phone = null;
        if (request()->getMethod() == 'POST') {
            if (request()->input('supporters_id') != null) {
                $supporters_id = (int)request()->input('supporters_id');
                $students = $students->where('supporters_id', $supporters_id);
            }
            if (request()->input('name') != null) {
                $name = trim(request()->input('name'));
                $students = $searchStudent->search($students, $name);
            }
            if (request()->input('sources_id') != null) {
                $sources_id = (int)request()->input('sources_id');
                $students = $students->where('sources_id', $sources_id);
            }
            if (request()->input('phone') != null) {
                $phone = (int)request()->input('phone');
                $students = $students->where('phone', $phone);
            }
        }
        $theStudents = $students
            ->with('user')
            ->with('studenttags.tag')
            ->with('studentcollections.collection.parent')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->get();
        $count = is_countable($students) ? count($students) : 0;
        $moralTags = Tag::where('is_deleted', false)
            ->where('type', 'moral')
            ->get();
        $parentOnes = TagParentOne::where('is_deleted', false)->has('tags')->get();
        $parentTwos = TagParentTwo::where('is_deleted', false)->has('tags')->get();
        $parentThrees = TagParentThree::where('is_deleted', false)->has('tags')->get();
        $parentFours = TagParentFour::where('is_deleted', false)->has('tags')->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();

        foreach ($theStudents as $index => $student) {
            $theStudents[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }

        if (request()->getMethod() == 'GET') {
            return view('students.archived', [
                'route' => 'student_archived',
                'students' => $theStudents,
                'supports' => $supports,
                'sources' => $sources,
                'supporters_id' => $supporters_id,
                'name' => $name,
                'sources_id' => $sources_id,
                'phone' => $phone,
                'moralTags' => $moralTags,
                'needTags' => $collections,
                'hotTemperatures' => $hotTemperatures,
                'coldTemperatures' => $coldTemperatures,
                "parentOnes" => $parentOnes,
                "parentTwos" => $parentTwos,
                "parentThrees" => $parentThrees,
                "parentFours" => $parentFours,
                "firstCollections" => $firstCollections,
                "secondCollections" => $secondCollections,
                "thirdCollections" => $thirdCollections,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        } else {
            $allStudents = $students->get();
            $req =  request()->all();

            if (!isset($req['start'])) {
                $req['start'] = 0;
                $req['length'] = 10;
                $req['draw'] = 1;
            }
            // $students = $students->with('user')
            // ->with('studenttags.tag')
            // ->with('studentcollections.collection.parent')
            // ->with('studenttemperatures.temperature')
            // ->with('source')
            // ->with('consultant')
            // ->with('supporter')
            // ->orderBy('created_at', 'desc')
            // ->offset($req['start'])
            // ->limit($req['length'])
            // ->get();
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc

            if ($columnName != 'row' && $columnName != "temps" && $columnName != "tags") {
                $students = $students->orderBy($columnName, $columnSortOrder)
                    ->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            } else if ($columnName == "tags") {
                $students = $students
                    ->withCount('studenttags')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->orderBy('studenttags_count', $columnSortOrder)
                    ->get();
            } else {
                $students = $students->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            }
            $data = [];
            foreach ($students as $index => $item) {
                $tags = "";
                if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                    for ($i = 0; $i < count($item->studenttags); $i++) {
                        $tags .= '<span class="bg-cyan d-inline-block px-1 rounded small p-1 mt-2">
                        ' . $item->studenttags[$i]->tag->name . '
                    </span><br/>';
                    }
                    for ($i = 0; $i < count($item->studentcollections); $i++) {
                        $tags .= '<span class="bg-warning d-inline-block px-1 rounded small p-1 mt-2">
                            ' . (($item->studentcollections[$i]->collection->parent) ? $item->studentcollections[$i]->collection->parent->name . '->' : '') . ' ' . $item->studentcollections[$i]->collection->name . '
                        </span><br/>';
                    }
                }
                $user_editor = "-";
                if ($item->user_editor)
                    $user_editor =  $item->user_editor->first_name . ' ' . $item->user_editor->last_name;
                $registerer = "-";
                if ($item->user)
                    $registerer =  $item->user->first_name . ' ' . $item->user->last_name;
                elseif ($item->saloon)
                    $registerer = $item->saloon;
                elseif ($item->is_from_site)
                    $registerer =  'سایت';
                $temps = "";
                if ($item->studenttemperatures && count($item->studenttemperatures) > 0) {
                    foreach ($item->studenttemperatures as $sitem) {
                        if ($sitem->temperature->status == 'hot')
                            $temps .= '<span class="bg-danger d-inline-block px-1 rounded small p-1 mt-2">';
                        else
                            $temps .= '<span class="bg-cyan d-inline-block px-1 rounded small p-1 mt-2">';
                        $temps .= $sitem->temperature->name . '</span>';
                    }
                }
                $supportersToSelect = "";
                foreach ($supports as $sitem) {
                    $supportersToSelect .= '<option value="' . $sitem->id . '"';
                    if ($sitem->id == $item->supporters_id)
                        $supportersToSelect .= ' selected';
                    $supportersToSelect .= '>' . $sitem->first_name . ' ' . $sitem->last_name . '</option>';
                }
                $data[] = [
                    "row" => $req['start'] + $index + 1,
                    "id" => $item->id,
                    "first_name" => $item->first_name,
                    "last_name" => $item->last_name,
                    "users_id" => $registerer,
                    "users_id_editor" => $user_editor,
                    "sources_id" => ($item->source) ? $item->source->name : '-',
                    "tags" => $tags,
                    "temps" => $temps,
                    "description" => $item->description
                ];
            }

            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "recordsTotal" => count($allStudents),
                "recordsFiltered" => count($allStudents)
            ];

            return $result;
        }
    }

    public function banned(Request $request)
    {

        $searchStudent = new SearchStudent;
        $students = Student::where('is_deleted', false)->where('banned', true);
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $supporters_id = null;
        $name = null;
        $sources_id = null;
        $phone = null;
        if (request()->getMethod() == 'POST') {
            // dump(request()->all());
            if (request()->input('supporters_id') != null) {
                $supporters_id = (int)request()->input('supporters_id');
                $students = $students->where('supporters_id', $supporters_id);
            }
            if (request()->input('name') != null) {
                $name = trim(request()->input('name'));
                $students = $searchStudent->search($students, $name);
            }
            if (request()->input('sources_id') != null) {
                $sources_id = (int)request()->input('sources_id');
                $students = $students->where('sources_id', $sources_id);
            }
            if (request()->input('phone') != null) {
                $phone = (int)request()->input('phone');
                $students = $students->where('phone', $phone);
            }
        }
        $allStudents = $students->get();
        $theStudents = $students
            ->with('user')
            ->with('studenttags.tag')
            ->with('studentcollections.collection.parent')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->get();
        $count = is_countable($students) ? count($students) : 0;

        $moralTags = Tag::where('is_deleted', false)
            ->where('type', 'moral')
            ->get();
        $parentOnes = TagParentOne::where('is_deleted', false)->has('tags')->get();
        $parentTwos = TagParentTwo::where('is_deleted', false)->has('tags')->get();
        $parentThrees = TagParentThree::where('is_deleted', false)->has('tags')->get();
        $parentFours = TagParentFour::where('is_deleted', false)->has('tags')->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();

        foreach ($theStudents as $index => $student) {
            $theStudents[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }

        if (request()->getMethod() == "GET") {
            return view('students.banned', [
                'students' => $theStudents,
                'supports' => $supports,
                'sources' => $sources,
                'supporters_id' => $supporters_id,
                'name' => $name,
                'sources_id' => $sources_id,
                'phone' => $phone,
                'moralTags' => $moralTags,
                'needTags' => $collections,
                'hotTemperatures' => $hotTemperatures,
                'coldTemperatures' => $coldTemperatures,
                "parentOnes" => $parentOnes,
                "parentTwos" => $parentTwos,
                "parentThrees" => $parentThrees,
                "parentFours" => $parentFours,
                "firstCollections" => $firstCollections,
                "secondCollections" => $secondCollections,
                "thirdCollections" => $thirdCollections,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        } else {
            $count = $allStudents ? count($allStudents) : 0;
            $req =  request()->all();
            if (!isset($req['start'])) {
                $req['start'] = 0;
                $req['length'] = 10;
                $req['draw'] = 1;
            }
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc

            if ($columnName != 'row' && $columnName != "temps" && $columnName != "tags") {
                $students = $students->orderBy($columnName, $columnSortOrder)
                    ->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            } else if ($columnName == "tags") {
                $students = $students
                    ->withCount('studenttags')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->orderBy('studenttags_count', $columnSortOrder)
                    ->get();
            } else {
                $students = $students->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            }
            $data = [];
            foreach ($students as $index => $item) {
                $tags = "";
                if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                    for ($i = 0; $i < count($item->studenttags); $i++) {
                        $tags .= '<span class="d-inline-block px-1 rounded small mt-2 ' . (($item->studenttags[$i]->tag->type == 'moral') ? 'bg-cyan' : 'bg-warning') . ' p-1">
                        ' . $item->studenttags[$i]->tag->name . '
                    </span><br/>';
                    }
                }
                $registerer = "-";
                $user_editor = "-";
                if ($item->user_editor)
                    $user_editor =  $item->user_editor->first_name . ' ' . $item->user_editor->last_name;
                if ($item->user)
                    $registerer =  $item->user->first_name . ' ' . $item->user->last_name;
                elseif ($item->saloon)
                    $registerer = $item->saloon;
                elseif ($item->is_from_site)
                    $registerer =  'سایت';
                $temps = "";
                if ($item->studenttemperatures && count($item->studenttemperatures) > 0) {
                    foreach ($item->studenttemperatures as $sitem) {
                        if ($sitem->temperature->status == 'hot')
                            $temps .= '<span class="bg-danger d-inline-block px-1 rounded small mt-2 p-1">';
                        else
                            $temps .= '<span class="bg-cyan d-inline-block px-1 rounded small mt-2 p-1">';
                        $temps .= $sitem->temperature->name . '</span>';
                    }
                }
                $data[] = [
                    "row" => $req['start'] + $index + 1,
                    "id" => $item->id,
                    "first_name" => $item->first_name,
                    "last_name" => $item->last_name,
                    "users_id" => $registerer,
                    "users_id_editor" => $user_editor,
                    "sources_id" => ($item->source) ? $item->source->name : '-',
                    "tags" => $tags,
                    "temps" => $temps,
                    "description" => $item->description,
                ];
            }
            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "recordsTotal" => $count,
                "recordsFiltered" => $count
            ];

            return $result;
        }
    }

    public function index()
    {

        $searchStudent = new SearchStudent;
        $students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', 0);
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $supporters_id = null;
        $name = null;
        $sources_id = null;
        $phone = null;
        if (request()->getMethod() == 'POST') {
            // dump(request()->all());
            if (request()->input('supporters_id') != null) {
                $supporters_id = (int)request()->input('supporters_id');
                $students = $students->where('supporters_id', $supporters_id);
            }
            if (request()->input('name') != null) {
                $name = trim(request()->input('name'));
                $students = $searchStudent->search($students, $name);
            }
            if (request()->input('sources_id') != null) {
                $sources_id = (int)request()->input('sources_id');
                $students = $students->where('sources_id', $sources_id);
            }
            if (request()->input('phone') != null) {
                $phone = (int)request()->input('phone');
                $students = $students->where('phone', $phone);
            }
            if (request()->input('cities_id') != null) {
                $cities_id = (int)request()->input('cities_id');
                $students = $students->where('cities_id', $cities_id);
            }
            if (request()->input('egucation_level') != null) {
                $egucation_level = request()->input('egucation_level');
                $students = $students->where('egucation_level', $egucation_level);
            }
            if (request()->input('concours_year') != null) {
                $concours_year = request()->input('concours_year');
                $students = $students->where('concours_year', $concours_year);
            }
            if (request()->input('major') != null) {
                $major = request()->input('major');
                $students = $students->where('major', $major);
            }
            if (request()->input('school') != null) {
                $school = request()->input('school');
                $students = $students->where('school', 'like',  '%' . $school . '%');
            }
            if (request()->input('national_code') != null) {
                $national_code = request()->input('national_code');
                $students = $students->where('nationality_code', 'like',  '%' . $national_code . '%');
            }
        }
        $moralTags = Tag::where('is_deleted', false)
            ->where('type', 'moral')
            ->get();
        $needTags = Tag::where('is_deleted', false)
            ->where('type', 'need')
            ->get();
        $parentOnes = TagParentOne::where('is_deleted', false)->has('tags')->get();
        $parentTwos = TagParentTwo::where('is_deleted', false)->has('tags')->get();
        $parentThrees = TagParentThree::where('is_deleted', false)->has('tags')->get();
        $parentFours = TagParentFour::where('is_deleted', false)->has('tags')->get();
        $needTagParentOnes = NeedTagParentOne::where('is_deleted', false)->has('tags')->get();
        $needTagParentTwos = NeedTagParentTwo::where('is_deleted', false)->has('tags')->get();
        $needTagParentThrees = NeedTagParentThree::where('is_deleted', false)->has('tags')->get();
        $needTagParentFours = NeedTagParentFour::where('is_deleted', false)->has('tags')->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();
        $cities = City::where('is_deleted', false)->get();
        $theStudents = $students
            ->with('user')
            ->with('studenttags.tag.parent_four')
            ->with('studentcollections.collection.parent')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->get();
        foreach ($theStudents as $index => $student) {
            $theStudents[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }

        if (request()->getMethod() == 'GET') {
            return view('students.student-input-and-division', [
                'students' => $theStudents,
                'supports' => $supports,
                'sources' => $sources,
                'supporters_id' => $supporters_id,
                'name' => $name,
                'sources_id' => $sources_id,
                'phone' => $phone,
                'moralTags' => $moralTags,
                'needTags' => $needTags,
                'hotTemperatures' => $hotTemperatures,
                'coldTemperatures' => $coldTemperatures,
                "parentOnes" => $parentOnes,
                "parentTwos" => $parentTwos,
                "parentThrees" => $parentThrees,
                "parentFours" => $parentFours,
                "firstCollections" => $firstCollections,
                "secondCollections" => $secondCollections,
                "thirdCollections" => $thirdCollections,
                "needTagParentOnes" => $needTagParentOnes,
                "needTagParentTwos" => $needTagParentTwos,
                "needTagParentThrees" => $needTagParentThrees,
                "needTagParentFours" => $needTagParentFours,
                "cities" => $cities,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        } else {
            $allStudents = $students->get();
            $req =  request()->all();
            if (!isset($req['start'])) {
                $req['start'] = 0;
                $req['length'] = 10;
                $req['draw'] = 1;
            }
            $columnIndex_arr = request()->get('order');
            $columnName_arr = request()->get('columns');
            $order_arr = request()->get('order');
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc

            if ($columnName != 'row' && $columnName != 'end' && $columnName != "temps" && $columnName != "tags") {
                $students = $students->orderBy($columnName, $columnSortOrder)
                    ->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            } else if ($columnName == "tags") {
                $students = $students
                    ->withCount('studenttags')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->orderBy('studenttags_count', $columnSortOrder)
                    ->get();
            } else {
                $students = $students->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->with('user')
                    ->with('studenttags.tag.parent_four')
                    ->with('studentcollections.collection.parent')
                    ->with('studenttemperatures.temperature')
                    ->with('source')
                    ->with('consultant')
                    ->with('supporter')
                    ->get();
            }
            $data = [];
            foreach ($students as $index => $item) {
                $tags = "";
                if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                    for ($i = 0; $i < count($item->studenttags); $i++) {
                        $tags .= '<span class="d-inline-block px-1 rounded small mt-2 ' . (($item->studenttags[$i]->tag->type == 'moral') ? 'bg-cyan' : 'bg-warning') . ' p-1">
                        ' . $item->studenttags[$i]->tag->name . '
                    </span><br/>';
                    }
                }
                $registerer = "-";
                $user_editor = "-";
                if ($item->user_editor) {
                    $user_editor = $item->user_editor->first_name . ' ' . $item->user_editor->last_name;
                }
                if ($item->user)
                    $registerer =  $item->user->first_name . ' ' . $item->user->last_name;
                elseif ($item->saloon)
                    $registerer = $item->saloon;
                elseif ($item->is_from_site)
                    $registerer =  'سایت';
                $temps = "";
                if ($item->studenttemperatures && count($item->studenttemperatures) > 0) {
                    foreach ($item->studenttemperatures as $sitem) {
                        if ($sitem->temperature->status == 'hot')
                            $temps .= '<span class="bg-danger d-inline-block px-1 rounded small mt-2 p-1">';
                        else
                            $temps .= '<span class="bg-cyan d-inline-block px-1 rounded small mt-2 p-1">';
                        $temps .= $sitem->temperature->name . '</span>';
                    }
                }
                $supportersToSelect = "";
                foreach ($supports as $sitem) {
                    $supportersToSelect .= '<option value="' . $sitem->id . '"';
                    if ($sitem->id == $item->supporters_id)
                        $supportersToSelect .= ' selected';
                    $supportersToSelect .= '>' . $sitem->first_name . ' ' . $sitem->last_name . '</option>';
                }
                $data[] = [
                    "row" => $index + 1,
                    "id" => $item->id,
                    "first_name" => $item->first_name,
                    "last_name" => $item->last_name,
                    "users_id" => $registerer,
                    "users_id_editor" => $user_editor,
                    "sources_id" => ($item->source) ? $item->source->name : '-',
                    "tags" => $tags,
                    "temps" => $temps,
                    "supporters_id" => '<select id="supporters_id_' . $index . '" class="form-control select2">
                        <option>-</option>
                        ' . $supportersToSelect . '
                        </select>
                        <a class="btn btn-success btn-sm" href="#" onclick="return changeSupporter(' . $index . "," . $item->id . ');">
                            ذخیره
                        </a>
                        <br/>
                        <img id="loading-' . $index . '" src="/dist/img/loading.gif" style="height: 20px;display: none;" />',
                    "description" => $item->description,
                    "end" => '<div class="d-flex justify-content-between"><a class="btn btn-warning btn-sm" href="#" onclick="onClickTemperature(' . $item->id . ')">
                        داغ/سرد
                    </a></div>'
                ];
            }
            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "recordsTotal" => count($allStudents),
                "recordsFiltered" => count($allStudents)
            ];

            return $result;
        }
    }

    public function create(Request $request)
    {
        $student = new Student();
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $consultantGroupId = Group::getConsultant();
        if ($consultantGroupId)
            $consultantGroupId = $consultantGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $consultants = User::where('is_deleted', false)->where('groups_id', $consultantGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $cities = City::where('is_deleted', false)->get();
        if ($request->getMethod() == 'GET') {
            return view('students.create', [
                "supports" => $supports,
                "consultants" => $consultants,
                "sources" => $sources,
                "cities" => $cities,
                "student" => $student,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        }
        $all_phones = array_filter(array_unique(array(
            trim($request->input('phone')),
            trim($request->input('phone1')),
            trim($request->input('phone2')),
            trim($request->input('phone3')),
            trim($request->input('phone4')),
            trim($request->input('student_phone')),
            trim($request->input('mother_phone')),
            trim($request->input('father_phone'))

        )));
        foreach ($all_phones as $one_phone) {
            $is_exist = Student::where('is_deleted', 0)
                ->where(function ($query) use ($one_phone) {
                    $query->where('phone', $one_phone)
                        ->orWhere('father_phone', $one_phone)
                        ->orWhere('mother_phone', $one_phone)
                        ->orWhere('phone1', $one_phone)
                        ->orWhere('phone2', $one_phone)
                        ->orWhere('phone3', $one_phone)
                        ->orWhere('phone4', $one_phone)
                        ->orWhere('student_phone', $one_phone);
                })
                // ->where(function($query) 
                // {
                //     $query->where('is_deleted', 0);
                // })
                ->first();
            if ($is_exist) {
                $request->session()->flash("msg_error", " شماره تکراریست و مربوط به دانش آموز یا ولی  " . $is_exist->first_name . " " . $is_exist->last_name);
                return redirect()->route('student_create');
            }
        }

        // $is_exist=Student::where(function($query) use($request,$all_phones)
        // {             
        //     $query->where('phone',$request->input('phone'))
        //     ->orWhere('father_phone',$request->input('phone'))
        //     ->orWhere('mother_phone',$request->input('phone'));
        // })->where(function($query) 
        // {
        //     $query->where('is_deleted', 0);
        // })->first();

        // $is_exist=Student::where(function($query) use($request)
        // {

        //     // $query->where('phone',$request->input('phone'))
        //     // ->orWhere('father_phone',$request->input('phone'))
        //     // ->orWhere('mother_phone',$request->input('phone'));
        // })->where(function($query) 
        // {
        //     $query->where('is_deleted', 0);
        // })->first();
        if ($is_exist) {
            $request->session()->flash("msg_error", " شماره تکراریست و مربوط به دانش آموز یا ولی  " . $is_exist->first_name . " " . $is_exist->last_name);
            return redirect()->route('student_create');
        }
        $is_national_code_exist = $this->IsNationalCodeExist($request->input('national_no'));
        if ($is_national_code_exist) {

            $request->session()->flash("msg_error", " کد ملی تکراریست و مربوط به دانش آموز    " . $is_national_code_exist->first_name . " " . $is_national_code_exist->last_name);
            return redirect()->route('student_create');
        }

        $student->users_id = Auth::user()->id;
        $student->first_name = $request->input('first_name');
        $student->last_name = $request->input('last_name');
        $student->nationality_code = $request->input('national_no');
        $student->concours_year = $request->input('concours_year');
        $student->last_year_grade = (int)$request->input('last_year_grade');
        $student->consultants_id = $request->input('consultants_id');
        $student->parents_job_title = $request->input('parents_job_title');
        $student->home_phone = $request->input('home_phone');
        $student->egucation_level = $request->input('egucation_level');
        $student->father_phone = $request->input('father_phone');
        $student->mother_phone = $request->input('mother_phone');
        $student->phone  = $request->input('phone');
        $student->phone1  = $request->input('phone1');
        $student->phone2  = $request->input('phone2');
        $student->phone3  = $request->input('phone3');
        $student->phone4  = $request->input('phone4');
        $student->school = $request->input('school');
        $student->average = $request->input('average');
        $student->major = $request->input('major');
        $student->introducing = $request->input('introducing');
        $student->student_phone = $request->input('student_phone');
        $student->sources_id = $request->has('sources_id') ? $request->input('sources_id') : 0;
        $student->supporters_id = $request->input('supporters_id');
        $student->cities_id = $request->input('cities_id');
        $student->outside_consultants = $request->input('outside_consultants');
        $student->description = $request->input('description');
        try {
            $student->save();
        } catch (Exception $e) {
            // dd($e);
            if ($e->getCode() == 23000)
                $request->session()->flash("msg_error", "شماره دانش آموز تکراری است");
            else
                $request->session()->flash("msg_error", "خطا در ثبت دانش آموز");

            return redirect()->route('student_create');
        }
        $request->session()->flash("msg_success", "دانش آموز با موفقیت افزوده شد.");
        return redirect()->route('students');
    }

    public function edit(Request $request, $call_back, $id)
    {
        $i = 1;
        $student = Student::where('is_deleted', false)->where('id', $id)->first();
        $old_sources_id=$student->sources_id;

        if ($student == null) {
            $request->session()->flash("msg_error", "دانش آموز مورد نظر پیدا نشد!");
            return redirect()->route('students');
        }

        if (($student->level == 3 and $request->input('level') == 2) || ($student->level == 2 and $request->input('level') == 1)) {
            $request->session()->flash("msg_error", "تغییر سطح دانش آموز امکان پذیر نیست");
            return redirect()->route($call_back);
        }

        $all_phones = array_filter(array_unique(array(
            trim($request->input('phone')),
            trim($request->input('phone1')),
            trim($request->input('phone2')),
            trim($request->input('phone3')),
            trim($request->input('phone4')),
            trim($request->input('student_phone')),
            trim($request->input('mother_phone')),
            trim($request->input('father_phone'))

        )));
        foreach ($all_phones as $one_phone) {
            $is_exist = Student::where('is_deleted', 0)
                ->where(function ($query) use ($one_phone) {
                    $query->where('phone', $one_phone)
                        ->orWhere('father_phone', $one_phone)
                        ->orWhere('mother_phone', $one_phone)
                        ->orWhere('phone1', $one_phone)
                        ->orWhere('phone2', $one_phone)
                        ->orWhere('phone3', $one_phone)
                        ->orWhere('phone4', $one_phone)
                        ->orWhere('student_phone', $one_phone);
                })
                ->where('id', '!=', $id)
                ->first();
            if ($is_exist) {
                $request->session()->flash("msg_error", " شماره تکراریست و مربوط به دانش آموز یا ولی  " . $is_exist->first_name . " " . $is_exist->last_name);
                return redirect()->route($call_back);
            }
        }

        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $consultantGroupId = Group::getConsultant();
        if ($consultantGroupId)
            $consultantGroupId = $consultantGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $consultants = User::where('is_deleted', false)->where('groups_id', $consultantGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $cities = City::where('is_deleted', false)->get();
        if ($request->getMethod() == 'GET') {
            return view('students.create', [
                "supports" => $supports,
                "consultants" => $consultants,
                "sources" => $sources,
                "cities" => $cities,
                "student" => $student,
                'i' => $i,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        }

        $is_national_code_exist = $this->IsNationalCodeExist($request->input('national_no'), $id);
        if ($is_national_code_exist) {

            $request->session()->flash("msg_error", " کد ملی تکراریست و مربوط به دانش آموز    " . $is_national_code_exist->first_name . " " . $is_national_code_exist->last_name);
            return redirect()->route($call_back);
        }


        $student->users_id_editor = Auth::user()->id;
        $student->first_name = $request->input('first_name');
        $student->last_name = $request->input('last_name');
        $student->nationality_code = $request->input('national_no');
        $student->concours_year = $request->input('concours_year');
        $student->last_year_grade = (int)$request->input('last_year_grade');
        $student->consultants_id = $request->input('consultants_id');
        $student->parents_job_title = $request->input('parents_job_title');
        $student->home_phone = $request->input('home_phone');
        $student->egucation_level = $request->input('egucation_level');
        $student->father_phone = $request->input('father_phone');
        $student->mother_phone = $request->input('mother_phone');
        $student->phone  = $request->input('phone');
        $student->phone1  = $request->input('phone1');
        $student->phone2  = $request->input('phone2');
        $student->phone3  = $request->input('phone3');
        $student->phone4  = $request->input('phone4');
        $student->school = $request->input('school');
        $student->average = $request->input('average');
        $student->major = $request->input('major');
        $student->introducing = $request->input('introducing');
        $student->student_phone = $request->input('student_phone');
        //$student->sources_id = $request->has('sources_id') ? $request->input('sources_id') : 0;
        if(($request->input('sources_id')!=0) && $old_sources_id === 0){
           $student->sources_id = $request->input('sources_id') ;//? $request->input('sources_id') : 0;
        }
        
        $student->cities_id = $request->input('cities_id');
        if ($student->supporters_id != $request->input('supporters_id') && $student->supporter_seen) {
            $student->supporter_seen = false;
        }
        if (Gate::allows('parameters')) {
            $student->supporters_id = $request->input('supporters_id');
        }
        if (!Gate::allows('supervisor') && Gate::allows('parameters')) {
            $student->level = $request->input('level');
        }
        $student->banned = ($request->input('banned') != null) ? true : false;
        $student->archived = ($request->input('archived') != null) ? true : false;
        $student->outside_consultants = $request->input('outside_consultants');
        //Log::info("the group id edit is: " . Auth::user()->groups_id);
        if ($request->has('description')) {
            $student->description = $request->input('description');
        }
        if ($request->input('description_exists') and Auth::user()->groups_id != 2) {
            $student->description = $request->input('description_exists');
        }
        //dd($old_sources_id);

        try {
            if ($student->banned || $student->archived) {
                $student->supporters_id = 0;
            }

            $student->save();
        } catch (Exception $e) {
            // dd($e);
            if ($e->getCode() == 23000)
                $request->session()->flash("msg_error", "شماره دانش آموز تکراری است");
            else
                $request->session()->flash("msg_error", "خطا در ثبت دانش آموز");

            return redirect()->route($call_back);
        }

        $request->session()->flash("msg_success", "دانش آموز با موفقیت بروز شد.");
        return redirect()->route($call_back);
    }

    public function delete(Request $request, $id)
    {
        $student = Student::where('is_deleted', false)->where('id', $id)->first();
        if ($student == null) {
            $request->session()->flash("msg_error", "دانش آموز مورد نظر پیدا نشد!");
            return redirect()->route('students');
        }
        $student->delete();
        //$student->is_deleted = true;
        //$student->save();

        $request->session()->flash("msg_success", "دانش آموز با موفقیت حذف شد.");
        return redirect()->route('students');
    }
    public function education_level_null_for_csv($educationLevels, $educationLevelsInPersian, $level)
    {
        if (!isset($educationLevels[$level]) && isset($educationLevelsInPersian[$level])) {
            $educationLevel = $educationLevelsInPersian[$level];
        } else if (!isset($educationLevels[$level]) && !isset($educationLevelsInPersian[$level])) {
            $educationLevel = null;
        }
        return $educationLevel;
    }
    public function outputCsv(Request $request)
    {
        //return Excel::download(new UsersExport,'users-data.xlsx');       
        // $spreadsheet = new Spreadsheet();
        // $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A1', 'Hello World sssss !');

        // $writer = new Xlsx($spreadsheet);
        // $writer->save('hello world1.xlsx');
        // return Excel::download(new UsersExport, 'users.xlsx');

        // dd("ff");
        $from_date = null;
        $to_date = null;
        $concours_year = null;
        $majors = [
            "mathematics" => "ریاضی",
            "experimental" => "تجربی",
            "humanities" => "انسانی",
            "art" => "هنر",
            "other" => "دیگر"
        ];
        $education_levels = [
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9",
            "10" => "10",
            "11" => "11",
            "12" => "12",
            "13" => "فارغ التحصیل",
            "14" => "دانشجو",
            null => ""
        ];
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        if ($request->getMethod() == 'POST') {
            $studentsExport = new StudentsExport($request->input('students_select'), (int)$request->input('supporters_id'), $request->input('major'), $request->input('egucation_level'), $request->input('concours_year'), $request->input('from_date'), $request->input('to_date'));
            if (!count($studentsExport->collection())) {
                $request->session()->flash("msg_error", "دانش آموزی با این شرایط پیدا نشد!");
                return redirect()->back();
            }
            return Excel::download($studentsExport, 'students.xlsx');
        }
        return view('students.output-csv')->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'majors' => $majors,
            'egucation_levels' => $education_levels,
            'concours_year' => $concours_year,
            'supports' => $supports,
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }

    public function csv(Request $request)
    {
        $msg = null;
        $fails = [];
        $majors = [
            'ریاضی' => 'mathematics',
            'تجربی' => 'experimental',
            'انسانی' => 'humanities',
            'هنر' => 'art',
            'غیره' => 'other'
        ];
        $mainMajors = [
            'mathematics' => 'mathematics',
            'experimental' => 'experimental',
            'humanities' => 'humanities',
            'art' => 'art',
            'other' => 'other'
        ];
        $educationLevels = ['6', '7', '8', '9', '10', '11', '12', '13', '14'];
        $educationLevelsInPersian = [
            'ششم' => '6',
            'شش' => '6',
            'هفتم' => '7',
            'هفت' => '7',
            'هشتم' => '8',
            'هشت' => '8',
            'نهم' => '9',
            'نه' => '9',
            'دهم' => '10',
            'ده' => '10',
            'یازدهم' => '11',
            'یازده' => '11',
            'دوازدهم' => '12',
            'دوازده' => '12',
            'فارغ التحصیل' => '13',
            'دانشجو' => '14'
        ];
        $sources = Source::where('is_deleted', false)->get();
        if ($request->getMethod() == 'POST') {
            $msg = 'بروز رسانی با موفقیت انجام شد';
            $csvPath = $request->file('attachment')->getPathname();
            $sources_id = $request->input('sources_id');
            $concours_year = $request->input('concours_year');
            if ($request->file('attachment')->extension() == 'xlsx') {
                $importer = new StudentsImport;
                $importer->source_id = $sources_id;
                $importer->concours_year = $concours_year;
                $res = $importer->import($csvPath, null, \Maatwebsite\Excel\Excel::XLSX);
                $fails = $importer->getFails();
                //dd($fails);
                return view('students.csv', [
                    'msg_success' => $msg,
                    'fails' => $fails,
                    'sources' => $sources
                ]);
            }
            $csv = explode("\n", file_get_contents($csvPath));

            foreach ($csv as $index => $line) {
                $line = explode(',', $line);
                if ($index > 0 && count($line) >= 13) {
                    $student = new Student;
                    $student->users_id_editor = Auth::user()->id;
                    $student->phone = ((strpos($this->perToEn($line[0]), '0') !== 0) ? '0' : '') . $this->perToEn($line[0]);
                    $student->first_name = $line[1] == "NULL" ? null : $line[1];
                    $student->last_name = $line[2];
                    $student->egucation_level = $this->education_level_null_for_csv($educationLevels, $educationLevelsInPersian, $line[3]);

                    $student->parents_job_title = $line[4] == "NULL" ? null : $line[4];
                    $student->home_phone = $line[5] == "NULL" ? null : $line[5];
                    $student->father_phone = $line[6] == "NULL" ? null : $line[6];
                    $student->mother_phone = $line[7] == "NULL" ? null : $line[7];
                    $student->school = $line[8] == "NULL" ? null : $line[8];
                    $student->average = ($line[9] == "NULL" || $line[9] == "") ? null : str_replace('/', '.', $line[9]);
                    $student->major = null;
                    if (isset($majors[$line[10]])) {
                        $student->major = $majors[$line[10]];
                    } else if (isset($mainMajors[$line[10]])) {
                        $student->major = $line[10];
                    }
                    $student->introducing = $line[11] == "NULL" ? null : $line[11];
                    $student->student_phone = $line[12] == "NULL" ? null : $line[12];
                    $student->sources_id = $sources_id;
                    if (count($line) == 17) {
                        if ($line[14] != "NULL" && $line[14] != "" && (int)$line[14] > 0) {
                            $student->sources_id = (int)$line[14];
                        }
                        if ($line[15] != "NULL" && $line[15] != "" && (int)$line[15] > 0) {
                            $student->supporters_id = (int)$line[15];
                        }
                    }
                    if (isset($line[16])) {
                        $line[16] = trim($line[16]);
                        if ($line[16] != "NULL" && $line[16] != "") {
                            $student->description = $line[16];
                        }
                    }
                    try {
                        $student->save();
                    } catch (Exception $e) {
                        $fails[] = $line[0];
                    }
                }
            }
        }
        return view('students.csv', [
            'msg_success' => $msg,
            'fails' => $fails,
            'sources' => $sources
        ]);
    }

    public function thirnaryOperatorsForpurchases($item)
    {
        $first = $item ? $item->first_name : '';
        $second = $item ? $item->last_name : '';
        $third = $item ? ('[' . $item->phone . ']') : '';
        return $first . ' ' . $second . ' ' . $third;
    }
    public function relatedPersons($one, $two, $three, $boolParam)
    {
        $class = $boolParam ? 'text-success' : '';
        $output = '<p class="text-info">افراد مرتبط</p>
    <ul class="list_style_type_none">
        <li class=' . "$class" . '>' .
            $this->thirnaryOperatorsForpurchases($one) .
            '</li>
        <li>' .
            $this->thirnaryOperatorsForpurchases($two) .
            '</li>
        <li>' .
            $this->thirnaryOperatorsForpurchases($three) .
            '</li>
    </ul>';

        return $output;
    }
    public function purchases(Request $request, $id)
    {

        $appMergeStudent = AppMergeStudents::where('is_deleted', false)->where('main_students_id', $id)->orWhere('auxilary_students_id', $id)
            ->orWhere('second_auxilary_students_id', $id)
            ->orWhere('third_auxilary_students_id', $id)
            ->first();
        $main = AppMergeStudents::where('is_deleted', false)->where('main_students_id', $id)->first();
        $auxilary = AppMergeStudents::where('is_deleted', false)->where('auxilary_students_id', $id)->first();
        $secondAuxilary = AppMergeStudents::where('is_deleted', false)->where('second_auxilary_students_id', $id)->first();
        $thirdAuxilary = AppMergeStudents::where('is_deleted', false)->where('third_auxilary_students_id', $id)->first();
        $relatedToMain = '';
        $relatedToAuxilary = '';
        $relatedToSecondAuxilary = '';
        $relatedToThirdAuxilary = '';
        $mainTitleOfPurchasePage = '';
        $auxilaryTitleOfPurchasePage = '';
        $secondAuxilaryTitleOfPurchasePage = '';
        $thirdAuxilaryTitleOfPurchasePage = '';
        if ($main) {
            $relatedToMain = $this->relatedPersons($main->auxilaryStudent, $main->secondAuxilaryStudent, $main->thirdAuxilaryStudent, false);
            $mainTitleOfPurchasePage = $this->thirnaryOperatorsForpurchases($main->mainStudent);
        }
        if ($auxilary) {
            $relatedToAuxilary = $this->relatedPersons($auxilary->mainStudent, $auxilary->secondAuxilaryStudent, $auxilary->thirdAuxilaryStudent, true);
            $auxilaryTitleOfPurchasePage = $this->thirnaryOperatorsForpurchases($auxilary->auxilaryStudent);
        }
        if ($secondAuxilary) {
            $relatedToSecondAuxilary = $this->relatedPersons($secondAuxilary->mainStudent, $secondAuxilary->auxilaryStudent, $secondAuxilary->thirdAuxilaryStudent, true);
            $secondAuxilaryTitleOfPurchasePage = $this->thirnaryOperatorsForpurchases($secondAuxilary->secondAuxilaryStudent);
        }
        if ($thirdAuxilary) {
            $relatedToThirdAuxilary = $this->relatedPersons($thirdAuxilary->mainStudent, $thirdAuxilary->auxilaryStudent, $thirdAuxilary->secondAuxilaryStudent, true);
            $thirdAuxilaryTitleOfPurchasePage = $this->thirnaryOperatorsForpurchases($thirdAuxilary->thirdAuxilaryStudent);
        }
        $student = null;
        if ($appMergeStudent) {
            $purchases = Purchase::where('is_deleted', false)->whereIn('students_id', [
                $appMergeStudent->main_students_id,
                $appMergeStudent->auxilary_students_id,
                $appMergeStudent->second_auxilary_students_id,
                $appMergeStudent->third_auxilary_students_id
            ])->get();
        } else {
            $student = Student::where('is_deleted', false)->where('banned', false)->where('id', $id)->first();
            if ($student) {
                $purchases = $student->purchases()->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->get();
            } else if ($student == null) {
                $request->session()->flash("msg_error", "دانش آموز پیدا نشد!");
                return redirect()->route('students');
            }
        }
        return view('students.purchase', [
            'student' => $student,
            'purchases' => $purchases,
            'appMergeStudent' => $appMergeStudent,
            'main' => $main,
            'auxilary' => $auxilary,
            'secondAuxilary' => $secondAuxilary,
            'thirdAuxilary' => $thirdAuxilary,
            'relatedToMain' => $relatedToMain,
            'relatedToAuxilary' => $relatedToAuxilary,
            'relatedToSecondAuxilary' => $relatedToSecondAuxilary,
            'relatedToThirdAuxilary' => $relatedToThirdAuxilary,
            'mainTitleOfPurchasePage' => $mainTitleOfPurchasePage,
            'auxilaryTitleOfPurchasePage' => $auxilaryTitleOfPurchasePage,
            'secondAuxilaryTitleOfPurchasePage' => $secondAuxilaryTitleOfPurchasePage,
            'thirdAuxilaryTitleOfPurchasePage' => $thirdAuxilaryTitleOfPurchasePage
        ]);
    }
    //---------------------AJAX-----------------------------------
    public function tag(Request $request)
    {
        $students_id = $request->input('students_id');
        $selectedTags = $request->input('selectedTags');
        $selectedCollections = $request->input('selectedColllections');

        $student = Student::where('id', $students_id)->where('banned', false)->where('is_deleted', false)->first();
        if ($student == null) {
            return [
                "error" => "student_not_found",
                "data" => null
            ];
        }

        StudentTag::where("students_id", $students_id)->update([
            "is_deleted" => true
        ]);

        StudentCollection::where("students_id", $students_id)->update([
            "is_deleted" => true
        ]);

        if ($selectedTags) {
            foreach ($selectedTags as $theselectedTag) {
                $studentTag = new StudentTag;
                $studentTag->students_id = $students_id;
                $studentTag->tags_id = $theselectedTag;
                $studentTag->users_id = Auth::user()->id;
                $studentTag->save();
            }
        }

        if ($selectedCollections) {
            // foreach($selectedCollections as $theselectedCollection) {
            //     $studentCollection = new StudentCollection();
            //     $studentCollection->students_id = $students_id;
            //     $studentCollection->collections_id = $theselectedCollection;
            //     $studentCollection->users_id = Auth::user()->id;
            //     $studentCollection->save();
            // }
            foreach ($selectedCollections as $theselectedTag) {
                $studentTag = new StudentTag;
                $studentTag->students_id = $students_id;
                $studentTag->tags_id = $theselectedTag;
                $studentTag->users_id = Auth::user()->id;
                $studentTag->save();
            }
        }

        return [
            "error" => null,
            "data" => null
        ];
    }

    public function temperature(Request $request)
    {
        $students_id = $request->input('students_id');
        $selectedTemperatures = $request->input('selectedTemperatures');

        $student = Student::where('id', $students_id)->where('banned', false)->where('is_deleted', false)->first();
        if ($student == null) {
            return [
                "error" => "student_not_found",
                "data" => null
            ];
        }

        StudentTemperature::where("students_id", $students_id)->update([
            "is_deleted" => true
        ]);

        if ($selectedTemperatures) {
            foreach ($selectedTemperatures as $theselectedTemperature) {
                $studentTemperature = new StudentTemperature;
                $studentTemperature->students_id = $students_id;
                $studentTemperature->temperatures_id = $theselectedTemperature;
                $studentTemperature->users_id = Auth::user()->id;
                $studentTemperature->save();
            }
        }


        return [
            "error" => null,
            "data" => null
        ];
    }
    public function supporter(Request $request)
    {
        $students_id = $request->input('students_id');
        $supporters_id = $request->input('supporters_id');
        $mergeStudent = AppMergeStudents::where('main_students_id', $students_id)->where('is_deleted', false)->first();
        $auxilaryStudent = AppMergeStudents::where('auxilary_students_id', $students_id)->where('is_deleted', false)->first();
        $secondAuxilaryStudent = AppMergeStudents::where('second_auxilary_students_id', $students_id)->where('is_deleted', false)->first();
        $thirdAuxilaryStudent = AppMergeStudents::where('third_auxilary_students_id', $students_id)->where('is_deleted', false)->first();
        if ($mergeStudent != null) {
            $auxilaryStu = $this->getStudent($mergeStudent->auxilary_students_id);
            $secondAuxilaryStu = $this->getStudent($mergeStudent->second_auxilary_students_id);
            $thirdAuxilaryStu = $this->getStudent($mergeStudent->third_auxilary_students_id);
            $mergeStudent = Student::where('id', $mergeStudent->main_students_id)->first();
            $this->giveStudentThatItsSupporterChanged($mergeStudent, $supporters_id);
            $this->giveStudentThatItsSupporterChanged($auxilaryStu, $supporters_id);
            $this->giveStudentThatItsSupporterChanged($secondAuxilaryStu, $supporters_id);
            $this->giveStudentThatItsSupporterChanged($thirdAuxilaryStu, $supporters_id);
            return [
                "error" => null,
                "data" => null
            ];
        } else if ($mergeStudent == null && ($auxilaryStudent != null || $secondAuxilaryStudent != null || $thirdAuxilaryStudent != null)) {
            return [
                "error" => "ابتدا باید پشتیبان فرد اصلی را تغییر دهید!",
                "data" => null
            ];
        }
        $student = Student::where('id', $students_id)->where('banned', false)->where('is_deleted', false)->first();
        if ($student == null) {
            return [
                "error" => "دانش آموز پیدا نشد!",
                "data" => null
            ];
        }
        $this->giveStudentThatItsSupporterChanged($student, $supporters_id);
        return [
            "error" => null,
            "data" => null
        ];
    }

    //---------------------API------------------------------------
    // public function apiAddStudents(Request $request) //old
    // {
    //     $students = $request->input('students', []);
    //     $ids = [];
    //     $fails = [];
    //     foreach ($students as $student) {
    //         if (!isset($student['phone'])) {
    //             $student['error'] = "No Phone";
    //             $fails[] = $student;
    //             continue;
    //         }
    //         $studentObject = Student::where('phone', $student['phone'])->first();

    //         if ($studentObject/* && isset($student['marketers_id']) && $studentObject->marketers_id<=0*/) {
    //             // $marketer = Marketer::where('users_id', $student['marketers_id'])->first();
    //             // if($marketer){
    //             //     $studentObject->marketers_id = $student['marketers_id'];
    //             //     $studentObject->save();
    //             //     $ids[] = $studentObject->id;
    //             // }else{
    //             // $fails[] = $student;
    //             $ids[] = $studentObject->phone;
    //             // }
    //         } else {
    //             $studentObject = new Student;
    //             foreach ($student as $key => $value) {
    //                 $studentObject->$key = $value;
    //             }
    //             $studentObject->is_from_site = true;
    //             try {
    //                 $studentObject->save();
    //                 $ids[] = $studentObject->phone;
    //             } catch (Exception $e) {
    //                 $student['error'] = $e->getMessage();
    //                 $fails[] = $student;
    //             }
    //         }
    //     }
    //     return [
    //         "added_ids" => $ids,
    //         "fails" => $fails
    //     ];
    // }

    public function apiAddStudents(Request $request) // new added 4 phones to student
    {
        $students = $request->input('students', []);
        // Log::info(json_encode($students));
        $ids = [];
        $fails = [];
        //$student_tmp = Student::where('is_deleted', 0);
        foreach ($students as $student) {
            $student_tmp = Student::where('is_deleted', 0);
            //Log::info("the studentds is:".$student['phone']);
            if (!isset($student['phone'])) {
                $student['error'] = "No Phone";
                $fails[] = $student;
                continue;
            }
            // $studentObject = Student::where('phone', $student['phone'])->first();

            $is_exist = $student_tmp->where('phone', $student['phone'])
                ->orWhere('father_phone', $student['phone'])
                ->orWhere('mother_phone', $student['phone'])
                ->orWhere('phone1', $student['phone'])
                ->orWhere('phone2', $student['phone'])
                ->orWhere('phone3', $student['phone'])
                ->orWhere('phone4', $student['phone'])
                ->orWhere('student_phone', $student['phone'])
                ->first();
            //Log::info(json_encode($is_exist));
            if ($is_exist) {
                $ids[] = $is_exist->phone;
                //$ids[] =$is_exist->id;
            } else {
                $studentObject = new Student;
                foreach ($student as $key => $value) {
                    $studentObject->$key = $value;
                }
                $studentObject->is_from_site = true;
                try {
                    $studentObject->save();
                    $ids[] = $studentObject->phone;
                    //$ids[] = $studentObject->id;
                } catch (Exception $e) {
                    $student['error'] = $e->getMessage();
                    $fails[] = $student;
                }
            }
        }
        return [
            "added_ids" => $ids,
            "fails" => $fails
        ];
    }

    public function apiUpdateStudents(Request $request)
    {
        $students = $request->input('students', []);
        $ids = [];
        $fails = [];
        foreach ($students as $student) {
            if (!isset($student['phone'])) {
                $student['error'] = "No Phone";
                $fails[] = $student;
                continue;
            }
            $studentObject = Student::where('phone', $student['phone'])
                ->orWhere('father_phone', $student['phone'])
                ->orWhere('mother_phone', $student['phone'])
                ->orWhere('phone1', $student['phone'])
                ->orWhere('phone2', $student['phone'])
                ->orWhere('phone3', $student['phone'])
                ->orWhere('phone4', $student['phone'])
                ->orWhere('student_phone', $student['phone'])
                ->where('banned', false)
                ->first();
            if ($studentObject == null) {
                $studentObject = new Student;
            }
            if (isset($student['marketers_id'])) {
                $marketer = Marketer::where('users_id', $student['marketers_id'])->first();
                if ($marketer) {
                    $studentObject->marketers_id = $student['marketers_id'];
                }
            }

            foreach ($student as $key => $value) {
                if ($key != 'marketers_id')
                    $studentObject->$key = $value;
            }
            $studentObject->is_from_site = true;
            try {
                $studentObject->save();
                $ids[] = $studentObject->phone;
            } catch (Exception $e) {
                // dump($e);
                $student['error'] = $e->getMessage();
                $fails[] = $student;
            }
        }
        return [
            "added_ids" => $ids,
            "fails" => $fails
        ];
    }

    public function apiFilterStudents()
    {
        $req =  request()->all();
        $students = Student::where('is_deleted', false)->where('banned', false);
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $sources = Source::where('is_deleted', false)->get();
        $supporters_id = null;
        $name = null;
        $sources_id = null;
        $phone = null;

        if (request()->input('supporters_id') != null) {
            $supporters_id = (int)request()->input('supporters_id');
            $students = $students->where('supporters_id', $supporters_id);
        }
        if (request()->input('name') != null) {
            $name = trim(request()->input('name'));
            $students = $students->where(function ($query) use ($name) {
                $query->where('first_name', 'like', '%' . $name . '%')->orWhere('last_name', 'like', '%' . $name . '%');
            });
        }
        if (request()->input('sources_id') != null) {
            $sources_id = (int)request()->input('sources_id');
            $students = $students->where('sources_id', $sources_id);
        }
        if (request()->input('phone') != null) {
            $phone = (int)request()->input('phone');
            $students = $students->where('phone', $phone);
        }

        $students = $students
            ->with('user')
            ->with('studenttags.tag')
            ->with('studentcollections.collection.parent')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->orderBy('created_at', 'desc')
            ->get();
        // dd(DB::getQueryLog());
        $moralTags = Tag::where('is_deleted', false)
            // ->with('parent_one')
            // ->with('parent_two')
            // ->with('parent_three')
            // ->with('parent_four')
            ->where('type', 'moral')
            ->get();
        $parentOnes = TagParentOne::where('is_deleted', false)->has('tags')->get();
        $parentTwos = TagParentTwo::where('is_deleted', false)->has('tags')->get();
        $parentThrees = TagParentThree::where('is_deleted', false)->has('tags')->get();
        $parentFours = TagParentFour::where('is_deleted', false)->has('tags')->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();

        foreach ($students as $index => $student) {
            $students[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }

        $data = [];
        foreach ($students as $index => $item) {
            $tags = "";
            if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                for ($i = 0; $i < count($item->studenttags); $i++) {
                    $tags .= '<span class="alert alert-info p-1">
                    ' . $item->studenttags[$i]->tag->name . '
                </span><br/>';
                }
                for ($i = 0; $i < count($item->studentcollections); $i++) {
                    $tags .= '<span class="alert alert-warning p-1">
                        ' . (($item->studentcollections[$i]->collection->parent) ? $item->studentcollections[$i]->collection->parent->name . '->' : '') . ' ' . $item->studentcollections[$i]->collection->name . '
                    </span><br/>';
                }
            }
            $registerer = "-";
            if ($item->user)
                $registerer =  $item->user->first_name . ' ' . $item->user->last_name;
            elseif ($item->is_from_site)
                $registerer =  'سایت';
            elseif ($item->saloon)
                $registerer = $item->saloon;
            $temps = "";
            if ($item->studenttemperatures && count($item->studenttemperatures) > 0) {
                foreach ($item->studenttemperatures as $sitem) {
                    if ($sitem->temperature->status == 'hot')
                        $temps .= '<span class="alert alert-danger p-1">';
                    else
                        $temps .= '<span class="alert alert-info p-1">';
                    $temps .= $sitem->temperature->name . '</span>';
                }
            }
            $supportersToSelect = "";
            foreach ($supports as $sitem) {
                $supportersToSelect .= '<option value="' . $sitem->id . '"';
                if ($sitem->id == $item->supporters_id)
                    $supportersToSelect .= ' selected';
                $supportersToSelect .= '>' . $sitem->first_name . ' ' . $sitem->last_name . '</option>';
            }
            $data[] = [
                $index + 1,
                $item->id,
                $item->first_name,
                $item->last_name,
                $registerer,
                ($item->source) ? $item->source->name : '-',
                $tags,
                $temps,
                '<select id="supporters_id_' . $index . '" class="form-control select2">
                    <option>-</option>
                    ' . $supportersToSelect . '
                    </select>
                    <a class="btn btn-success btn-sm" href="#" onclick="return changeSupporter(' . $index . "," . $item->id . ');">
                        ذخیره
                    </a>
                    <br/>
                    <img id="loading-' . $index . '" src="/dist/img/loading.gif" style="height: 20px;display: none;" />',
                $item->description,
                '<a class="btn btn-warning btn-sm" href="#" onclick="onClickTemperature(' . $item->id . ')">
                    داغ/سرد
                </a>'
            ];
        }


        $outdata = [];
        for ($i = $req['start']; $i < min($req['length'] + $req['start'], count($data)); $i++) {
            $outdata[] = $data[$i];
        }

        $result = [
            "draw" => $req['draw'],
            "data" => $outdata,
            "recordsTotal" => count($students),
            "recordsFiltered" => count($students)
        ];

        return $result;
    }
    public function merge()
    {
        return view('students.merge');
    }


    public function apiIndexStudents(Request $requests)
    {
        $req = $requests->all();

        $query = Student::query();
        foreach ($req as $key => $value) {
            if (isset($value))
                $query = $query->where($key, 'like', "%$value%");
        }
        $result = $query->where('is_deleted', 0)
            // ->where('first_name', 'like', '%majid%')
            ->orderBy('id', 'desc')
            ->get();

        // $result=$result->paginate(10);

        //Log::info($result);
        //->get();
        // $students=Student::where('is_deleted',0)
        // ->where('first_name', 'like', '%' . $requests->first_name . '%')
        // ->orderBy('id','desc')
        // ->get();//->paginate(env('MAX_PAGINATION_ACADEMY'));
        return  $result;
    }
    public function apiShowStudent($id)
    {
        $student = Student::where('id', $id)->where('is_deleted', 0)->orderBy('id', 'desc')->first(); //->paginate(env('MAX_PAGINATION_ACADEMY'));
        return $student;
    }
    public function apiStoreStudents(Request $request)
    {
        //return($request->all());
        // $student=new Student;
        // $student->phone = $request->phone;    
        // $student->first_name = $request->first_name;
        // $student->last_name = $request->last_name;
        // $student->level = $request->level;
        // $student->egucation_level = $request->egucation_level;    
        // $student->parents_job_title = $request->parents_job_title;    
        // $student->home_phone = $request->home_phone;    
        // $student->father_phone = $request->father_phone;    
        // $student->mother_phone = $request->mother_phone;    
        // $student->major = $request->major;    
        // $student->description = $request->description;    
        // $student->school = $request->school;    
        //$student->average = $request->average;    
        //$student->introducing = $request->introducing;
        // $student->student_phone = $request->student_phone;
        // $student->cities_id = $request->cities_id;
        //$student->sources_id = $request->sources_id;
        //$student->supporters_id = $request->supporters_id;
        //$student->archived = $request->archived;

        //return Student::create($student);
        //return "kkkk";//$request->role;
        // if($request->role==="admin"){

        // }
        // $is_exist=Student::where($request->all())->first();
        // if($is_exist){

        // }
        return Student::create($request->all());
        // if($student)
        // {
        //     return $student;
        // }
        // return  $student;       

    }
    public function apiUpdateAcademyStudents(Request $request, int $id)
    {
        $selection = Student::where('id', $id)->where('is_deleted', 0)->first();

        if (!$selection) {
            return $selection;
        }

        // $student=new Student;
        // $student->phone = $request->phone;    
        // $student->first_name = $request->first_name;
        // $student->last_name = $request->last_name;
        // $student->level = $request->level;
        // $student->egucation_level = $request->egucation_level;    
        // $student->parents_job_title = $request->parents_job_title;    
        // $student->home_phone = $request->home_phone;    
        // $student->father_phone = $request->father_phone;    
        // $student->mother_phone = $request->mother_phone;    
        // $student->school = $request->school;    
        // $student->average = $request->average;    
        // $student->introducing = $request->introducing;
        // $student->student_phone = $request->student_phone;
        // $student->cities_id = $request->cities_id;
        // $student->sources_id = $request->sources_id;
        // $student->supporters_id = $request->supporters_id;
        // $student->archived = $request->archived;

        $student_result = $selection->fill($request->all());
        $selection->save();
        return  $student_result;
        //$student=Student::create([$request]);
        // if($student_result=$student->update())
        // {
        //     return $student;
        // }
        // return   $student;       

    }
    public function apiDestroyStudent(int $id)
    {

        $selection = Student::where('id', $id)->where('is_deleted', 0)->first();

        if (!$selection) {
            return $selection;
        }
        // return $selection;
        $selection->is_deleted = 1;
        //return $student;         
        //$student=Student::create([$request]);
        $selection->save();
        return $selection;
        if ($student_result = $selection->save()) {
            return true;
        }
        return  false;
    }
    public function IsNationalCodeExist($national_code, $id = null)
    {
        if ($national_code != "") {
            return  Student::where('is_deleted', 0)
                ->where('archived', 0)
                ->where('banned', 0)
                ->where('nationality_code', $national_code)
                ->where('id', ($id != null) ? '!=' : '>', ($id != null) ? $id : 0)
                ->first();
        }
        return false;
        //Log::info("th eid nation code is:" . $id);

    }
}
