@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="patientResultsPage()" x-cloak>

    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <div class="flex items-center gap-4">
                <a href="{{ route('lab-results.index', ['view' => 'patients']) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-gray-300 text-gray-500 hover:text-red-600 hover:border-red-400 bg-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-semibold text-gray-900">
                            {{ $data['patient']['first_name'] }} {{ $data['patient']['last_name'] }}
                        </h1>
                        @if($data['patient']['gender'] ?? null)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                         {{ strtolower($data['patient']['gender']) === 'f' ? 'bg-pink-50 text-pink-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ ucfirst($data['patient']['gender']) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Dossier N° <span class="font-mono text-gray-700 font-semibold">{{ $data['patient']['file_number'] }}</span>
                    </p>
                </div>
            </div>

            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimer
            </button>
        </div>
    </header>

    {{-- MAIN --}}
    <main class="max-w-7xl mx-auto px-6 py-6 space-y-6">

        {{-- PATIENT SUMMARY CARD --}}
        <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col md:flex-row gap-5">
            {{-- Avatar --}}
            <div class="flex items-center justify-center md:w-40">
                <div class="w-20 h-20 rounded-full bg-red-600 text-white flex items-center justify-center text-3xl font-bold">
                    {{ strtoupper(substr($data['patient']['first_name'] ?? 'U', 0, 1) . substr($data['patient']['last_name'] ?? 'N', 0, 1)) }}
                </div>
            </div>

            {{-- Basic info + stats --}}
            <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="col-span-2 md:col-span-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Patient</p>
                    <p class="mt-1 text-base font-semibold text-gray-900">
                        {{ $data['patient']['first_name'] }} {{ $data['patient']['last_name'] }}
                    </p>
                    @if($data['patient']['dob'] ?? null)
                        <p class="mt-1 text-xs text-gray-500">
                            Né(e) le {{ \Carbon\Carbon::parse($data['patient']['dob'])->format('d/m/Y') }}
                        </p>
                    @endif
                </div>

                @if($data['patient']['dob'] ?? null)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Âge</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($data['patient']['dob'])->age }} <span class="text-xs font-normal text-gray-500">ans</span>
                        </p>
                    </div>
                @endif

                @if($data['patient']['blood_type'] ?? null)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Groupe sanguin</p>
                        <p class="mt-1 text-xl font-bold text-red-600">
                            {{ $data['patient']['blood_type'] }}
                        </p>
                    </div>
                @endif

                @if($data['patient']['phone'] ?? null)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Téléphone</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $data['patient']['phone'] }}
                        </p>
                    </div>
                @endif

                @if($data['patient']['email'] ?? null)
                    <div class="col-span-2 md:col-span-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Email</p>
                        <p class="mt-1 text-xs font-semibold text-gray-900 truncate">
                            {{ $data['patient']['email'] }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Medical notes (simple) --}}
            @if($data['patient']['allergies'] || $data['patient']['chronic_diseases'] || $data['patient']['medication'])
                <div class="md:w-56 border-t md:border-t-0 md:border-l border-gray-200 md:pl-4 pt-4 md:pt-0 text-xs space-y-2">
                    @if($data['patient']['allergies'] ?? null)
                        <div>
                            <p class="font-semibold text-gray-700">Allergies</p>
                            <p class="text-gray-600 mt-0.5">{{ $data['patient']['allergies'] }}</p>
                        </div>
                    @endif
                    @if($data['patient']['chronic_diseases'] ?? null)
                        <div>
                            <p class="font-semibold text-gray-700">Maladies chroniques</p>
                            <p class="text-gray-600 mt-0.5">{{ $data['patient']['chronic_diseases'] }}</p>
                        </div>
                    @endif
                    @if($data['patient']['medication'] ?? null)
                        <div>
                            <p class="font-semibold text-gray-700">Médication</p>
                            <p class="text-gray-600 mt-0.5">{{ $data['patient']['medication'] }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </section>

        {{-- FILTER BAR --}}
        <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col lg:flex-row gap-4 lg:items-center justify-between">

            {{-- Year pills --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold text-gray-600 uppercase">Années</span>
                @foreach($data['years'] ?? [] as $year)
                    <button type="button"
                            @click="toggleYear({{ $year }})"
                            :class="yearActive({{ $year }}) ? 'bg-red-600 text-white border-red-600' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-full text-xs font-medium border transition">
                        {{ $year }}
                    </button>
                @endforeach
                <button type="button"
                        @click="resetYears()"
                        class="text-xs text-gray-500 underline hover:text-gray-700">
                    Réinitialiser
                </button>
            </div>

            {{-- Timeline slider --}}
            @php $datesArray = array_values($data['dates'] ?? []); @endphp
            @if(count($datesArray) > 1)
                <div class="w-full lg:w-80">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Période</span>
                        <span x-text="currentDateLabel" class="font-mono"></span>
                    </div>
                    <input type="range"
                           min="0"
                           max="{{ count($datesArray) - 1 }}"
                           value="{{ count($datesArray) - 1 }}"
                           step="1"
                           x-model.number="index"
                           class="w-full h-1.5 bg-gray-200 rounded-full appearance-none cursor-pointer accent-red-600">
                    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                        <span>{{ $datesArray[0]['display'] ?? '' }}</span>
                        <span>{{ $datesArray[count($datesArray)-1]['display'] ?? '' }}</span>
                    </div>
                </div>
            @endif
        </section>

        {{-- RESULTS MATRIX --}}
        <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <div class="px-6 py-3 border-b flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Historique des analyses</h2>
                    <p class="text-xs text-gray-500">Résultats par analyse et par date</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span> Normal
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span> Anormal
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span> Critique
                    </span>
                </div>
            </div>

            @php
                $datesByYear = [];
                $flatIndex = 0;
                $dateIndexMap = [];
                foreach (($data['dates'] ?? []) as $dateKey => $dateInfo) {
                    $year = $dateInfo['year'];
                    $datesByYear[$year][$dateKey] = $dateInfo;
                    $dateIndexMap[$dateKey] = $flatIndex++;
                }
            @endphp

            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-50 sticky top-[64px] z-10">
                    {{-- Years --}}
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b border-gray-200 sticky left-0 bg-gray-50 min-w-[200px]">
                            Analyse
                        </th>
                        @foreach(($data['years'] ?? []) as $year)
                            <th colspan="{{ count($datesByYear[$year] ?? []) }}"
                                x-show="yearActive({{ $year }})"
                                class="px-4 py-3 text-center font-semibold text-gray-800 border-b border-l border-gray-200 bg-gray-50">
                                {{ $year }}
                            </th>
                        @endforeach
                    </tr>

                    {{-- Dates --}}
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 sticky left-0 bg-white"></th>
                        @foreach(($data['years'] ?? []) as $year)
                            @foreach(($datesByYear[$year] ?? []) as $dateKey => $dateInfo)
                                <th class="px-3 py-2 text-center border-l border-gray-100 bg-gray-50 min-w-[110px]"
                                    x-show="yearActive({{ $year }}) && index >= {{ $dateIndexMap[$dateKey] }}">
                                    <div class="text-xs font-semibold text-gray-800">{{ $dateInfo['display'] }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $dateInfo['time'] }}</div>
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>

                        <tbody>
                        @foreach(($data['categories'] ?? []) as $categoryName => $analyses)
                            {{-- Category row --}}
                            <tr class="bg-red-50">
                                <td colspan="100" class="px-4 py-2 border-t border-red-200 text-sm font-semibold text-red-700">
                                    {{ $categoryName }}
                                </td>
                            </tr>

                            {{-- Analyses --}}
                            @foreach($analyses as $analysisName => $results)
                                @php
                                    $resultsByDate = [];
                                    foreach ($results as $result) {
                                        $resultDate = \Carbon\Carbon::parse($result['date'])->format('Y-m-d H:i:s');
                                        $resultsByDate[$resultDate] = $result;
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 border-b border-gray-100">
                                    {{-- Analysis name --}}
                                    <td class="px-4 py-2 sticky left-0 bg-white border-r border-gray-100">
                                        <span class="font-medium text-gray-800">{{ $analysisName }}</span>
                                    </td>

                                    {{-- Results per date --}}
                                    @foreach(($data['years'] ?? []) as $year)
                                        @foreach(($datesByYear[$year] ?? []) as $dateKey => $dateInfo)
                                            @php
                                                $result = $resultsByDate[$dateKey] ?? null;
                                                $value = $result['value'] ?? null;
                                                $isCritical = $result && strtolower($result['interpretation'] ?? '') === 'critical';
                                                $isAbnormal = false;
                                                if ($result && isset($result['normal_min'], $result['normal_max']) && is_numeric($value)) {
                                                    $num = floatval($value);
                                                    $isAbnormal = $num < $result['normal_min'] || $num > $result['normal_max'];
                                                }
                                                $badgeColor = $isCritical ? 'text-red-600' : ($isAbnormal ? 'text-orange-600' : 'text-gray-900');
                                            @endphp
                                            <td class="px-3 py-2 text-center border-l border-gray-100 align-top"
                                                x-show="yearActive({{ $year }}) && index >= {{ $dateIndexMap[$dateKey] }}">
                                                @if($result)
                                                    <div class="flex flex-col items-center space-y-0.5">

                                                        {{-- Status dot + value + unit --}}
                                                        <div class="flex items-center gap-1">
                                                            <span class="w-2 h-2 rounded-full {{ $isCritical ? 'bg-red-500' : ($isAbnormal ? 'bg-orange-500' : 'bg-green-500') }}"></span>
                                                            <span class="text-sm font-semibold {{ $badgeColor }}">
                                                                {{ $value }}
                                                                @if(!empty($result['unit']))
                                                                    <span class="text-[11px] text-gray-500 ml-1">{{ $result['unit'] }}</span>
                                                                @endif
                                                            </span>
                                                        </div>

                                                        {{-- Normal range --}}
                                                        @if(isset($result['normal_min'], $result['normal_max']))
                                                            <div class="text-[10px] text-gray-500">
                                                                {{ $result['normal_min'] }} - {{ $result['normal_max'] }}
                                                            </div>
                                                        @endif

                                                        {{-- Device --}}
                                                        @if($result['device'] ?? null)
                                                            <div class="text-[10px] text-gray-400 truncate max-w-[80px]">
                                                                {{ $result['device'] }}
                                                            </div>
                                                        @endif

                                                    </div>
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                </tbody>

            </table>
        </section>

    </main>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    @media print {
        body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        header, button, section:nth-of-type(2) { display: none !important; }
        .sticky { position: static !important; }
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 999px;
        background: #dc2626;
        cursor: pointer;
    }
    input[type="range"]::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        background: #dc2626;
        cursor: pointer;
        border: none;
    }
</style>
@endpush

@push('scripts')
<script>
function patientResultsPage() {
    return {
        years: @json($data['years'] ?? []),
        activeYears: new Set(@json($data['years'] ?? [])),
        index: {{ max(count($data['dates'] ?? []) - 1, 0) }},
        datesFlat: @json(array_values($data['dates'] ?? [])),

        yearActive(year) {
            return this.activeYears.has(year);
        },
        toggleYear(year) {
            if (this.activeYears.has(year)) {
                this.activeYears.delete(year);
            } else {
                this.activeYears.add(year);
            }
            this.activeYears = new Set(this.activeYears);
        },
        resetYears() {
            this.activeYears = new Set(this.years);
            this.index = this.datesFlat.length ? this.datesFlat.length - 1 : 0;
        },
        get currentDateLabel() {
            if (!this.datesFlat.length) return '';
            const idx = Math.max(0, Math.min(this.index, this.datesFlat.length - 1));
            const d = this.datesFlat[idx];
            return `${d.display} ${d.time}`;
        }
    }
}
</script>
@endpush

@endsection
