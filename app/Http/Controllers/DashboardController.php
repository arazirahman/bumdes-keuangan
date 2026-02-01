<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m')); // YYYY-MM
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $income = Transaction::whereBetween('trx_date', [$start, $end])
            ->where('type', 'income')
            ->sum('amount');

        $expense = Transaction::whereBetween('trx_date', [$start, $end])
            ->where('type', 'expense')
            ->sum('amount');

        // saldo sampai hari ini
        $totalIncomeAll = Transaction::where('type', 'income')->sum('amount');
        $totalExpenseAll = Transaction::where('type', 'expense')->sum('amount');
        $saldo = $totalIncomeAll - $totalExpenseAll;

        return view('dashboard', [
            'title' => 'Dashboard',
            'month' => $month,
            'income' => $income,
            'expense' => $expense,
            'profit' => $income - $expense,
            'saldo' => $saldo,
        ]);
    }
}
