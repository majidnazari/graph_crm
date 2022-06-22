<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Student;
use App\Group;
use App\User;
use App\Call;
use App\CallResult;
use App\Commission;
use App\Utils\CommissionPurchaseRelation;
use Carbon\Carbon;
use Morilog\Jalali\CalendarUtils;
use App\Http\Traits\AllTypeCallsTrait;
use App\Purchase;
use App\Sanad;

class DashboardController extends Controller
{
    use AllTypeCallsTrait;

    public static function fixDateDigits($inp){
        $out = explode('-', $inp);
        if(count($out)!=3){
            return $inp;
        }
        if((int)$out[1]<10){
            $out[1] = '0' . $out[1];
        }
        if((int)$out[2]<10){
            $out[2] = '0' . $out[2];
        }
        return implode('-', $out);
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
        $pdate = explode('-', DashboardController::persianToEnglishDigits($pdate));
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
            $gregorian = $gregorian[0] . "-" . ($gregorian[1] < 10 ? '0'.$gregorian[1]: $gregorian[1]) . "-" . ($gregorian[2] < 10 ? '0'.$gregorian[2]: $gregorian[2]);
        }
        return $gregorian;
    }
    public static function gregorianToJalali($Edate){
        $Edate = explode('-', $Edate);
        $date = "";
        if (count($Edate) == 3) {
            $y = (int)$Edate[0];
            $m = (int)$Edate[1];
            $d = (int)$Edate[2];
            if ($d > $y) {
                $tmp = $d;
                $d = $y;
                $y = $tmp;
            }
            $y = (($y < 1000) ? $y + 1300 : $y);
            $jalali = CalendarUtils::toJalali($y, $m, $d);
            $jalali = $jalali[0] . "/" . $jalali[1] . "/" . $jalali[2];
        }
        return $jalali;

    }
    public function toPersianNum($number)
    {
        $number = str_replace("1","۱",$number);
        $number = str_replace("2","۲",$number);
        $number = str_replace("3","۳",$number);
        $number = str_replace("4","۴",$number);
        $number = str_replace("5","۵",$number);
        $number = str_replace("6","۶",$number);
        $number = str_replace("7","۷",$number);
        $number = str_replace("8","۸",$number);
        $number = str_replace("9","۹",$number);
        $number = str_replace("0","۰",$number);
        return $number;
    }
    public function index(){
        $user = Auth::user();
        $group = $user->group()->first();
        $gates = $group->gates()->where('key', 'marketers')->get();
        if(count($gates)>0){
            return redirect()->route('marketerdashboard');
        }
        $gates = $group->gates()->where('key', 'supporters')->get();
        if(count($gates)>0){
            $sum = 0;
            $count_all_missed_calls = $this->missed_calls()['count'];
            $count_yesterday_missed_calls = $this->yesterday_missed_calls()['count'];
            $count_of_no_need_calls = $this->no_need_calls_students()['count'];
            $recalls = Call::where('calls_id', '!=', null)->pluck('calls_id');
            $calls = Call::where('next_call', '!=', null)->where('users_id', Auth::user()->id)->whereNotIn('id', $recalls);
            $calls = $calls->get();
            $arrayOfReminders = [];
            $todayCount = $calls->where('next_call', '>=', Carbon::now()->addDays(0)->toDateString().' '."00:00:00")->where('next_call', '<=', Carbon::now()->addDays(0)->toDateString().' '."23:59:59")->count();
            for($i = 1;$i < 10; $i++){
                $arrayOfReminders[$i] = $calls->where('next_call', '>=', Carbon::now()->addDays($i)->toDateString().' '."00:00:00")->where('next_call', '<=', Carbon::now()->addDays($i)->toDateString().' '."23:59:59")->count();
            }
            $newStudents = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', Auth::user()->id)->where('supporter_seen', false)->count();
            $year = (int)jdate()->format("Y");
            $month = (int)jdate()->format("m");
            $startOfYear = implode('-', CalendarUtils::toGregorian($year, 1, 1));

            $dates = [$startOfYear];
            $currentMonth = 2;
            do{
                $dates[] = implode('-', CalendarUtils::toGregorian($year, $currentMonth, 1));
                $currentMonth++;
            }while($currentMonth <= $month);
            $dates[] = date("Y-m-d");
            $results = [];
            for($i = 0;$i < count($dates)-1;$i++){
                $results[] = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', Auth::user()->id)->where('created_at', '>=', DashboardController::fixDateDigits($dates[$i]) . ' 00:00:00')->where('created_at', '<', DashboardController::fixDateDigits($dates[$i+1]) . ' 00:00:00')->count();
            }
            $yesterday_recalls = Call::where('users_id', Auth::user()->id)->where('next_call', ">=", date("Y-m-d 00:00:00", strtotime("yesterday")))->where('next_call', "<=", date("Y-m-d 23:59:59", strtotime("yesterday")))->get();
            $first_day_of_this_month = jdate()->format('Y').'-'.jdate()->format('n').'-01';
            $last_day_of_this_month = jdate()->format('Y-n-t');
            $gregorian_first_day_of_this_month = $this->jalaliToGregorian($first_day_of_this_month);
            $gregorian_last_day_of_this_month = $this->jalaliToGregorian($last_day_of_this_month);
            // $purchases = Purchase::where('is_deleted',false)->where('supporters_id',Auth::user()->id)->where('created_at','>=',$gregorian_first_day_of_this_month)->where('created_at','<=',$gregorian_last_day_of_this_month);
            // $thePurchases = Purchase::where('is_deleted',false)->where('supporters_id',Auth::user()->id)->where('created_at','>=',$gregorian_first_day_of_this_month)->where('created_at','<=',$gregorian_last_day_of_this_month)->get();
            $supporterSanads = Sanad::where('supporter_id', Auth::user()->id)
                ->where('status', 'created')
                ->where('created_at', '>=', $gregorian_first_day_of_this_month)
                ->where('created_at', '<=', $gregorian_last_day_of_this_month)
                ->get();
            $user = User::where('is_deleted',false)->where('id',Auth::user()->id)->first();
            // $out = CommissionPurchaseRelation::computeMonthIncome($purchases,$thePurchases);
            // $sum = $out[0];
            $sum = 0;
            for ($indx = 0; $indx < count($supporterSanads); $indx++) {
                if($supporterSanads[$indx]->type>0)
                    $sum += ceil($supporterSanads[$indx]->total) ;//* $supporterSanads[$indx]->supporter_percent / 100) * $supporterSanads[$indx]->type;
            }
            $sum = $this->toPersianNum(number_format($sum));
            return view('dashboard.support', [
                'sum' => $sum,
                'newStudents'=>$newStudents,
                'results'=>$results,
                'todayCount' => $todayCount,
                'arrOfReminders' => $arrayOfReminders,
                'count_no_need_calls_students' => $count_of_no_need_calls,
                'count_of_all_missed_calls' =>  $count_all_missed_calls,
                'count_of_yesterday_missed_calls' => $count_yesterday_missed_calls,
                'yesterday_recalls'=>$yesterday_recalls
            ]);
        }

        $devideStudents = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', 0)->count();
        $todayStudents = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('supporters_id', 0)->where('created_at', '>=', date('Y-m-d 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'))->count();
        $year = (int)jdate()->format("Y");
        $month = (int)jdate()->format("m");
        $startOfYear = implode('-', CalendarUtils::toGregorian($year, 1, 1));

        $dates = [$startOfYear];
        $currentMonth = 2;
        do{
            $dates[] = implode('-', CalendarUtils::toGregorian($year, $currentMonth, 1));
            $currentMonth++;
        }while($currentMonth <= $month);
        $dates[] = date("Y-m-d");
        $results = [];
        for($i = 0;$i < count($dates)-1;$i++){
            $results[] = Student::where('is_deleted', false)->where('banned', false)->where('archived', false)->where('created_at', '>=', DashboardController::fixDateDigits($dates[$i]) . ' 00:00:00')->where('created_at', '<', DashboardController::fixDateDigits($dates[$i+1]) . ' 00:00:00')->count();
        }
        $supportGroupId = Group::getSupport();
        $supporters = [];
        if($supportGroupId){
            $supportGroupId = $supportGroupId->id;
            $supporters = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->with('students')->get()->sortBy(function($hackathon)
            {
                return $hackathon->students->count();
            }, SORT_REGULAR, true);
        }
        return view('dashboard.admin', [
            'devideStudents'=>$devideStudents,
            'todayStudents'=>$todayStudents,
            'results'=>$results,
            'supporters'=>$supporters
        ]);
    }
}
