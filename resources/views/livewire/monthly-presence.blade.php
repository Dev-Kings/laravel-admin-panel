<div 
    wire:ignore
    class="mt-1"
    x-data="{
        selectedYear: @entangle('selectedYear'),
        selectedMonth: @entangle('selectedMonth'),
        thisMonthData: @entangle('thisMonthData'),
        thisMonthTotal: @entangle('thisMonthTotal'),
        init(){
            const labels = [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
                '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
                '21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
                '31',
            ];

            const data = {
                labels: labels,
                datasets: [{
                    label: `${this.selectedMonth} Data`,
                    backgroundColor: 'lightgreen',
                    data: this.thisMonthData,
                }]
            };
            const config = {
                type: 'bar',
                data: data,
                options: {}
            };

            const myChart = new Chart(
                this.$refs.canvas,
                config
            );

            Livewire.on('updateTheChart', () => {
                myChart.data.datasets[0].label = `${this.selectedMonth} Data`;
                myChart.data.datasets[0].data = this.thisMonthData;

                myChart.update();
            })
        }
    }"
>
    <div class="inline-flex space-x-4">
        <div>
            <span>Year: </span>
            <select name="selectedYear" id="selectedYear" class="border" wire:model="selectedYear"
            wire:change="updateDataCount">
                @foreach ($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <span>Month: </span>
            <select name="selectedMonth" id="selectedMonth" class="border" wire:model="selectedMonth"
            wire:change="updateDataCount">
                @foreach(Carbon\CarbonPeriod::create(now()->startOfMonth(), '1 month',
                now()->addMonths(11)->startOfMonth()) as $date)
                <option value="{{ $date->format('m') }}">
                    {{ $date->format('F') }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="my-4">
        <div>
            Month <span x-text="selectedMonth"></span> Data: Kshs.
            <span x-text="thisMonthTotal"></span>
        </div>
    </div>
    <canvas id="myChart" x-ref="canvas"></canvas>
</div>