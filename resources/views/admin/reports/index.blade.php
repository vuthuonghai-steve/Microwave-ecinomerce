@extends('admin.layout')
@section('title','Reports')
@section('content')
<h1 class="text-xl font-semibold mb-4">Báo cáo</h1>

<div class="bg-white rounded shadow p-4 mb-4 grid md:grid-cols-6 gap-3">
  <select id="range" class="border rounded px-3 py-2">
    <option value="daily" selected>Theo ngày</option>
    <option value="weekly">Theo tuần</option>
    <option value="monthly">Theo tháng</option>
  </select>
  <input id="from" type="date" class="border rounded px-3 py-2" />
  <input id="to" type="date" class="border rounded px-3 py-2" />
  <button id="applyFilters" class="bg-blue-600 text-white px-4 py-2 rounded">Áp dụng</button>
  <a id="exportRevenue" class="bg-gray-800 text-white px-4 py-2 rounded text-center" href="#">CSV doanh thu</a>
  <a id="exportRevenueXlsx" class="bg-gray-800 text-white px-4 py-2 rounded text-center" href="#">XLSX doanh thu</a>
  <a id="exportBest" class="bg-gray-800 text-white px-4 py-2 rounded text-center" href="#">CSV bán chạy</a>
  <a id="exportBestXlsx" class="bg-gray-800 text-white px-4 py-2 rounded text-center" href="#">XLSX bán chạy</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">Doanh thu theo thời gian</h2>
    <canvas id="revenueChart" height="120"></canvas>
  </div>
  <div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">Số đơn theo thời gian</h2>
    <canvas id="ordersChart" height="120"></canvas>
  </div>
  <div class="bg-white rounded shadow p-4 lg:col-span-2">
    <h2 class="font-semibold mb-2">Overlay: Doanh thu vs Số đơn</h2>
    <canvas id="overlayChart" height="120"></canvas>
  </div>
  <div class="bg-white rounded shadow p-4 lg:col-span-2">
    <h2 class="font-semibold mb-2">Top sản phẩm bán chạy</h2>
    <canvas id="bestChart" height="120"></canvas>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const revenueCtx = document.getElementById('revenueChart').getContext('2d');
  const ordersCtx = document.getElementById('ordersChart').getContext('2d');
  const bestCtx = document.getElementById('bestChart').getContext('2d');
  const overlayCtx = document.getElementById('overlayChart').getContext('2d');
  let revenueChart, ordersChart, bestChart, overlayChart;

  function buildQuery() {
    const range = document.getElementById('range').value;
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;
    const params = new URLSearchParams({range});
    if (from) params.set('from', from);
    if (to) params.set('to', to);
    return params.toString();
  }

  async function loadRevenue() {
    const res = await fetch(`{{ route('admin.reports.revenue_data') }}?${buildQuery()}`);
    return await res.json();
  }
  async function loadBest() {
    const res = await fetch(`{{ route('admin.reports.best_selling_data') }}?${buildQuery()}`);
    return await res.json();
  }

  function renderRevenue(data) {
    const labels = data.labels;
    const revenue = data.revenue;
    const orders = data.orders;
    if (revenueChart) revenueChart.destroy();
    if (ordersChart) ordersChart.destroy();
    if (overlayChart) overlayChart.destroy();
    revenueChart = new Chart(revenueCtx, {
      type: 'line',
      data: { labels, datasets: [{ label: 'Doanh thu (₫)', data: revenue, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.2)', tension: .2 }] },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    ordersChart = new Chart(ordersCtx, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Số đơn', data: orders, backgroundColor: '#16a34a' }] },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // Overlay chart: revenue (line) + orders (bar) with dual axes
    overlayChart = new Chart(overlayCtx, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            type: 'line',
            label: 'Doanh thu (₫)',
            data: revenue,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.2)',
            tension: .2,
            yAxisID: 'y',
          },
          {
            type: 'bar',
            label: 'Số đơn',
            data: orders,
            backgroundColor: '#16a34a',
            yAxisID: 'y1',
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Doanh thu' } },
          y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Số đơn' } }
        }
      }
    });
  }

  function renderBest(data) {
    const labels = data.labels;
    const sold = data.sold;
    if (bestChart) bestChart.destroy();
    bestChart = new Chart(bestCtx, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Đã bán', data: sold, backgroundColor: '#f59e0b' }] },
      options: { indexAxis: 'y', responsive: true, scales: { x: { beginAtZero: true } } }
    });
  }

  async function refresh() {
    const rev = await loadRevenue();
    renderRevenue(rev);
    const best = await loadBest();
    renderBest(best);
    const params = buildQuery();
    document.getElementById('exportRevenue').href = `{{ route('admin.reports.export') }}?type=revenue&format=csv&${params}`;
    document.getElementById('exportRevenueXlsx').href = `{{ route('admin.reports.export') }}?type=revenue&format=xlsx&${params}`;
    document.getElementById('exportBest').href = `{{ route('admin.reports.export') }}?type=best-selling&format=csv&${params}`;
    document.getElementById('exportBestXlsx').href = `{{ route('admin.reports.export') }}?type=best-selling&format=xlsx&${params}`;
  }

  document.getElementById('applyFilters').addEventListener('click', refresh);
  // init defaults: today range for daily
  (function initDefaults(){
    const now = new Date();
    const pad = (n) => (n<10 ? '0'+n : ''+n);
    document.getElementById('range').value = 'daily';
    // default to last 30 days
    const from = new Date(now.getTime() - 29*24*60*60*1000);
    document.getElementById('from').value = `${from.getFullYear()}-${pad(from.getMonth()+1)}-${pad(from.getDate())}`;
    document.getElementById('to').value = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
  })();
  refresh();
</script>
@endpush
@endsection
