<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class NeracaSheet implements FromArray, WithTitle
{
    public function __construct(private array $r) {}

    public function title(): string
    {
        return 'Neraca';
    }

    public function array(): array
    {
        return [
            ['Periode', $this->r['month']],
            [],
            ['Kas', $this->r['kas']],
            ['Aset Lain', $this->r['aset_lain']],
            ['Total Aset', $this->r['total_aset']],
            [],
            ['Modal', $this->r['modal']],
        ];
    }
}
