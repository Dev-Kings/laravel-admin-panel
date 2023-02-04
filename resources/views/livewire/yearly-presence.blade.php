<div 
    wire:ignore
    class="mt-1"
    x-data="{
        selectedYear: @entangle('selectedYear'),
        thisYearData: @entangle('thisYearData'),
        thisYearTotal: @entangle('thisYearTotal'),
        init(){
            const labels = [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
                '11', '12',
            ];

            const data = {
                labels: labels,
                datasets: [{
                    label: `${this.selectedYear} Data`,
                    backgroundColor: 'lightgreen',
                    data: this.thisYearData,
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
                myChart.data.datasets[0].label = `${this.selectedYear} Data`;
                myChart.data.datasets[0].data = this.thisYearData;

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
    </div>
    <div class="my-4">
        <div>
            <span x-text="selectedYear"></span> Data: Kshs.
            <span x-text="thisYearTotal"></span>
        </div>
    </div>
    <canvas id="myChart" x-ref="canvas"></canvas>
</div>