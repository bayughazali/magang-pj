@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Export Data</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Export Report Activity -->
        <div class="bg-white p-4 rounded-xl shadow-md text-center">
            <h3 class="font-semibold text-lg mb-2">Report Activity</h3>
            <p class="text-gray-500 text-sm mb-4">Export laporan aktivitas sales</p>
            <a href="{{ route('export.activity') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Export PDF / CSV
            </a>
        </div>

        <!-- Export Report Competitor -->
        <div class="bg-white p-4 rounded-xl shadow-md text-center">
            <h3 class="font-semibold text-lg mb-2">Report Competitor</h3>
            <p class="text-gray-500 text-sm mb-4">Export laporan competitor</p>
            <a href="{{ route('export.competitor') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Export PDF / CSV
            </a>
        </div>

        <!-- Export Report Operational -->
        <div class="bg-white p-4 rounded-xl shadow-md text-center">
            <h3 class="font-semibold text-lg mb-2">Report Operational</h3>
            <p class="text-gray-500 text-sm mb-4">Export laporan operasional</p>
            <a href="{{ route('export.operational') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                Export PDF / CSV
            </a>
        </div>

    </div>
</div>
@endsection
