<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Morilog\Jalali\CalendarUtils;

use App\Group;
use App\Student;
use App\SupporterHistory;
use App\User;
use App\Source;
use App\Call;
use App\Product;
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
use App\CallResult;
use App\StudentCollection;
use App\Collection;
use App\Notice;
use App\Purchase;
use App\StudentTag;
use App\City;
use App\Commission;
use App\Utils\CommissionPurchaseRelation;
use App\Http\Traits\AllTypeCallsTrait;
use App\MergeStudents as AppMergeStudents;
use App\SaleSuggestion;
use App\Sanad;
use App\Utils\SearchStudent;
use Exception;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SupporterController extends Controller
{
    use AllTypeCallsTrait;
    public function allMissedCalls()
    {
        $persons = [
            "student" => "دانش آموز",
            "father" => "پدر",
            "mother" => "مادر",
            "other" => "غیره"
        ];
        $all_missed_calls = $this->missed_calls()['value'];
        return view('supporters.allMissedCalls')->with([
            'all_missed_calls' => $all_missed_calls,
            'persons' => $persons
        ]);
    }
    public function yesterdayMissedCalls()
    {
        $persons = [
            "student" => "دانش آموز",
            "father" => "پدر",
            "mother" => "مادر",
            "other" => "غیره"
        ];
        $missed_calls_of_yesterday = $this->yesterday_missed_calls()['value'];
        return view('supporters.yesterdayMissedCalls')->with([
            'yesterday_missed_calls' => $missed_calls_of_yesterday,
            'persons' => $persons
        ]);
    }
    public function noNeedStudents(Request $request)
    {
        $call_results_no_need = CallResult::where('no_call', 1)->where('is_deleted', false)->get();
        $no_need_calls = [];
        $no_need_calls_students = [];
        $no_need_calls_students_builder = "";
        $count = 0;
        $arrOfNoNeed = [];
        if ($call_results_no_need) {
            foreach ($call_results_no_need as $result) {
                $arrOfNoNeed[] = $result->id;
                $no_need_calls = Call::where('is_deleted', false)->where('users_id', Auth::user()->id)->whereIn('call_results_id', $arrOfNoNeed)->pluck('students_id')->implode(',');
            }
        }
        if (!is_array($no_need_calls)) {
            $no_need_calls = array_unique(explode(',', $no_need_calls));
            $no_need_calls_students_builder =  Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->whereIn('id', $no_need_calls);
            $no_need_calls_students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->whereIn('id', $no_need_calls)->get();
            $count = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->whereIn('id', $no_need_calls)->count();
        }
        if ($request->getMethod() == "GET") {
            return view('supporters.noNeedStudents');
        } else {
            $data = [];
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
            if ($no_need_calls_students_builder != "") {
                if ($columnName != 'row' && $columnName != "temps" && $columnName != "tags") {
                    $students = $no_need_calls_students_builder->orderBy($columnName, $columnSortOrder)
                        ->select('students.*')
                        ->skip($req['start'])
                        ->take($req['length'])
                        ->get();
                } else if ($columnName == "tags") {
                    $students = $no_need_calls_students_builder
                        ->withCount('studenttags')
                        ->skip($req['start'])
                        ->take($req['length'])
                        ->orderBy('studenttags_count', $columnSortOrder)
                        ->get();
                } else {
                    $students = $no_need_calls_students_builder->select('students.*')
                        ->skip($req['start'])
                        ->take($req['length'])
                        ->get();
                }
            } else {
                $students = null;
            }
            if ($students) {
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
                    $data[] = [
                        "row" => $index + 1,
                        "id" => $item->id,
                        "first_name" => $item->first_name,
                        "last_name" => $item->last_name,
                        "users_id" => $registerer,
                        "sources_id" => ($item->source) ? $item->source->name : '-',
                        "tags" => $tags,
                        "temps" => $temps,
                        "description" => $item->description,
                    ];
                }
            }



            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
            ];

            return $result;
        }
    }


    public static function persianToEnglishDigits($pnumber)
    {
        $number = str_replace('۰', '0', $pnumber);
        $number = str_replace('۱', '1', $number);
        $number = str_replace('۲', '2', $number);
        $number = str_replace('۳', '3', $number);
        $number = str_replace('۴', '4', $number);
        $number = str_replace('۵', '5', $number);
        $number = str_replace('۶', '6', $number);
        $number = str_replace('۷', '7', $number);
        $number = str_replace('۸', '8', $number);
        $number = str_replace('۹', '9', $number);
        return $number;
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
    public function index()
    {
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supporters = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->with('students.purchases')->with('students.studenttags.tag')->orderBy('max_student', 'desc')->get();
        return view('supporters.index', [
            'supporters' => $supporters,
            'msg_success' => request()->session()->get('msg_success'),
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }

    public function supporterCallIndex()
    {
        $theSupporters_id = Auth::user()->id;
        return $this->callIndex($theSupporters_id);
    }

    public function callIndex($theSupporters_id = null)
    {
        $supporters_id = null;
        $supportersForSelectInView = null;
        if ($theSupporters_id == null) {
            $supportGroupId = Group::getSupport();
            if ($supportGroupId)
                $supportGroupId = $supportGroupId->id;
            $supporters_id = null;
            $supporters = User::where('is_deleted', false)->where('groups_id', $supportGroupId);
            $supportersForSelectInView = $supporters->get();
            $supporters = $supporters->with('students.purchases')->with('students.studenttags.tag')->orderBy('max_student', 'desc')->get();
        } else {
            $supporters = User::where('id', $theSupporters_id)->get();
            $supportersForSelectInView = User::where('is_deleted', false)->get();
        }
        $persons = [
            "student" => "دانش آموز",
            "father" => "پدر",
            "mother" => "مادر",
            "other" => "غیره"
        ];
        $callResults = CallResult::where('is_deleted', false)->get();
        $from_date = null;
        $to_date = null;
        $products_id = null;
        $notices_id = null;
        $replier_id = null;
        $sources_id = null;
        $products = Product::where('is_deleted', false)->where('is_private', false)->with('collection')->orderBy('name')->get();
        foreach ($products as $index => $product) {
            $products[$index]->parents = "-";
            if ($product->collection) {
                $parents = $product->collection->parents();
                $name = ($parents != '') ? $parents . "->" . $product->collection->name : $product->collection->name;
                $products[$index]->parents = $name;
            }
        }
        $notices = Notice::where('is_deleted', false)->get();
        $sources = Source::where('is_deleted', false)->get();
        return view('supporters.calls', [
            'supportersForSelectInView' => $supportersForSelectInView,
            'products' => $products,
            'notices' => $notices,
            'sources' => $sources,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'products_id' => $products_id,
            'notices_id' => $notices_id,
            'supporters_id' => $supporters_id,
            'replier_id' => $replier_id,
            'sources_id' => $sources_id,
            'callResults' => $callResults,
            "isSingle" => ($theSupporters_id != null),
            'persons' => $persons,
            'msg_success' => request()->session()->get('msg_success'),
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }
    public function withCountCallResultForUser($req, $supporters_builder, $columnSortOrder, $call_results_id)
    {
        $supporters = $supporters_builder
            ->withCount([
                'calls',
                'callresult',
                'callresult as count' => function ($query) use ($req, $call_results_id) {
                    $query->where('call_results.id', $call_results_id);
                    $query->where(function ($q) use ($req) {
                        $from_date = $req['from_date'];
                        if ($from_date != "") $q->where('calls.created_at', '>=', SupporterController::jalaliToGregorian($from_date));
                    });
                    $query->where(function ($q) use ($req) {
                        $to_date = $req['to_date'];
                        if ($to_date != "") $q->where('calls.created_at', '<=', SupporterController::jalaliToGregorian($to_date));
                    });
                    $query->where(function ($q) use ($req) {
                        $products_id = (int)$req['products_id'];
                        if ($products_id > 0) $q->where('calls.products_id', $products_id);
                    });
                    $query->where(function ($q) use ($req) {
                        $notices_id = (int)$req['notices_id'];
                        if ($notices_id > 0) $q->where('calls.notices_id', $notices_id);
                    });
                    $query->where(function ($q) use ($req) {
                        $sources_id = (int)$req['sources_id'];
                        if ($sources_id > 0) {
                            $students = Student::where('sources_id', $sources_id)->where('is_deleted', false)->where('banned', false)->pluck('id');
                            $q->whereIn('students_id', $students);
                        }
                    });
                    $query->where(function ($q) use ($req) {
                        $from_date = $req['from_date'];
                        $to_date = $req['to_date'];
                        if ($from_date == null && $to_date == null) {
                            $q->where('calls.created_at', '<=', date("Y-m-d 23:59:59"))->where('calls.created_at', '>=', date("Y-m-d 00:00:00"));
                        }
                    });
                },
            ])
            ->skip($req['start'])
            ->take($req['length'])
            ->orderBy('count', $columnSortOrder)
            ->get();
        return $supporters;
    }
    public function callIndexPost(Request $request)
    {
        $theSupporters_id = null;
        $from_date = null;
        $to_date = null;
        $products_id = null;
        $notices_id = null;
        $replier_id = null;
        $sources_id = null;
        $count = 0;
        $callResults = CallResult::where('is_deleted', false)->get();
        $user_id = Auth::user()->id;
        $foundUser = User::where('is_deleted', false)->where('id', $user_id)->first();
        if ($foundUser && $foundUser->group->type == "support") {
            $theSupporters_id = $user_id;
        }
        if ($theSupporters_id == null) {
            $supportGroupId = Group::getSupport();
            if ($supportGroupId)
                $supportGroupId = $supportGroupId->id;
            $supporters_id = null;
            $supporters = User::where('users.is_deleted', false)->where('users.groups_id', $supportGroupId);
            $count = $supporters->get() ? count($supporters->get()) : 0;
            if (request()->input('supporters_id')) {
                $supporters_id = request()->input('supporters_id');
                $supporters = $supporters->where('id', $supporters_id);
            }
            $supporters_builder = $supporters->with('students.purchases')->with('students.studenttags.tag');
        } else {
            $supporters_builder = User::where('id', $theSupporters_id);
            $count = $supporters_builder->get() ? count($supporters_builder->get()) : 0;
        }
        $isSingle = ($theSupporters_id != null);
        $req =  request()->all();
        if (!isset($req['start'])) {
            $req['start'] = 0;
            $req['length'] = 10;
            $req['draw'] = 1;
        }
        if (!$isSingle) {
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
            if ($columnName != 'row' && $columnName != "call_count" && !str_contains($columnName, "call_result")) {
                $supporters = $supporters_builder->orderBy($columnName, $columnSortOrder)
                    ->select('users.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            } else if ($columnName == "call_count") {
                $supporters = $supporters_builder
                    ->withCount([
                        'calls',
                        'calls as count' => function ($query) use ($req) {
                            $query->where(function ($q) use ($req) {
                                $from_date = $req['from_date'];
                                if ($from_date != "") $q->where('created_at', '>=', SupporterController::jalaliToGregorian($from_date));
                            });
                            $query->where(function ($q) use ($req) {
                                $to_date = $req['to_date'];
                                if ($to_date != "") $q->where('created_at', '<=', SupporterController::jalaliToGregorian($to_date));
                            });
                            $query->where(function ($q) use ($req) {
                                $products_id = (int)$req['products_id'];
                                if ($products_id > 0) $q->where('products_id', $products_id);
                            });
                            $query->where(function ($q) use ($req) {
                                $notices_id = (int)$req['notices_id'];
                                if ($notices_id > 0) $q->where('notices_id', $notices_id);
                            });
                            $query->where(function ($q) use ($req) {
                                $sources_id = (int)$req['sources_id'];
                                if ($sources_id > 0) {
                                    $students = Student::where('sources_id', $sources_id)->where('is_deleted', false)->where('banned', false)->pluck('id');
                                    $q->whereIn('students_id', $students);
                                }
                            });
                            $query->where(function ($q) use ($req) {
                                $from_date = $req['from_date'];
                                $to_date = $req['to_date'];
                                if ($from_date == null && $to_date == null) {
                                    $q->where('created_at', '<=', date("Y-m-d 23:59:59"))->where('created_at', '>=', date("Y-m-d 00:00:00"));
                                }
                            });
                        },
                    ])
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->orderBy('count', $columnSortOrder)
                    ->get();
            } else if (str_contains($columnName, "call_result")) {
                foreach ($callResults as $item) {
                    if ($columnName == "call_result" . $item->id) {
                        $supporters = $this->withCountCallResultForUser($req, $supporters_builder, $columnSortOrder, $item->id);
                    }
                }
            } else {
                $supporters = $supporters_builder->select('users.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            }
        } else {
            $supporters = $supporters_builder
                ->offset($req['start'])
                ->limit($req['length'])
                ->get();
        }
        $data = [];
        $lastTds = [];
        foreach ($supporters as $index => $supporter) {
            $call = Call::where("users_id", $supporter->id)->where('is_deleted', false);
            $supporterCallResults = $callResults->ToArray();
            foreach ($supporterCallResults as $sindex => $supporterCallResult) {
                $supporterCallResults[$sindex]['count'] = 0;
            }
            if (request()->input('from_date')) {
                $from_date = SupporterController::jalaliToGregorian(request()->input('from_date'));
                if ($from_date != '')
                    $call->where('created_at', '>=', $from_date);
            }
            if (request()->input('to_date')) {
                $to_date = SupporterController::jalaliToGregorian(request()->input('to_date'));
                if ($to_date != '')
                    $call->where('created_at', '<=', $to_date);
            }
            if (request()->input('products_id')) {
                $products_id = (int)request()->input('products_id');
                if ($products_id > 0)
                    $call->where('products_id', $products_id);
            }
            if (request()->input('notices_id')) {
                $notices_id = (int)request()->input('notices_id');
                if ($notices_id > 0)
                    $call->where('notices_id', $notices_id);
            }
            if (request()->input('sources_id')) {
                $sources_id = (int)request()->input('sources_id');
                if ($sources_id > 0) {
                    $students = Student::where('sources_id', $sources_id)->where('is_deleted', false)->where('banned', false)->pluck('id');
                    $call->whereIn('students_id', $students);
                }
            }
            if ($from_date == null && $to_date == null) {
                $call->where('created_at', '<=', date("Y-m-d 23:59:59"))->where('created_at', '>=', date("Y-m-d 00:00:00"));
            }
            $calls = $call->get();
            foreach ($calls as $theCall) {
                foreach ($supporterCallResults as $sindex => $supporterCallResult) {
                    if ($supporterCallResult['id'] == $theCall->call_results_id) {
                        $supporterCallResults[$sindex]['count']++;
                    }
                }
            }
            $supporter->callCount = count($calls);
            $supporter->supporterCallResults = $supporterCallResults;
        }
        $from_date = ($from_date) ? $from_date : date("Y-m-d");
        $to_date = ($to_date) ? $to_date : date("Y-m-d");
        $products_id = ($products_id) ? $products_id : '';
        $notices_id = ($notices_id) ? $notices_id : '';
        $replier_id = ($replier_id) ? $replier_id : '';
        $sources_id = ($sources_id) ? $sources_id : '';
        foreach ($supporters as $index => $item) {
            $countCall = '<form method="GET" action="' . route('user_supporter_acall', $item->id) . '" target="_blank" >
            <input type="hidden" name="from_date" value="' . $from_date . '" />
            <input type="hidden" name="to_date" value="' . $to_date . '" />
            <input type="hidden" name="products_id" value="' . $products_id . '" />
            <input type="hidden" name="notices_id" value="' . $notices_id . '" />
            <input type="hidden" name="replier_id" value="' . $replier_id . '" />
            <input type="hidden" name="sources_id" value="' . $sources_id . '" />
            <input type="hidden" name="id" value="' . $item->id . '" />
            <button class="btn btn-link">' . $item->callCount . '</button>
            </form>';
            if ($item->supporterCallResults) {
                $i = 1;
                foreach ($item->supporterCallResults as $sitem) {
                    $lastTds["call_result" . $sitem['id']] = (isset($sitem['count'])) ? $sitem['count'] : '0';
                    $i++;
                }
            }
            if ($isSingle) {
                $data[] = array_merge([
                    "row" => $req['start'] + $index + 1,
                    "call_count" => $countCall
                ], $lastTds);
            } else {
                $data[] = array_merge([
                    "row" => $req['start'] + $index + 1,
                    "id" => $item->id,
                    "first_name" => $item->first_name,
                    "last_name" => $item->last_name,
                    "call_count" => $countCall
                ], $lastTds);
            }
        }

        $result = [
            "draw" => $req['draw'],
            "data" => $data,
            "recordsTotal" => $count,
            "recordsFiltered" => $count
        ];

        return $result;
    }


    public function acallIndex(Request $request, $id)
    {
        $supporter = User::find($id);
        $persons = [
            "student" => "دانش آموز",
            "father" => "پدر",
            "mother" => "مادر",
            "other" => "غیره"
        ];
        $fullName = null;
        if ($request->getMethod() == 'POST') {
            if (request()->input('call_id')) {
                $call =  Call::where("users_id", $id)->where('id', request()->input('call_id'))->where('is_deleted', false)->first();
                if ($call) {
                    $call->is_deleted = 1;
                    try {
                        $call->save();
                    } catch (Exception $e) {
                        dd($e);
                    }
                }
            }
            $calls = Call::where("users_id", $id)->where('is_deleted', false);
            if (request()->input('from_date')) {
                $from_date = request()->input('from_date');
                if ($from_date != '')
                    $calls->where('created_at', '>=', $from_date);
            }
            if (request()->input('to_date')) {
                $to_date = request()->input('to_date');
                if ($to_date != '')
                    $calls->where('created_at', '<=', $to_date);
            }
            if (request()->input('products_id')) {
                $products_id = (int)request()->input('products_id');
                if ($products_id > 0)
                    $calls->where('products_id', $products_id);
            }
            if (request()->input('notices_id')) {
                $notices_id = (int)request()->input('notices_id');
                if ($notices_id > 0)
                    $calls->where('notices_id', $notices_id);
            }
            if (request()->input('sources_id')) {
                $sources_id = (int)request()->input('sources_id');
                if ($sources_id > 0) {
                    $students = Student::where('sources_id', $sources_id)->where('is_deleted', false)->where('banned', false)->pluck('id');
                    $calls->whereIn('students_id', $students);
                }
            }
            if (request()->input('fullName')) {
                $fullName = trim(request()->input('fullName'));
                $students = Student::select('id', DB::raw("CONCAT(first_name,' ',last_name)"))->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $fullName . '%')
                    ->where('is_deleted', false)->where('banned', false)->pluck('id');
                $calls->whereIn('students_id', $students);
            }
            $callsBuilder = $calls->with('student')->with('product.collection')->with('notice');
            $calls = $calls->with('student')->with('product.collection')->with('notice')->get();
            $count_calls = $calls ? count($calls) : 0;
            foreach ($calls as $index => $call) {
                if ($call->product) {
                    $product = $call->product;
                    $product->parents = "-";
                    if ($product->collection) {
                        $parents = $product->collection->parents();
                        $name = ($parents != '') ? $parents . "->" . $product->collection->name : $product->collection->name;
                        $product->parents = $name;
                    }
                    $calls[$index]->product = $product;
                }
            }
        }


        if ($request->getMethod() == 'GET') {
            return view("supporters.supportercalls", [
                "supporter" => $supporter,
                "from_date" => $request->input('from_date'),
                "to_date" => $request->input('to_date'),
                "products_id" => $request->input('products_id'),
                "notices_id" => $request->input('notices_id'),
                'replier_id' => $request->input('replier_id'),
                "sources_id" => $request->input('sources_id'),
                'id' => $id,
                'supporter' => $supporter,
                "request" => request()->all(),
                "route" => 'user_supporter_acall'
            ]);
        } else {
            $req =  $request->all();
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

            if ($columnName != 'row' && $columnName != "end") {
                $calls = $callsBuilder->orderBy($columnName, $columnSortOrder)
                    ->select('calls.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            } else {
                $calls = $callsBuilder->select('calls.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            }
            $data = [];
            foreach ($calls as $index => $item) {

                $data[] = [
                    "row" => $req['start'] + $index + 1,
                    "id" => $item->id,
                    "students_id" => ($item->student) ? $item->student->first_name . ' ' . $item->student->last_name : '-',
                    "products_id" => ($item->product) ? (($item->product->parents != '-') ? $item->product->parents . '->' : '') . $item->product->name : '-',
                    "notices_id" => ($item->notice) ? $item->notice->name : '-',
                    "replier" => $persons[$item->replier],
                    "call_results_id" => ($item->callresult) ? $item->callresult->title : '-',
                    "next_call" => ($item->next_call) ? jdate($item->next_call)->format("Y/m/d") : '-',
                    "next_to_call" => ($item->next_to_call) ? $persons[$item->next_to_call] : '-',
                    "created_at" => ($item->created_at) ? jdate($item->created_at)->format("Y/m/d H:i:s") : jdate()->format("Y/m/d H:i:s"),
                    "description" => $item->description,
                    "end" => '<a class="btn btn-danger" onclick ="destroy(event)" href="' . route('user_supporter_delete_call', ["user_id" => $id, "id" => $item->id]) . '">حذف</a>'
                ];
            }

            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "recordsTotal" => $count_calls,
                "recordsFiltered" => $count_calls
            ];

            return $result;
        }
    }

    public function create(Request $request)
    {
        $groups = Group::all();
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $user = new User;
        if ($request->getMethod() == 'GET') {
            return view('supporters.create', [
                "groups" => $groups,
                "user" => $user
            ]);
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->groups_id = $supportGroupId; //(int)$request->input('groups_id');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->pass = $request->input('password');
        $user->gender = $request->input('gender');
        $user->national_code = $request->input('national_code');
        $user->education = $request->input('education');
        $user->major = $request->input('major');
        $user->home_phone = $request->input('home_phone');
        $user->mobile = $request->input('mobile');
        $user->work_mobile = $request->input('work_mobile');
        $user->home_address = $request->input('home_address');
        $user->work_address = $request->input('work_address');
        $user->max_student = (int)$request->input('max_student');
        if ($request->file('image_path')) {
            $filename = now()->timestamp . '.' . $request->file('image_path')->extension();
            $user->image_path = $request->file('image_path')->storeAs('supporters', $filename, 'public_uploads');
        }

        $find = User::where('email', $request->input('email'))->first();
        if ($find) {
            $request->session()->flash("msg_error", "نام کاربری قبلا استفاده شده است!");
            return view('supporters.create', [
                "groups" => $groups,
                "user" => $user,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        }


        if ($request->input('password') != $request->input('repassword')) {
            $request->session()->flash("msg_error", "رمز عبور و تکرار آن باید یکی باشند");
            return view('supporters.create', [
                "groups" => $groups,
                "user" => $user,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        }

        $user->save();

        $request->session()->flash("msg_success", "کاربر با موفقیت افزوده شد.");
        return redirect()->route('user_supporters');
    }

    public function students($id)
    {
        return $this->student($id);
    }
    public function findStudent($request, $students)
    {
        $request != null ? $students = $students->where('id', (int)$request) : $students = '';
    }
    public function arrOfAuxilaries($input, $arr)
    {
        if ($input) {
            $arr[] = $input;
        }
        return $arr;
    }
    function isSubset($arr1, $arr2, $m, $n)
    {
        $i = 0;
        $j = 0;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                if ($arr2[$i] == $arr1[$j])
                    break;
            }

            /* If the above inner loop was
        not broken at all then arr2[i]
        is not present in arr1[] */
            if ($j == $m)
                return 0;
        }

        /* If we reach here then all
    elements of arr2[] are present
    in arr1[] */
        return 1;
    }
    public function changeLevelAjax(Request $request)
    {
        $students_id = $request->input('students_id');
        $level = $request->level;
        $student = Student::where('id', $students_id)->first();
        $student->level = $level;
        $student->save();
        return [
            "error" => null,
            "data" => null
        ];
    }

    public function showStudents($id = null, $level, $view, $route)
    {

        $searchStudent = new SearchStudent;
        $user = null;
        $sw = null;
        $count = 0;
        $megeStudents = AppMergeStudents::where('is_deleted', false)->get();
        $arr_of_auxilaries = [];
        foreach ($megeStudents as $index => $student) {
            $arr_of_auxilaries = $this->arrOfAuxilaries($student->auxilary_students_id, $arr_of_auxilaries);
            $arr_of_auxilaries = $this->arrOfAuxilaries($student->second_auxilary_students_id, $arr_of_auxilaries);
            $arr_of_auxilaries = $this->arrOfAuxilaries($student->third_auxilary_students_id, $arr_of_auxilaries);
        }
        $saleSuggestions = SaleSuggestion::all();
        if ($id == null) {
            $id = Auth::user()->id;
        } else {
            $user = User::find($id);
        }
        $students = $level == "all" ? Student::where('students.is_deleted', false)->where('students.banned', false)->where('students.archived', false)->where('supporters_id', $id) : Student::where('students.is_deleted', false)->where('students.level', $level)->where('students.banned', false)->where('students.archived', false)->where('supporters_id', $id);
        $sources = Source::where('is_deleted', false)->get();
        $products = Product::where('is_deleted', false)->where('is_private', false)->with('collection')->orderBy('name')->get();
        foreach ($products as $index => $product) {
            $products[$index]->parents = "-";
        }
        $callResults = CallResult::where('is_deleted', false)->get();
        $notices = Notice::where('is_deleted', false)->get();
        $name = null;
        $sources_id = null;
        $phone = null;
        $students_id = null;
        $calls_id = null;
        $education_level = null;
        $has_collection = 'false';
        $has_the_product = '';
        $has_the_tags = '';
        $has_call_result = '';
        $has_site = 'false';
        $order_collection = 'false';
        $has_reminder = 'false';
        $has_tag = 'false';
        $appMergeStudents = null;
        $replier = null;
        $products_id = null;
        $notices_id = null;
        $call_results_id = null;
        $next_to_call = null;
        if (request()->input('students_id') != null) {
            $students_id = (int)request()->input('students_id');
            $calls_id = (int)request()->input('calls_id');
            $call = Call::where('id', $calls_id)->where('students_id', $students_id)->where('is_deleted', false)->first();
            $replier = $call ? $call->replier : '-';
            $products_id = $call ? $call->products_id : '-';
            $notices_id = $call ? $call->notices_id : '-';
            $call_results_id = $call ? $call->call_results_id : '-';
            $next_to_call = $call ? $call->next_to_call : '-';
            $students = $students->where('id', $students_id);
        }

        $this->findStudent(request()->input('main_id'), $students);
        $this->findStudent(request()->input('auxilary_id'), $students);
        $this->findStudent(request()->input('second_auxilary_id'), $students);
        $this->findStudent(request()->input('third_auxilary_id'), $students);

        if (request()->getMethod() == 'POST') {

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
            if (request()->input('has_collection') != null) {
                $has_collection = request()->input('has_collection');
                if ($has_collection == 'true') {
                    $studentCollections = StudentCollection::where('is_deleted', false)->pluck('students_id');
                    $students = $students->whereIn('students.id', $studentCollections);
                }
            }
            if (request()->input('has_the_product') != null && request()->input('has_the_product') != '') {
                $has_the_product = request()->input('has_the_product');
                $purchases = Purchase::where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->whereIn('purchases.products_id', explode(',', $has_the_product))->pluck('students_id');
                $students = $students->whereIn('students.id', $purchases);
            }
            if (request()->input('has_call_result') != null && request()->input('has_call_result') != '') {
                $has_call_result = request()->input('has_call_result');
                $arr_of_calls = explode(',', $has_call_result);
                $calls = Call::where('is_deleted', false)->get();
                $callsArray = [];
                $valid_student_ids = [];

                foreach ($calls as $call) {
                    $callsArray[$call->students_id][] = $call->call_results_id;
                    if ($this->isSubset($callsArray[$call->students_id], $arr_of_calls, count($callsArray[$call->students_id]), count($arr_of_calls))) {
                        $valid_student_ids[] = $call->students_id;
                    }
                }
                $valid_student_ids = array_unique($valid_student_ids);
                $students = $students->whereIn('students.id', $valid_student_ids);
            } else {
                if (request()->input('has_the_product') != null && request()->input('has_the_product') != '') {
                    $has_the_product = request()->input('has_the_product');
                    $arr_of_products = explode(',', $has_the_product);
                    $purchases = Purchase::where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->get();
                    $products = [];
                    $valid_student_ids = [];
                    foreach ($purchases as $purchase) {
                        $products[$purchase->students_id][] = $purchase->products_id;
                        if ($this->isSubset($products[$purchase->students_id], $arr_of_products, count($products[$purchase->students_id]), count($arr_of_products))) {
                            $valid_student_ids[] = $purchase->students_id;
                        }
                    }
                    $students = $students->whereIn('students.id', $valid_student_ids);
                }
            }
            if (request()->input('has_the_tags') != null && request()->input('has_the_tags') != '') {
                $has_the_tags = request()->input('has_the_tags');
                $arr_of_tags = explode(',', $has_the_tags);
                $student_tags = StudentTag::where('is_deleted', false)->get();
                $tags = [];
                $valid_student_ids = [];
                foreach ($student_tags as $student_tag) {
                    $tags[$student_tag->students_id][] = $student_tag->tags_id;
                    if ($this->isSubset($tags[$student_tag->students_id], $arr_of_tags, count($tags[$student_tag->students_id]), count($arr_of_tags))) {
                        $valid_student_ids[] = $student_tag->students_id;
                    }
                }
                $students = $students->whereIn('students.id', $valid_student_ids);
            }
            if (request()->input('has_need_tags') != null && request()->input('has_need_tags') != '') {
                $has_the_tags = request()->input('has_need_tags');
                $arr_of_tags = explode(',', $has_the_tags);
                $student_tags = StudentTag::where('is_deleted', false)->get();
                $tags = [];
                $valid_student_ids = [];
                foreach ($student_tags as $student_tag) {
                    $tags[$student_tag->students_id][] = $student_tag->tags_id;
                    if ($this->isSubset($tags[$student_tag->students_id], $arr_of_tags, count($tags[$student_tag->students_id]), count($arr_of_tags))) {
                        $valid_student_ids[] = $student_tag->students_id;
                    }
                }
                $students = $students->whereIn('students.id', $valid_student_ids);
            }
            if (request()->input('has_site') != null) {
                $has_site = request()->input('has_site');
                if ($has_site == 'true') {
                    $purchases = Purchase::where('is_deleted', false)->where('type', 'site_successed')->pluck('students_id');
                    $students = $students->whereIn('students.id', $purchases);
                }
            }
            if (request()->input('has_reminder') != null) {
                $has_reminder = request()->input('has_reminder');
                if ($has_reminder == 'true') {
                    $students = $students->has('remindercalls');
                }
            }
            if (request()->input('has_tag') != null) {
                $has_tag = request()->input('has_tag');
                if ($has_tag == 'true') {
                    $students = $students->has('studenttags');
                }
            }
            if (request()->input('education_level') != null) {
                $egucation_level = request()->input('education_level');
                $students = $students->where('egucation_level', $egucation_level);
            }
            if (request()->input('major') != null) {
                $major = request()->input('major');
                $students = $students->where('major', $major);
            }
            if (request()->input('conditions') != null) {
                $condition_id = request()->input('conditions');
                $moralTags = Tag::where('type', 'moral')->where('is_deleted', false)->orderBy('name')->get();
                if ($condition_id) {
                    $suggestion = SaleSuggestion::where('id', $condition_id)->first();
                    $purchases = [];
                    $student_tags = [];
                    $need_tags = [];
                    $school = null;
                    $last_year_grade = 0;
                    $average = 0;
                    $source = 0;
                    if ($suggestion->if_products_id != null && $suggestion->if_products_id != "") {
                        $if_products_id = explode(',', $suggestion->if_products_id);
                        $purchases = Purchase::whereIn('products_id', $if_products_id)->pluck('students_id');
                    }
                    if ($suggestion->if_moral_tags_id != null && $suggestion->if_moral_tags_id != "") {
                        $if_moral_tags_id = explode(',', $suggestion->if_moral_tags_id);
                        $student_tags = StudentTag::whereIn('tags_id', $if_moral_tags_id)->where('is_deleted', false)->pluck('students_id');
                    }
                    if ($suggestion->if_need_tags_id != null && $suggestion->if_need_tags_id != "") {
                        $if_need_tags_id = explode(',', $suggestion->if_need_tags_id);
                        $need_tags = StudentTag::whereIn('tags_id', $if_need_tags_id)->where('is_deleted', false)->pluck('students_id');
                    }
                    if ($suggestion->if_schools_id != null && $suggestion->if_schools_id != "") {
                        $school = $suggestion->if_schools_id;
                    }
                    if ($suggestion->if_last_year_grade != 0) {
                        $last_year_grade = $suggestion->if_last_year_grade == null ? 0 : $suggestion->if_last_year_grade;
                    }
                    if ($suggestion->if_avarage != 0) {
                        $average = $suggestion->if_avarage == null ? 0 : $suggestion->if_avarage;
                    }
                    if ($suggestion->if_sources_id != 0) {
                        $source = $suggestion->if_sources_id == null ? 0 : $suggestion->if_sources_id;
                    }
                    $students = $students->where(function ($query) use ($purchases) {
                        if ($purchases) $query->whereIn('students.id', $purchases);
                    })
                        ->where(function ($query) use ($student_tags) {
                            if ($student_tags) $query->whereIn('students.id', $student_tags);
                        })
                        ->where(function ($query) use ($need_tags) {
                            if ($need_tags) $query->whereIn('students.id', $need_tags);
                        })
                        ->where(function ($query) use ($last_year_grade) {
                            if ($last_year_grade) $query->where('students.last_year_grade', '<=', $last_year_grade);
                        })
                        ->where(function ($query) use ($average) {
                            if ($average) $query->where('students.average', '>=', $average);
                        })
                        ->where(function ($query) use ($school) {
                            if ($school) $query->where('students.school', $school);
                        })
                        ->where(function ($query) use ($source) {
                            if ($source) $query->where('students.sources_id', $source);
                        });
                }
            }
        }

        $students = $students
            ->where('supporters_id', $id)
            ->whereNotIn('id', $arr_of_auxilaries)
            ->with('user')
            ->with('studentcollections.collection')
            ->with('studenttags.tag.parent_four')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('calls.product')
            ->with('calls.notice')
            ->with('calls.callresult')
            ->with('mergestudent.mainStudent')
            ->with('mergestudent.auxilaryStudent')
            ->with('mergestudent.secondAuxilaryStudent')
            ->with('mergestudent.thirdAuxilaryStudent')
            ->with('mergeauxilarystudent.mainStudent')
            ->with('mergeauxilarystudent.auxilaryStudent')
            ->with('mergeauxilarystudent.secondAuxilaryStudent')
            ->with('mergeauxilarystudent.thirdAuxilaryStudent')
            ->with('mergesecondauxilarystudent.mainStudent')
            ->with('mergesecondauxilarystudent.auxilaryStudent')
            ->with('mergesecondauxilarystudent.secondAuxilaryStudent')
            ->with('mergesecondauxilarystudent.thirdAuxilaryStudent')
            ->with('mergethirdauxilarystudent.mainStudent')
            ->with('mergethirdauxilarystudent.auxilaryStudent')
            ->with('mergethirdauxilarystudent.secondAuxilaryStudent')
            ->with('mergethirdauxilarystudent.thirdAuxilaryStudent');
        //->orderBy('created_at', 'desc');
        $theStudents = $students;
        $getStudents = $students->get();

        if (request()->input('order_collection') != null) {
            $order_collection = request()->input('order_collection');

            if ($order_collection == 'true') {
                $students = $getStudents->sortBy(function ($hackathon) {
                    return $hackathon->studentcollections->count();
                }, SORT_REGULAR, true);
            }
        }
        $parentOnes = TagParentOne::has('tags')->where('is_deleted', false)->get();
        $parentTwos = TagParentTwo::has('tags')->where('is_deleted', false)->get();
        $parentThrees = TagParentThree::has('tags')->where('is_deleted', false)->get();
        $parentFours = TagParentFour::has('tags')->where('is_deleted', false)->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $needTagParentOnes = NeedTagParentOne::where('is_deleted', false)->has('tags')->get();
        $needTagParentTwos = NeedTagParentTwo::where('is_deleted', false)->has('tags')->get();
        $needTagParentThrees = NeedTagParentThree::where('is_deleted', false)->has('tags')->get();
        $needTagParentFours = NeedTagParentFour::where('is_deleted', false)->has('tags')->get();
        $moralTags = Tag::where('is_deleted', false)->where('type', 'moral')->get();
        $needTags = Tag::where('is_deleted', false)->where('type', 'need')->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();
        $collections = Collection::where('is_deleted', false)->get();

        foreach ($getStudents as $index => $student) {
            $getStudents[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
            if ($getStudents[$index]->calls)
                foreach ($getStudents[$index]->calls as $cindex => $call) {
                    $getStudents[$index]->calls[$cindex]->next_call = ($getStudents[$index]->calls[$cindex]->next_call) ? jdate(strtotime($getStudents[$index]->calls[$cindex]->next_call))->format("Y/m/d") : null;
                }
        }
        foreach ($getStudents as $index => $student) {
            $getStudents[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }
        if (request()->getMethod() == 'GET') {
            return view($view, [
                'route' => $route,
                'user' => $user,
                'students' => $getStudents,
                'sources' => $sources,
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
                'products' => $products,
                'notices' => $notices,
                'callResults' => $callResults,
                'has_collection' => $has_collection,
                'has_the_product' => ($has_the_product != '') ? explode(',', $has_the_product) : '',
                'has_the_tags' => ($has_the_tags != '') ? explode(',', $has_the_tags) : '',
                'has_call_result' => $has_call_result,
                'has_site' => $has_site,
                'order_collection' => $order_collection,
                'has_reminder' => $has_reminder,
                'has_tag' => $has_tag,
                "needTagParentOnes" => $needTagParentOnes,
                "needTagParentTwos" => $needTagParentTwos,
                "needTagParentThrees" => $needTagParentThrees,
                "needTagParentFours" => $needTagParentFours,
                "students_id" => $students_id,
                "calls_id" => $calls_id,
                "replier" => $replier,
                "products_id" => $products_id,
                "notices_id" => $notices_id,
                "next_to_call" => $next_to_call,
                "call_results_id" => $call_results_id,
                "saleSuggestions" => $saleSuggestions,
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        } else {
            $req =  request()->all();
            if (!isset($req['start'])) {
                $req['start'] = 0;
                $req['length'] = 10;
                $req['draw'] = 1;
            }

            $columnIndex_arr = $req['order'];
            $columnName_arr = $req['columns'];
            $order_arr = $req['order'];
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc

            if ($columnName != 'row' && $columnName != 'end' && $columnName != "temps" && $columnName != "tags") {
                $sw = "all";
                $students = $theStudents->orderBy($columnName, $columnSortOrder)
                    ->select('students.*')
                    ->whereNotIn('students.id', $arr_of_auxilaries)
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            } else if ($columnName == "tags") {
                $sw = "tags";
                $students = $theStudents
                    ->whereNotIn('students.id', $arr_of_auxilaries)
                    ->withCount('studenttags')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->orderBy('studenttags_count', $columnSortOrder)
                    ->get();
            } else {
                $sw = "other";
                $students = $theStudents->select('students.*')
                    ->whereNotIn('students.id', $arr_of_auxilaries)
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            }
            $data = [];
            foreach ($students as $index => $student) {
                $students[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
            }
            foreach ($students as $index => $item) {
                $tags = "";
                if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                    for ($i = 0; $i < count($item->studenttags); $i++) {
                        $tags .= '<span class="d-inline-block px-1 rounded small mt-2 ' . (($item->studenttags[$i]->tag->type == 'moral') ? 'bg-cyan' : 'bg-warning') . ' p-1">
                        ' . (($item->studenttags[$i]->tag->parent_four) ? $item->studenttags[$i]->tag->parent_four->name . '->' : '') . ' ' . $item->studenttags[$i]->tag->name . '
                        </span><br/>';
                    }
                }
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
                            $temps .= '<span class="bg-danger d-inline-block px-1 rounded small mt-2 p-1">';
                        else
                            $temps .= '<span class="bg-cyan d-inline-block px-1 rounded small mt-2 p-1">';
                        $temps .= $sitem->temperature->name . '</span>';
                    }
                }
                foreach ($item->calls as $call) {
                    $call->next_call =  $call->next_call ? jdate(strtotime($call->next_call))->format('Y/m/d') : '-';
                }
                $levelsToSelect = "";
                $levels = [1,2,3,4];
                foreach ($levels as $l) {
                    $levelsToSelect .= '<option value="' . $l . '"';
                    if ($l == $item->level)
                        $levelsToSelect .= ' selected';
                    $levelsToSelect .= '>' . $l . '</option>';
                }
                if ($route == "supporters_student") {
                    $data[] = [
                        "row" => $index + 1,
                        "id" => $item->id,
                        "first_name" => $item->first_name,
                        "last_name" => $item->last_name,
                        "users_id" => $registerer,
                        "sources_id" => ($item->source) ? $item->source->name : '-',
                        "tags" => $tags,
                        "temps" => $temps,
                        "level" => '<select id="level_' . $index . '" class="form-control select2">'.
                           $levelsToSelect
                        .'</select>
                        <a class="btn btn-success btn-sm" href="#" onclick="return changeLevel(' . $index . "," . $item->id . ');">
                            ذخیره
                        </a>
                        <br/>
                        <img id="loading-' . $index . '" src="/dist/img/loading.gif" style="height: 20px;display: none;" />',
                        "end" => ""
                    ];
                } else {
                    $data[] = [
                        "row" => $index + 1,
                        "id" => $item->id,
                        "first_name" => $item->first_name,
                        "last_name" => $item->last_name,
                        "users_id" => $registerer,
                        "sources_id" => ($item->source) ? $item->source->name : '-',
                        "tags" => $tags,
                        "temps" => $temps,
                        "end" => ""
                    ];
                }
            }
            $result = [
                "draw" => $req['draw'],
                "data" => $data,
                "students" => $students,
                "recordsTotal" => count($getStudents),
                "recordsFiltered" => count($getStudents)
            ];

            return $result;
        }
    }
    public function showSanads($id = null, $level, $view, $route)
    {
       $user_id=Auth::user()->id; 
       if($user_id==$id)
       {
            $sanads= Sanad::where('supporter_id',$id)->get();
            return view($view,[
                'sanads' => $sanads
            ]);
       }
       return redirect('/login');
       
    }
    public function sanad($id = null)
    {       
        return $this->showSanads($id, 'all', "supporters.sanad", "supporters_sanad");
    }
    public function student($id = null)
    {
        return $this->showStudents($id, 'all', "supporters.student", "supporters_student");
    }
    public function levelOneStudents($id = null)
    {
        return $this->showStudents($id, '1', "supporters.level1", "student_level_1");
    }
    public function levelTwoStudents($id = null)
    {
        return $this->showStudents($id, '2', "supporters.level2", "student_level_2");
    }
    public function levelThreeStudents($id = null)
    {
        return $this->showStudents($id, '3', "supporters.level3", "student_level_3");
    }
    public function levelFourStudents($id = null)
    {
        return $this->showStudents($id, '4', "supporters.level4", "student_level_4");
    }

    public function newStudents()
    {

        $searchStudent = new SearchStudent;
        $students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporter_seen', false)->where('supporters_id', Auth::user()->id);
        $sources = Source::where('is_deleted', false)->get();
        $name = null;
        $sources_id = null;
        $phone = null;
        if (request()->getMethod() == 'POST') {
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
        $theStudents = $students;
        $getStudents = $students->get();
        $count = $getStudents ? count($getStudents) : 0;
        $students = $theStudents
            ->with('user')
            ->with('studenttags.tag.parent_four')
            ->with('studentcollections.collection')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->get();
        $moralTags = Tag::where('is_deleted', false)
            ->where('type', 'moral')
            ->get();
        $needTags = Tag::where('is_deleted', false)
            ->where('type', 'need')
            ->get();
        $parentOnes = TagParentOne::has('tags')->get();
        $parentTwos = TagParentTwo::has('tags')->get();
        $parentThrees = TagParentThree::has('tags')->get();
        $parentFours = TagParentFour::has('tags')->get();
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

        foreach ($getStudents as $index => $student) {
            $getStudents[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }

        if (request()->getMethod() == 'GET') {
            return view('supporters.new', [
                'students' => $getStudents,
                'sources' => $sources,
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
                'msg_success' => request()->session()->get('msg_success'),
                'msg_error' => request()->session()->get('msg_error')
            ]);
        } else {
            $req =  request()->all();
            if (!isset($req['start'])) {
                $req['start'] = 0;
                $req['length'] = 10;
                $req['draw'] = 1;
            }
            $columnIndex_arr = $req['order'];
            $columnName_arr = $req['columns'];
            $order_arr = $req['order'];
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc

            if ($columnName != 'row' && $columnName != 'end' && $columnName != "temps" && $columnName != "tags") {
                $students = $theStudents->orderBy($columnName, $columnSortOrder)
                    ->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            } else if ($columnName == "tags") {
                $students = $theStudents
                    ->withCount('studenttags')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->orderBy('studenttags_count', $columnSortOrder)
                    ->get();
            } else {
                $students = $theStudents->select('students.*')
                    ->skip($req['start'])
                    ->take($req['length'])
                    ->get();
            }
            $data = [];
            if ($students) {
                foreach ($students as $index => $item) {
                    $tags = "";
                    if (($item->studenttags && count($item->studenttags) > 0) || ($item->studentcollections && count($item->studentcollections) > 0)) {
                        for ($i = 0; $i < count($item->studenttags); $i++) {
                            $tags .= '<span class="d-inline-block px-1 rounded small mt-2 ' . (($item->studenttags[$i]->tag->type == 'moral') ? 'bg-cyan' : 'bg-warning') . ' p-1">
                            ' . (($item->studenttags[$i]->tag->parent_four) ? $item->studenttags[$i]->tag->parent_four->name . '->' : '') . ' ' . $item->studenttags[$i]->tag->name . '
                            </span><br/>';
                        }
                    }
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
                                $temps .= '<span class="d-inline-block px-1 rounded small mt-2 bg-danger p-1">';
                            else
                                $temps .= '<span class="d-inline-block px-1 rounded small mt-2 bg-cyan p-1">';
                            $temps .= $sitem->temperature->name . '</span>';
                        }
                    }
                    $data[] = [
                        "row" => $req['start'] + $index + 1,
                        "id" => $item->id,
                        "first_name" => $item->first_name,
                        "last_name" => $item->last_name,
                        "users_id" => $registerer,
                        "sources_id" => ($item->source) ? $item->source->name : '-',
                        "tags" => $tags,
                        "temps" => $temps,
                        "description" => $item->description,
                        "end" => '<a class="btn btn-success btn-sm" href="#" onclick="return seeStudent(this, ' . $item->id . ');">
                        مشاهده شد
                        </a>'
                    ];
                }
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

    public function getPurchases()
    {
        $supports = [];
        $sources = Source::where('is_deleted', false)->get();
        $name = null;
        $sources_id = null;
        $phone = null;
        $moralTags = Tag::where('is_deleted', false)
            ->where('type', 'moral')
            ->get();
        $students = [];
        if (Gate::allows('purchases')) {
            $students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->get();
            $supportGroupId = Group::getSupport();
            if ($supportGroupId)
                $supportGroupId = $supportGroupId->id;
            $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        } else {
            $students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', Auth::user()->id)->get();
        }
        // foreach ($students as $index => $item) {
        //     $item->today_purchases = $item->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('type', '!=', 'site_failed')->where('is_deleted', false)->count();
        //     $item->save();
        // }
        foreach ($students as $index => $student) {
            $students[$index]->pcreated_at = jdate(strtotime($student->created_at))->format("Y/m/d");
        }
        $parentOnes = TagParentOne::has('tags')->get();
        $parentTwos = TagParentTwo::has('tags')->get();
        $parentThrees = TagParentThree::has('tags')->get();
        $parentFours = TagParentFour::has('tags')->get();
        $collections = Collection::where('is_deleted', false)->get();
        $firstCollections = Collection::where('is_deleted', false)->where('parent_id', 0)->get();
        $secondCollections = Collection::where('is_deleted', false)->whereIn('parent_id', $firstCollections->pluck('id'))->get();
        $thirdCollections = Collection::where('is_deleted', false)->with('parent')->whereIn('parent_id', $secondCollections->pluck('id'))->get();
        $hotTemperatures = Temperature::where('is_deleted', false)->where('status', 'hot')->get();
        $coldTemperatures = Temperature::where('is_deleted', false)->where('status', 'cold')->get();
        $needTagParentOnes = NeedTagParentOne::where('is_deleted', false)->has('tags')->get();
        $needTagParentTwos = NeedTagParentTwo::where('is_deleted', false)->has('tags')->get();
        $needTagParentThrees = NeedTagParentThree::where('is_deleted', false)->has('tags')->get();
        $needTagParentFours = NeedTagParentFour::where('is_deleted', false)->has('tags')->get();
        $products = Product::where('is_deleted', false)->where('is_private', false)->get();
        return view('supporters.purchase', [
            'students' => $students,
            'sources' => $sources,
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
            "needTagParentOnes" => $needTagParentOnes,
            "needTagParentTwos" => $needTagParentTwos,
            "needTagParentThrees" => $needTagParentThrees,
            "needTagParentFours" => $needTagParentFours,
            "supports" => $supports,
            "products" => $products,
            'msg_success' => request()->session()->get('msg_success'),
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }
    public function postPurchases(Request $request)
    {

        $supports = [];
        $students = [];
        $searchStudent = new SearchStudent;
        if (Gate::allows('purchases')) {
            $students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false);
            $supportGroupId = Group::getSupport();
            if ($supportGroupId)
                $supportGroupId = $supportGroupId->id;
            $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        } else {
            $students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', Auth::user()->id);
        }
        $sources = Source::where('is_deleted', false)->get();
        $name = null;
        $sources_id = null;
        $products_id = null;
        $phone = null;
        $supporters_id = null;
        $from_date = null;
        $to_date = null;
        if ($request->input('name') != null) {
            $name = trim(request()->input('name'));
            $students = $searchStudent->search($students, $name);
        }
        if ($request->input('sources_id') != null) {
            $sources_id = (int)request()->input('sources_id');
            $students = $students->where('sources_id', $sources_id);
        }
        if ($request->input('phone') != null) {
            $phone = (int)request()->input('phone');
            $students = $students->where('phone', $phone);
        }
        if ($request->input('products_id') != null) {
            $products_id = (int)request()->input('products_id');
            $studentIds = Purchase::where('products_id', $products_id)->where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->pluck('students_id');
            $students = $students->whereIn('id', $studentIds);
        }
        if (Gate::allows('purchases')) {
            if ($request->input('supporters_id') != null) {
                $supporters_id = (int)request()->input('supporters_id');
                $students = $students->where('supporters_id', $supporters_id);
            }
            if ($request->input('from_date') != null) {
                $from_date = request()->input('from_date');
                $studentIds = Purchase::where('created_at', '>=', $from_date)->where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->pluck('students_id');
                $students = $students->whereIn('id', $studentIds);
            }
            if ($request->input('to_date') != null) {
                $to_date = request()->input('to_date');
                $studentIds = Purchase::where('created_at', '<=', $to_date)->where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->pluck('students_id');
                $students = $students->whereIn('id', $studentIds);
            }
        }
        $allStudents = $students->get();

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
        $students = $students->select('students.*')
            ->with('user')
            ->with('studenttags.tag')
            ->with('studentcollections.collection')
            ->with('studenttags.tag.parent_four')
            ->with('studenttemperatures.temperature')
            ->with('source')
            ->with('consultant')
            ->with('supporter')
            ->skip($req['start'])
            ->take($req['length']);
        foreach ($students->get() as $index => $item) {
            if ($item->supporters_id) {
                $item->own_purchases = $item->purchases()->where('supporters_id', $item->supporters_id)
                    ->where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->count();
            }
            $item->other_purchases = $item->purchases()->where(function ($query) use ($item) {
                if ($item->supporters_id) $query->where('supporters_id', '!=', $item->supporters_id)->orWhere('supporters_id', 0);
            })->where('is_deleted', false)->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->count();
            $item->today_purchases = $item->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where(function ($query) use ($products_id) {
                if ($products_id != null) $query->where('products_id', $products_id);
            })->where('type', '!=', 'site_failed')->where('type', '!=', 'manual_failed')->where('is_deleted', false)->count();
            $item->save();
        }
        if ($columnName != 'row' && $columnName != "end") {
            $students = $students->orderBy($columnName, $columnSortOrder)
                ->select('students.*')
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        }
        $data = [];
        foreach ($students as $index => $item) {
            $data[] = [
                "row" => $req['start'] + $index + 1,
                "id" => $item->id,
                "first_name" => $item->first_name,
                "last_name" => $item->last_name,
                "other_purchases" => $item->other_purchases,
                "own_purchases" => $item->own_purchases,
                "today_purchases" => $item->today_purchases,
                "supporters_id" => ($item->supporter) ? $item->supporter->first_name . ' ' . $item->supporter->last_name : '-',
                "end" => "<a href='" . route('student_purchases', ['id' => $item->id]) . "'>مشاهده کلیه خریدها</a>"
            ];
        }
        $result = [
            "draw" => $req['draw'],
            "data" => $data,
            "recordsTotal" => count($allStudents),
            "recordsFiltered" => count($allStudents),
        ];

        return $result;
    }

    public function deleteCall($users_id, $id)
    {
        $call = Call::find($id);
        $call->is_deleted = 1;
        try {
            $call->save();
            return redirect()->route('supporter_student_allcall', ["id" => $users_id]);
        } catch (Exception $e) {
            dd($e);
        }
    }
    public function newDeleteCall($users_id, $id)
    {
        $call = Call::find($id);
        //$call = Call::where('id',$id)->where('is_deleted',false)->get();
        $call->is_deleted = 1;
        try {
            $call->save();
            return redirect()->route('user_supporter_acall', ["id" => $users_id]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function calls($id)
    {
        $student = Student::where('id', $id)->where('banned', false)->with('calls.product')->with('calls.product.collection')->with('calls.callresult')->with('calls.notice')->first();
        if ($student->calls)
            foreach ($student->calls as $index => $call) {
                if ($student->calls[$index]->product) {
                    $student->calls[$index]->product->parents = "-";
                    if ($student->calls[$index]->product->collection) {
                        $parents = $student->calls[$index]->product->collection->parents();
                        $name = ($parents != '') ? $parents . "->" . $student->calls[$index]->product->collection->name : $student->calls[$index]->product->collection->name;
                        $student->calls[$index]->product->parents = $name;
                    }
                }
            }
        return view('supporters.call', [
            "student" => $student
        ]);
    }

    public function studentCreate()
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
        if (request()->getMethod() == 'GET') {
            return view('students.create', [
                "supports" => $supports,
                "cities" => $cities,
                "consultants" => $consultants,
                "sources" => $sources,
                "student" => $student
            ]);
        }

        $student->users_id = Auth::user()->id;
        $student->first_name = request()->input('first_name');
        $student->last_name = request()->input('last_name');
        $student->last_year_grade = (int)request()->input('last_year_grade');
        $student->consultants_id = request()->input('consultants_id');
        $student->parents_job_title = request()->input('parents_job_title');
        $student->home_phone = request()->input('home_phone');
        $student->egucation_level = request()->input('egucation_level');
        $student->father_phone = request()->input('father_phone');
        $student->mother_phone = request()->input('mother_phone');
        $student->phone  = request()->input('phone');
        $student->school = request()->input('school');
        $student->average = request()->input('average');
        $student->major = request()->input('major');
        $student->introducing = request()->input('introducing');
        $student->student_phone = request()->input('student_phone');
        $student->sources_id = request()->input('sources_id');
        $student->supporters_id = 0;
        $student->supporter_seen = false;
        try {
            $student->save();
        } catch (Exception $e) {
            // dd($e);
            $msg = "خطا در ثبت رخ داد است";
            if ($e->getCode() == 23000)
                $msg = "شماره تلفن تکراری است!";

            request()->session()->flash("msg_error", $msg);
            return redirect()->route('supporter_student_new');
        }

        request()->session()->flash("msg_success", "دانش آموز با موفقیت افزوده شد.");
        return redirect()->route('supporter_student_new');
    }
    //---------------------AJAX-----------------------------------
    public function call(Request $request)
    {
        $students_id = $request->input('students_id');

        $student = Student::where('id', $students_id)->where('banned', false)->where('is_deleted', false)->first();
        if ($student == null) {
            return [
                "error" => "student_not_found",
                "data" => null
            ];
        }

        $ids = [];
        if ($request->input('products_id') == null) {
            $call = new Call;
            $call->title = 'تماس';
            $call->students_id = $students_id;
            $call->users_id = Auth::user()->id;
            $call->description = $request->input('description');
            $call->call_results_id = $request->input('call_results_id');
            $call->replier = $request->input('replier');
            $call->next_to_call = $request->input('next_to_call');
            $call->next_call = $request->input('next_call');
            $call->notices_id = ($request->input('notices_id') == null) ? 0 : $request->input('notices_id');
            $call->products_id = 0;
            $call->calls_id = $request->input('calls_id');
            try {
                $call->save();
                $ids[] = $call->id;
            } catch (Exception $e) {
                Log::info("error in SupporterController/call when products_id = null " . json_encode($e));
                return [
                    "error" => json_encode($e),
                    "data" => $ids
                ];
            }
        } else {
            foreach ($request->input('products_id') as $products_id) {
                $call = new Call;
                $call->title = 'تماس';
                $call->students_id = $students_id;
                $call->users_id = Auth::user()->id;
                $call->description = $request->input('description');
                $call->call_results_id = $request->input('call_results_id');
                $call->replier = $request->input('replier');
                $call->products_id = $products_id;
                $call->next_to_call = $request->input('next_to_call');
                $call->next_call = $request->input('next_call');
                $call->notices_id = ($request->input('notices_id') == null) ? 0 : $request->input('notices_id');
                $call->calls_id = $request->input('calls_id');
                try {
                    $call->save();
                    $ids[] = $call->id;
                } catch (Exception $e) {
                    Log::info("error in SupporterController/call when products_id != null " . json_encode($e));
                    return [
                        "error" => json_encode($e),
                        "data" => $ids
                    ];
                }
            }
        }

        return [
            "error" => null,
            "data" => $ids
        ];
    }

    public function changePass(Request $request)
    {
        if (!Gate::allows('parameters')) {
            return [
                "error" => "permission denied",
                "data" => null
            ];
        }

        $user_id = $request->input('user_id');

        $user = User::where('id', $user_id)->where('is_deleted', false)->first();
        if ($user == null) {
            return [
                "error" => "user_not_found",
                "data" => null
            ];
        }

        $user->password = Hash::make($request->input('password'));
        $user->pass = $request->input('password');
        $user->save();

        return [
            "error" => null,
            "data" => null
        ];
    }

    public function seen(Request $request)
    {
        if ($request->input('student_id') == null) {
            return [
                "error" => "InvalidInput",
                "data" => null
            ];
        }

        $student = Student::find($request->input('student_id'));
        if ($student == null) {
            return [
                "error" => "StudentNotFound",
                "data" => null
            ];
        }

        $student->supporter_seen = true;
        $student->save();
        return [
            "error" => null,
            "data" => $student
        ];
    }
    public function showIncome()
    {
        $from_date = null;
        $to_date = null;
        return view('supporters.income')->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }
    public function showIncomePost(Request $request)
    {
        $purchases = Purchase::where('is_deleted', false)->where('price', '!=', 0)->where('supporters_id', Auth::user()->id);
        $thePurchases = $purchases;
        $wage = [];
        $sum = 0;
        if ($request->input('from_date') && $request->input('to_date') && $request->input('from_date') == $request->input('to_date')) {
            $purchases = $purchases->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('created_at', '<=', date("Y-m-d 23:59:59"));
        } else {
            if ($request->input('from_date')) {
                $from_date = $this->jalaliToGregorian($request->input('from_date'));
                $purchases = $purchases->where('created_at', '>=', $from_date);
            }
            if ($request->input('to_date')) {
                $to_date = $this->jalaliToGregorian($request->input('to_date'));
                $purchases = $purchases->where('created_at', '<=', $to_date);
            }
        }
        $allPurchases = $purchases->get();
        $out = CommissionPurchaseRelation::computeMonthIncome($thePurchases, $allPurchases);
        $sum = $out[0];
        $wage = $out[1];
        $default_wage = $out[2];
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
        if ($columnName != 'row' && $columnName != "wage" && $columnName != "portion") {
            $purchases = $purchases->orderBy($columnName, $columnSortOrder)
                ->select('purchases.*')
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        } else {
            $purchases = $purchases->select('purchases.*')
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        }
        $data = [];
        foreach ($purchases as $index => $item) {
            $data[] = [
                "row" => $req['start'] + $index + 1,
                "id" => $item->id,
                "students_id" => ($item->student) ? ($item->student->first_name . ' ' . $item->student->last_name) : '-',
                "created_at" => ($item->created_at) ? jdate($item->created_at)->format('Y-m-d') : jdate()->format("Y-m-d"),
                "products_id" => ($item->product) ? $item->product->name : '-',
                "price" => number_format($item->price),
                "wage" => isset($wage[$item->products_id]) ? $wage[$item->products_id] : $default_wage,
                "portion" => isset($wage[$item->products_id]) ? number_format(($wage[$item->products_id]) / 100 * $item->price) : number_format($default_wage / 100 * $item->price),
            ];
        }
        $result = [
            "draw" => $req['draw'],
            "data" => $data,
            "sum" => number_format($sum),
            "recordsTotal" => count($allPurchases),
            "recordsFiltered" => count($allPurchases),
        ];

        return $result;
    }
    public function mergeStudents()
    {
        $user = Auth::user()->id;
        $supporter_students = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', $user)->pluck('id');
        $mergedStudents = AppMergeStudents::where('is_deleted', false)
            ->where(function ($query) use ($supporter_students) {
                $query->whereIn('main_students_id', $supporter_students)
                    ->orWhereIn('second_auxilary_students_id', $supporter_students)
                    ->orWhereIn('third_auxilary_students_id', $supporter_students)
                    ->orWhereIn('auxilary_students_id', $supporter_students);
            })->get();
        return view('supporters.mergeStudents', [
            'mergedStudents' => $mergedStudents,
        ]);
    }
    public function supporterHistories()
    {
        $supportGroupId = Group::getSupport();
        if ($supportGroupId) {
            $supportGroupId = $supportGroupId->id;
        }
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        $name = null;
        $phone = null;
        $user_name = null;
        return view('supporters.supporter-histories')->with([
            'supports' => $supports,
            'name' => $name,
            'phone' => $phone,
            'user_name' => $user_name
        ]);
    }
    public function supporterHistoriesPost()
    {

        $supporterHistories = SupporterHistory::select('supporter_histories.*');
        $student_ids = SupporterHistory::pluck('students_id');
        if (request()->input('supporters_id') != null) {
            $supporters_id = (int)request()->input('supporters_id');
            $supporterHistories = $supporterHistories->where('supporter_histories.supporters_id', $supporters_id);
        }
        if (request()->input('name') != null) {
            $name = trim(request()->input('name'));
            $student = Student::where(DB::raw("CONCAT(IFNULL(first_name, ''), IFNULL(CONCAT(' ', last_name), ''))"), 'like', '%' . $name . '%')->whereIn('id', $student_ids)->pluck('id');
            $supporterHistories = $supporterHistories->whereIn('students_id', $student);
        }
        if (request()->input('phone') != null) {
            $phone = request()->input('phone');
            $student = Student::select('id', 'phone')->where('phone', 'like', '%' . $phone . '%')->whereIn('id', $student_ids)->pluck('id');
            $supporterHistories = $supporterHistories->whereIn('students_id', $student);
        }
        if (request()->input('user_name') != null) {
            $user_name = request()->input('user_name');
            $user = User::select('id', 'first_name', 'last_name', DB::raw("CONCAT(first_name,' ',last_name)"))
                ->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $user_name . '%')->pluck('id');
            $supporterHistories = $supporterHistories->whereIn('users_id', $user);
        }
        $count = $supporterHistories->count();
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

        if ($columnName != 'row' && $columnName != 'first_name' && $columnName != 'last_name' && $columnName != 'phone') {
            $supporterHistories = $supporterHistories->orderBy("supporter_histories." . $columnName, $columnSortOrder)
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        } else if ($columnName == "first_name" || $columnName == "last_name" || $columnName == "phone") {
            $supporterHistories = $supporterHistories->orderBy("supporter_histories.students_id", $columnSortOrder)
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        } else {
            $supporterHistories = $supporterHistories->select('students.*')
                ->skip($req['start'])
                ->take($req['length'])
                ->get();
        }
        $data = [];
        foreach ($supporterHistories as $index => $item) {
            $data[] = [
                "row" => $req['start'] + $index + 1,
                "id" => $item->id,
                "first_name" => ($item->student) ? $item->student->first_name : '-',
                "last_name" => ($item->student) ? $item->student->last_name : '-',
                "phone" => ($item->student) ? $item->student->phone : '-',
                "users_id" => ($item->user) ? $item->user->first_name . ' ' . $item->user->last_name : '-',
                "supporters_id" => ($item->supporter) ? $item->supporter->first_name . ' ' . $item->supporter->last_name : '',
                "created_at" => $item->created_at ? jdate($item->created_at)->format("Y/m/d") : jdate()->format("Y/m/d")
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
