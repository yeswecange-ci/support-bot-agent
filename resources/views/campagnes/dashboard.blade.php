@extends('layouts.app')
@section('title', 'Dashboard Campagnes')

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Dashboard Campagnes</h1>
                <p class="text-sm text-gray-500 mt-0.5">Vue d'ensemble de vos campagnes WhatsApp</p>
            </div>
            <div class="flex gap-1 bg-gray-100 rounded-lg p-0.5" id="period-selector">
                @foreach(['today' => "Aujourd'hui", 'week' => 'Semaine', 'month' => 'Mois'] as $p => $label)
                    <button onclick="changePeriod('{{ $p }}')"
                       data-period="{{ $p }}"
                       class="period-btn px-3 py-1.5 text-xs font-medium rounded-md transition
                              {{ $period === $p ? 'bg-white shadow-sm text-primary-700' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4" id="kpi-cards">
            {{-- Campagnes --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Campagnes</p>
                </div>
                <p class="text-2xl font-bold text-gray-900" id="kpi-campaigns">{{ $kpis['total_campaigns'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5" id="kpi-campaigns-detail">{{ $kpis['active_campaigns'] }} active(s)</p>
            </div>

            {{-- Contacts --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Contacts</p>
                </div>
                <p class="text-2xl font-bold text-blue-600" id="kpi-contacts">{{ $kpis['total_contacts'] }}</p>
            </div>

            {{-- Messages envoyes --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Messages</p>
                </div>
                <p class="text-2xl font-bold text-purple-600" id="kpi-messages">{{ $kpis['total_messages'] }}</p>
            </div>

            {{-- Delivres --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Delivres</p>
                </div>
                <p class="text-2xl font-bold text-green-600" id="kpi-delivered">{{ $kpis['delivered'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5" id="kpi-deliver-rate">{{ $kpis['deliver_rate'] }}% taux</p>
            </div>

            {{-- Echoues --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Echoues</p>
                </div>
                <p class="text-2xl font-bold text-red-600" id="kpi-failed">{{ $kpis['failed'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5" id="kpi-fail-rate">{{ $kpis['fail_rate'] }}% taux</p>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Tendance envois (Line Chart) --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Evolution des envois</h3>
                <div class="h-[280px]">
                    <canvas id="trend-chart"></canvas>
                </div>
            </div>

            {{-- Repartition statuts (Doughnut) --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Repartition des statuts</h3>
                <div class="h-[280px] flex items-center justify-center">
                    <canvas id="status-chart"></canvas>
                </div>
            </div>
        </div>

        {{-- Tableau recapitulatif campagnes --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Toutes les campagnes</h3>
                <div class="relative max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="search-campaigns-table" placeholder="Filtrer..."
                           class="w-48 pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="campaigns-table">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-2.5 font-semibold text-gray-600 text-xs">Campagne</th>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-600 text-xs">Statut</th>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-600 text-xs">Contacts</th>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-600 text-xs">Envoyes</th>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-600 text-xs">Delivres</th>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-600 text-xs">Echoues</th>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-600 text-xs">Taux</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="campaigns-tbody">
                        @foreach($campaigns as $c)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3">
                                <a href="{{ route('campagnes.show', $c['id']) }}" class="font-medium text-gray-900 hover:text-primary-600 transition">{{ $c['name'] }}</a>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $c['status_class'] }}">{{ $c['status_label'] }}</span>
                            </td>
                            <td class="px-3 py-3 text-center text-gray-600">{{ $c['contacts'] }}</td>
                            <td class="px-3 py-3 text-center text-gray-600">{{ $c['sent'] }}</td>
                            <td class="px-3 py-3 text-center text-green-600 font-medium">{{ $c['delivered'] }}</td>
                            <td class="px-3 py-3 text-center text-red-500 font-medium">{{ $c['failed'] }}</td>
                            <td class="px-3 py-3 text-center">
                                @if($c['total'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $c['deliver_rate'] >= 80 ? 'bg-green-100 text-green-700' : ($c['deliver_rate'] >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $c['deliver_rate'] }}%
                                </span>
                                @else
                                <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($campaigns->isEmpty())
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">Aucune campagne</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function() {
    const TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;
    let currentPeriod = '{{ $period }}';

    // Chart.js defaults
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.plugins.legend.display = false;

    const tooltipStyle = {
        backgroundColor: '#1f2937',
        padding: 10,
        cornerRadius: 8,
        titleFont: { size: 12 },
        bodyFont: { size: 11 }
    };

    // ═══ TREND LINE CHART ═══
    const trendData = @json($trend);
    const trendCtx = document.getElementById('trend-chart')?.getContext('2d');
    let trendChart = null;
    if (trendCtx) {
        const gradient = trendCtx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(d => formatDate(d.date)),
                datasets: [
                    {
                        label: 'Total',
                        data: trendData.map(d => d.total),
                        borderColor: '#6366f1',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#6366f1',
                    },
                    {
                        label: 'Delivres',
                        data: trendData.map(d => d.delivered),
                        borderColor: '#10b981',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#10b981',
                    },
                    {
                        label: 'Echoues',
                        data: trendData.map(d => d.failed),
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#ef4444',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: tooltipStyle,
                    legend: { display: true, position: 'top', labels: { boxWidth: 8, usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 11 } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ═══ STATUS DOUGHNUT CHART ═══
    const statusData = @json($statusDistribution);
    const statusCtx = document.getElementById('status-chart')?.getContext('2d');
    let statusChart = null;
    if (statusCtx) {
        const statusLabels = ['Envoyes', 'Delivres', 'Lus', 'Echoues', 'En file'];
        const statusValues = [statusData.sent || 0, statusData.delivered || 0, statusData.read || 0, statusData.failed || 0, statusData.queued || 0];
        const statusColors = ['#60a5fa', '#10b981', '#6366f1', '#ef4444', '#d1d5db'];

        statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors,
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    tooltip: tooltipStyle,
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 8, usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 11 } } }
                }
            }
        });
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    }

    // ═══ FILTRE TABLEAU CAMPAGNES ═══
    const searchCampaignsTable = document.getElementById('search-campaigns-table');
    if (searchCampaignsTable) {
        searchCampaignsTable.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('#campaigns-tbody tr').forEach(row => {
                const name = row.querySelector('td')?.textContent?.trim().toLowerCase() || '';
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }

    // ═══ PERIOD SWITCH ═══
    window.changePeriod = async function(period) {
        if (period === currentPeriod) return;
        currentPeriod = period;
        document.querySelectorAll('.period-btn').forEach(b => {
            b.classList.toggle('bg-white', b.dataset.period === period);
            b.classList.toggle('shadow-sm', b.dataset.period === period);
            b.classList.toggle('text-primary-700', b.dataset.period === period);
            b.classList.toggle('text-gray-500', b.dataset.period !== period);
        });

        try {
            const r = await fetch(`{{ route('ajax.campagnes.dashboard.data') }}?period=${period}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN }
            });
            const data = await r.json();

            // Update KPIs
            document.getElementById('kpi-campaigns').textContent = data.kpis.total_campaigns;
            document.getElementById('kpi-campaigns-detail').textContent = data.kpis.active_campaigns + ' active(s)';
            document.getElementById('kpi-contacts').textContent = data.kpis.total_contacts;
            document.getElementById('kpi-messages').textContent = data.kpis.total_messages;
            document.getElementById('kpi-delivered').textContent = data.kpis.delivered;
            document.getElementById('kpi-deliver-rate').textContent = data.kpis.deliver_rate + '% taux';
            document.getElementById('kpi-failed').textContent = data.kpis.failed;
            document.getElementById('kpi-fail-rate').textContent = data.kpis.fail_rate + '% taux';

            // Update trend chart
            if (trendChart) {
                trendChart.data.labels = data.trend.map(d => formatDate(d.date));
                trendChart.data.datasets[0].data = data.trend.map(d => d.total);
                trendChart.data.datasets[1].data = data.trend.map(d => d.delivered);
                trendChart.data.datasets[2].data = data.trend.map(d => d.failed);
                trendChart.update('none');
            }

            // Update status chart
            if (statusChart) {
                const sd = data.statusDistribution;
                statusChart.data.datasets[0].data = [sd.sent || 0, sd.delivered || 0, sd.read || 0, sd.failed || 0, sd.queued || 0];
                statusChart.update('none');
            }

            // Update table
            const tbody = document.getElementById('campaigns-tbody');
            if (tbody && data.campaigns) {
                tbody.innerHTML = data.campaigns.map(c => {
                    const rateClass = c.deliver_rate >= 80 ? 'bg-green-100 text-green-700' : (c.deliver_rate >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                    const rateHtml = c.total > 0
                        ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold ${rateClass}">${c.deliver_rate}%</span>`
                        : '<span class="text-gray-400 text-xs">-</span>';
                    return `<tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3"><a href="/campagnes/${c.id}" class="font-medium text-gray-900 hover:text-primary-600 transition">${c.name}</a></td>
                        <td class="px-3 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium ${c.status_class}">${c.status_label}</span></td>
                        <td class="px-3 py-3 text-center text-gray-600">${c.contacts}</td>
                        <td class="px-3 py-3 text-center text-gray-600">${c.sent}</td>
                        <td class="px-3 py-3 text-center text-green-600 font-medium">${c.delivered}</td>
                        <td class="px-3 py-3 text-center text-red-500 font-medium">${c.failed}</td>
                        <td class="px-3 py-3 text-center">${rateHtml}</td>
                    </tr>`;
                }).join('');
            }
        } catch(e) {
            console.error('Erreur chargement dashboard', e);
        }
    };
})();
</script>
@endpush
