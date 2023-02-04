<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MonthlyPresence extends Component
{
    public $selectedYear;
    public $selectedMonth;
    public $thisMonthData;
    public $thisMonthTotal;

    public function mount()
    {
        $this->selectedYear = date('Y');
        $this->selectedMonth = date('m');
        $this->updateDataCount();
    }

    public function updateDataCount()
    {
        $this->thisMonthData = DB::table('employee_present')
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth)
            ->selectRaw('day(date) as day')
            ->selectRaw('SUM(total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            // ->values()
            ->toArray();

        $total = DB::table('employee_present')
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth)
            ->selectRaw('day(date) as day')
            ->selectRaw('SUM(total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $this->thisMonthTotal = !$total == 0 ? max($total) : 0;

        $this->emit('updateTheChart');
    }

    public function render()
    {
        $years = [
            date('Y'), date('Y') - 1, date('Y') - 2, date('Y') - 3,
        ];

        return view(
            'livewire.monthly-presence',
            [
                'years' => $years,
            ]
        );
    }
}
