@extends('layouts.app')

@section('title', 'Dashboard - Panel de Control')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-900 p-6 rounded-xl border border-slate-800 shadow-lg text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fa-solid fa-chart-pie text-[150px]"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-black text-blue-400 tracking-tight uppercase">Panel de Control Analítico</h2>
            <p class="text-slate-400 text-sm mt-0.5">Resumen general de trazabilidad, métricas oncológicas e inferencias registradas en el sistema.</p>
        </div>
        <div class="relative z-10 text-xs font-mono font-bold text-emerald-400 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-calendar-day"></i> {{ date('d/m/Y') }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Pacientes</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalPacientes }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Evaluaciones IA</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalEvaluaciones }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-timeline"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Controles Gemelo</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalSeguimientos }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-red-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-red-500"></div>
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-xl animate-pulse">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest">Casos Críticos (>75%)</p>
                <h3 class="text-2xl font-black text-red-600">{{ $casosCriticos }}</h3>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                <h3 class="font-black text-slate-700 uppercase tracking-tight text-sm"><i class="fa-solid fa-chart-column text-blue-500 mr-1"></i> Evaluaciones Registradas (Últimos 6 meses)</h3>
            </div>
            <div class="flex-1 relative min-h-[300px] w-full">
                <canvas id="graficoMeses"></canvas>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                <h3 class="font-black text-slate-700 uppercase tracking-tight text-sm"><i class="fa-solid fa-chart-pie text-indigo-500 mr-1"></i> Distribución de Diagnósticos IA</h3>
            </div>
            <div class="flex-1 relative min-h-[300px] w-full flex items-center justify-center">
                <canvas id="graficoDiagnosticos"></canvas>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- 1. CONFIGURACIÓN DEL GRÁFICO DE BARRAS ---
        const ctxMeses = document.getElementById('graficoMeses').getContext('2d');
        let gradientBar = ctxMeses.createLinearGradient(0, 0, 0, 400);
        gradientBar.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
        gradientBar.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

        new Chart(ctxMeses, {
            type: 'bar',
            data: {
                labels: @json($meses),
                datasets: [{
                    label: 'Muestras Analizadas',
                    data: @json($evaluacionesPorMes),
                    backgroundColor: gradientBar,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 5,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 10 } },
                        grid: { color: '#f1f5f9', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#64748b', font: { size: 11, weight: 'bold' } },
                        grid: { display: false }
                    }
                }
            }
        });

        // --- 2. CONFIGURACIÓN DEL GRÁFICO DE DONA ---
        const ctxDiag = document.getElementById('graficoDiagnosticos').getContext('2d');
        new Chart(ctxDiag, {
            type: 'doughnut',
            data: {
                labels: @json($labelsDiagnosticos),
                datasets: [{
                    data: @json($valoresDiagnosticos),
                    backgroundColor: ['#ef4444', '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 10 },
                            color: '#475569'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection