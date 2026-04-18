<?php

namespace App\Exports;

use App\Models\Tuition;
use Maatwebsite\Excel\Concerns\FromCollection;

class TuitionsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Tuition::all();
    }
}
