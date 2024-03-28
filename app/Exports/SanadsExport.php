<?php

namespace App\Exports;

use App\Sanad;
use Maatwebsite\Excel\Concerns\FromCollection;

class SanadsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Sanad::all();
    }
}
