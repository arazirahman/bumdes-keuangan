<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class LabaRugiSheet implements FromArray, WithTitle
{
    public function __construct(private array $r) {}

    public function title(): string
    {
        return 'Laba Rugi';
    }

    public function array(): array
    {
        return [
            ['Periode', $this->r['month']],
            [],
            ['Pendapatan', $this->r['pendapatan']],
            ['Biaya', $this->r['biaya']],
            ['Laba Bersih', $this->r['laba_bersih']],
        ];
    }
}
