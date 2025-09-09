<?php

namespace App\Imports;

use App\Models\EnrolledStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EnrolledStudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (EnrolledStudent::where('id_number', $row['id_number'])->exists()) {
            return null;
        }
        return new EnrolledStudent([
            'id_number'   => $row['id_number'],
            'last_name'   => $row['last_name'],
            'first_name'  => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'birth_day'   => $row['birth_day'],
            'course'      => $row['course'],
            'gender'      => $row['gender'],
        ]);
    }
}
