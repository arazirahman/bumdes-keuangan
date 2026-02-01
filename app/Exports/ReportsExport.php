<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    public function __construct(private array $report) {}

    public function sheets(): array
    {
        return [
            new Sheets\LabaRugiSheet($this->report),
            new Sheets\ArusKasSheet($this->report),
            new Sheets\NeracaSheet($this->report),
        ];
    }
}
