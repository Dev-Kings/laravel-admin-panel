<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class YearlyPresence extends Component
{
    public $selectedYear;
    public $thisYearData;
    public $thisYearTotal;

    public function mount()
    {
        $this->selectedYear = date('Y');
        $this->updateDataCount();
    }

    public function updateDataCount()
    {
        $this->thisYearData = DB::table('employee_present')
            ->whereYear('date', $this->selectedYear)
            ->selectRaw('month(date) as month')
            ->selectRaw('day(date) as day')
            ->selectRaw('SUM(total) as total')
            ->groupBy('month', 'day')
            ->orderBy('month')
            ->pluck('total', 'month')
            // ->values()
            ->toArray();

        // dd($this->thisYearData);

        $total = DB::table('employee_present')
            ->whereYear('date', $this->selectedYear)
            ->selectRaw('month(date) as month')
            ->selectRaw('day(date) as day')
            ->selectRaw('SUM(total) as total')
            ->groupBy('month', 'day')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $this->thisYearTotal = !$total == 0 ? array_sum($total) : 0;

        $this->emit('updateTheChart');
    }

    public function render()
    {
        $years = [
            date('Y'), date('Y') - 1, date('Y') - 2, date('Y') - 3,
        ];

        return view(
            'livewire.yearly-presence',
            [
                'years' => $years,
            ]
        );
    }
}
