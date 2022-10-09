<?php

namespace App\Imports;

use App\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImport implements ToModel, WithChunkReading, ShouldQueue
{
    use Importable;
    public $fails = [];
    public $source_id = null;
    public $concours_year = null;
    

    public function chunkSize(): int
    {
        return 1000;
    }
    public function perToEn($inp)
    {
        $inp = str_replace(["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"], ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"], $inp);
        $inp = str_replace(["١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩", "٠"], ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"], $inp);
        return $inp;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $majors = [
            'ریاضی' => 'mathematics',
            'تجربی' => 'experimental',
            'انسانی' => 'humanities',
            'هنر' => 'art',
            'غیره' => 'other',
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
            'دانشجو' => '14',
        ];
        if((int)$this->perToEn($row[0])==0){
            return null;
        }

        $educationLevel = $row[3];
        if (!isset($educationLevels[$educationLevel]) && isset($educationLevelsInPersian[$educationLevel])) {
            $educationLevel = $educationLevelsInPersian[$educationLevel];
        } else if (!isset($educationLevels[$educationLevel]) && !isset($educationLevelsInPersian[$educationLevel])) {
            $educationLevel = null;
        }

        $student = Student::where('phone', ((strpos($this->perToEn($row[0]), '0') !== 0) ? '0' : '') . $this->perToEn($row[0]))->first();
        if ($student === null) {
            $st = [
                'phone' => ((strpos($this->perToEn($row[0]), '0') !== 0) ? '0' : '') . $this->perToEn($row[0]),
                'first_name' => $row[1],
                'last_name' => $row[2],
                'egucation_level' => $educationLevel,
                'parents_job_title' => $row[4],
                'home_phone' => $row[5],
                'father_phone' => $row[6],
                'mother_phone' => $row[7],
                'school' => $row[8],
                'average' => $row[9],
                'major' => ($row[10] != '' && strtoupper($row[10]) != 'NULL' && isset($majors[$row[10]])) ? $majors[$row[10]] : null,
                'introducing' => $row[11],
                'student_phone' => $row[12],
                'cities_id' => (int) $row[13],
                'supporters_id' => $row[15] ? $row[15] : 0
            ];
            $source_id = $row[14];
            if (!$source_id) {
                $source_id = $this->source_id;
            }

            if ($source_id) {
                $st['sources_id'] = $source_id;
            }

            if ($this->concours_year) {
                $st['concours_year'] = $this->concours_year;
            }
            return new Student($st);
        } else {
            Log::info("fail:" . $student->phone);
            $this->fails[] = $student->phone;
            Log::info("fails:" . json_encode($this->fails));

        }
        return $student;
    }

    public function getFails()
    {
        return $this->fails;
    }
}
