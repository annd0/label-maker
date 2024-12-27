<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabelExport implements FromArray, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $labels = [];
        $rowIndex = 0;

        foreach ($this->data[0] as $index => $row) {
            // Group into sets of 3 labels per row
            if ($index % 3 === 0) {
                $rowIndex++;
            }

            $labels[$rowIndex][] = [
                'First Name' => $row[4],
                'Last Name' => $row[5],
            ];
        }

        return $labels;
    }

    public function styles(Worksheet $sheet)
    {
        // Apply custom styles, like alignment or borders, if needed
        return [
            'A1:Z1000' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
