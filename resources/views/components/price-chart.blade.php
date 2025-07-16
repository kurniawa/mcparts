<div class="border p-2">
    <div class="text-center">
        <h2 class="font-bold">Grafik Harga Barang</h2>
    </div>
    <canvas id="priceChart" height="100" class="mt-5"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($priceChartData->pluck('created_at')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M Y'))) !!},
                datasets: [{
                    label: 'Harga',
                    data: {!! json_encode($priceChartData->pluck('price')) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    </script>
</div>
