<?php

namespace App\Imports;

use App\Models\Tuition;
use Maatwebsite\Excel\Concerns\ToModel;

class TuitionsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Tuition([
            //
        ]);
    }
}
