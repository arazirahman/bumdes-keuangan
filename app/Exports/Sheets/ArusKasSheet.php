<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ArusKasSheet implements FromArray, WithTitle
{
    public function __construct(private array $r) {}

    public function title(): string
    {
        return 'Arus Kas';
    }

    public function array(): array
    {
        return [
            ['Periode', $this->r['month']],
            [],
            ['Uang Masuk', $this->r['uang_masuk']],
            ['Uang Keluar', $this->r['uang_keluar']],
            ['Saldo Akhir', $this->r['saldo_akhir']],
        ];
    }
}
