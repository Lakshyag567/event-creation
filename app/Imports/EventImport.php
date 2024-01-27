<?php

namespace App\Imports;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EventImport implements ToCollection, WithHeadingRow, WithValidation
{

    private int $rowCount = 0;

    public function rules(): array
    {
        return [
            '*.title' => 'required',
            '*.description' => 'required',
            '*.start_date' => 'required',
            '*.end_date' => 'required',
            '*.image' => 'required',
        ];
    }

    public function collection(Collection $collection): void
    {
        $rows = $collection->toArray();
        $rows = json_decode(json_encode($rows));

        foreach ($rows as $index => $row) {
            // dd(date('Y-m-d', strtotime($row->start_date)));
            $event = Event::create([
                'title' => $row->title,
                'description' => $row->description,
                'start_date' => date('Y-m-d', strtotime($row->start_date)),
                'end_date' => date('Y-m-d', strtotime($row->end_date)),
                'image' => $row->image
            ]);
            if ($event) {
                $this->rowCount++;
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
