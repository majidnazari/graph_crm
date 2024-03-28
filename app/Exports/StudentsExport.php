<?php

namespace App\Exports;

use App\City;
use App\Province;
use App\Source;
use App\Student;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Morilog\Jalali\CalendarUtils;
use Illuminate\Support\Facades\DB;

class StudentsExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping
{
    protected $students_select, $from_date, $to_date, $education_level,$concours_year, $major, $supporters_id;

    function __construct($students_select, $supporters_id, $major, $education_level,$concours_year, $from_date = null, $to_date = null)
    {
        $this->students_select = $students_select;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->education_level = $education_level;
        $this->concours_year = $concours_year;
        
        $this->major = $major;
        $this->supporters_id = $supporters_id;
    }
    /**
     * @var Student $student
     */
    public function map($student): array
    {
        $consultant = User::where('id', $student->consultants_id)->first();
        $source = Source::where('id', $student->sources_id)->first();
        $supporter = User::where('id', $student->supporters_id)->first();
        $user = User::where('id', $student->users_id)->first();
        $marketer = User::where('id', $student->marketers_id)->first();
        $province = Province::where('id', $student->provinces_id)->first();
        $city = City::where('id', $student->cities_id)->first();
        switch ($student->major) {
            case 'experimental':
                $student->major = 'تجربی';
                break;
            case 'mathematics':
                $student->major = 'ریاضی';
                break;
            case 'other':
                $student->major = 'دیگر';
                break;
            case 'humanities':
                $student->major = 'انسانی';
                break;
            case 'art':
                $student->major = 'هنر';
                break;
        }
        if ($student->egucation_level == 13) {
            $student->egucation_level = 'فارغ التحصیل';
        } else if ($student->egucation_level == 14) {
            $student->egucation_level = 'دانشجو';
        }
        return [
            $student->id,
            $student->first_name,
            $student->last_name,
            $student->last_year_grade,
            $student->consultants_id = $consultant ? $consultant->first_name . ' ' . $consultant->last_name : '',
            $student->parents_job_title,
            $student->home_phone,
            $student->father_phone,
            $student->mother_phone,
            $student->phone,
            $student->school,
            $this->gregorianToJalali($student->created_at->format('Y-m-d')),
            $student->introducing,
            $student->student_phone,
            $student->sources_id = $source ? $source->name : '',
            $student->supporters_id = $supporter ? $supporter->first_name . ' ' . $supporter->last_name : '',
            $student->users_id = $user ? $user->first_name . ' ' . $user->last_name : '',
            $student->marketers_id = $marketer ? $marketer->first_name . ' ' . $marketer->last_name : '',
            $student->average,
            $student->password,
            $student->viewed = $student->viewed == 1 ? 'بله' : 'خیر',
            $student->major,
            $student->egucation_level,
            $student->concours_year,
            $student->provinces_id = $province ? $province->name : '',
            $student->is_from_site = $student->is_from_site == 1 ? 'بله' : 'خیر',
            $student->description,
            $student->supporter_seen = $student->supporter_seen == 1 ? 'بله' : 'خیر',
            $student->saloon,
            $student->supporter_start_date ? $this->gregorianToJalali($student->supporter_start_date) : '',
            $student->banned = $student->banned == 1 ? 'بله' : 'خیر',
            $student->cities_id = $city ? $city->name : '',
            $student->archived = $student->archived == 1 ? 'بله' : 'خیر',
            $student->outside_consultants,
            strval($student->own_purchases),
            strval($student->other_purchases),
            strval($student->today_purchases)
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 15,
            'C' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 25,
            'N' => 15,
            'O' => 15,
            'P' => 15,
            'Q' => 20,
            'R' => 20,
            'S' => 10,
            'T' => 15,
            'U' => 10,
            'X' => 15,
            'Y' => 10,
            'Z' => 50,
            'AA' => 20,
            'AB' => 20,
            'AC' => 15,
            'AD' => 10,
            'AF' => 10,
            'AG' => 20,
            'AH' => 25,
            'AI' => 25,
            'AJ' => 25,
        ];
    }
    public function headings(): array
    {
        $array = [
            'کد',
            'نام',
            'نام خانوادگی',
            'رتبه',
            'مشاور',
            'عنوان شغل والدین',
            'تلفن خانه',
            'تلفن پدر',
            'تلفن مادر',
            'تلفن',
            'مدرسه',
            'تاریخ ایجاد',
            'معرفی شده توسط',
            'تلفن دانش آموز',
            'منبع',
            'پشتیبان',
            'ایجادکننده',
            'بازاریاب',
            'معدل',
            'رمز عبور',
            'مشاهده شده',
            'رشته',
            'مقطع تحصیلی',
            'سال کنکور',
            'استان',
            'از طریق سایت',
            'توصیف',
            'مشاهده شده توسط پشتیبان',
            'سالن',
            'تاریخ شروع پشتیبان',
            'لغو شده',
            'شهر',
            'آرشیو شده',
            'مشاورهای بیرونی',
            'تعداد خریدها بعد از تخصیص به پشتیبان',
            'تعداد خریدها قبل از تخصیص به پشتیبان',
            'تعداد خریدهای امروز'
        ];
        return $array;
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
        $pdate = explode('/', StudentsExport::persianToEnglishDigits($pdate));
        $date = "";
        if (count($pdate) == 3) {
            $y = (int)$pdate[0];
            $m = (int)$pdate[1];
            $d = (int)$pdate[2];
            //for switch you don't need tmp variable two variable is enogh
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
    public static function gregorianToJalali($Edate)
    {
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
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $students = Student::where('is_deleted', false)->exclude(['updated_at', 'is_deleted']);
        if ($this->students_select != null) {
            if ($this->students_select == 'archive_students') {
                $students = $students->where('archived', true);
            } elseif ($this->students_select == 'black_students') {
                $students = $students->where('banned', true);
            }
        }
        if ($this->students_select == "all") {
            $this->from_date = null;
            $this->to_date = null;
        }
        if ($this->supporters_id != null) {
            $supporters_id = (int)$this->supporters_id;
            $students = $students->where('supporters_id', $supporters_id);
        }

        $egucation_level = $this->education_level;
        if ($this->education_level != null) {
            $students = $students->where('egucation_level', $egucation_level);
        }

        if ($this->major != null) {
            $major = $this->major;
            $students = $students->where('major', $major);
        }
        if ($this->concours_year != null) {
            $concours_year = $this->concours_year;
            $students = $students->where('concours_year', $concours_year);
        }
        if ($this->from_date != null) {
            $from_date = $this->jalaliToGregorian($this->from_date);
            if ($from_date != '') $students = $students->where('created_at', '>=', $from_date);
        }
        if ($this->to_date != null) {
            $to_date = $this->jalaliToGregorian($this->to_date);
            if ($to_date != '') $students = $students->where('created_at', '<=', $to_date);
        }
        $students = $students->get();
        if (!empty($students)) {
            return $students;
        }
        return [];
    }
}
