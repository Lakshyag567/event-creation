<?php

namespace App\Imports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EventImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Event([
            'title' => $row['title'],
            'description' => $row['description'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'image' => $row['image_path']
        ]);
    }

    public function rules(): array
    {
        return [
            '*.title' => 'required',
            '*.description' => 'required',
            '*.start_date' => 'required|date',
            '*.end_date' => 'required|date',
            '*.image' => 'required',
        ];
    }
}
