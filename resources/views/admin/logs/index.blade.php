@extends('layouts.app')

@section('title', 'Auditoría de Sistema')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-900 p-6 rounded-xl border border-slate-800 shadow-lg text-white">
        <div>
            <h2 class="text-2xl font-black text-slate-300 tracking-tight uppercase flex items-center gap-2">
                <i class="fa-solid fa-server text-slate-500"></i> Bitácora de Auditoría
            </h2>
            <p class="text-slate-400 text-sm mt-0.5">Registro inmutable de trazabilidad de acciones, accesos y modificaciones en el sistema.</p>
        </div>
        <div class="text-xs font-mono font-bold text-slate-300 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700 flex items-center gap-2 shadow-inner">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div> Tracking Activo
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
        <table id="tablaLogs" class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest border-b border-slate-200">
                    <th class="p-3 font-bold">Fecha y Hora</th>
                    <th class="p-3 font-bold">Usuario</th>
                    <th class="p-3 font-bold">Dirección IP</th>
                    <th class="p-3 font-bold">Acción Registrada en el Sistema</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @foreach($logs as $log)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="p-3 font-mono text-slate-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->fecha_acceso)->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800 uppercase">{{ $log->usuario->nombre ?? 'Sistema' }} {{ $log->usuario->apellido_paterno ?? '' }}</div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ $log->usuario->rol->nombre ?? 'N/A' }}</div>
                        </td>
                        <td class="p-3 font-mono text-[10px] text-blue-600 font-bold">
                            {{ $log->direccion_ip }}
                        </td>
                        <td class="p-3 font-medium text-slate-700">
                            {{ $log->accion_realizada }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaLogs').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            },
            order: [[0, 'desc']], // Ordenar por fecha más reciente
            pageLength: 25,
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4"ip>',
        });
    });
</script>
@endpush
@endsection