<x-chart-layout>
    <div class="bg-white rounded-md my-2 px-2 py-2 mx-24">
        <div>
            <h2 class="text-2xl font-semibold">Monthly Chart</h2>
            <livewire:monthly-presence />
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-chart-layout>