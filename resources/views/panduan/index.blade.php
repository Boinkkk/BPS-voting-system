@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buku Panduan Penggunaan</h1>
            <p class="text-sm text-gray-500 mt-1">Dokumentasi lengkap cara penggunaan SIVOTA</p>
        </div>
        
        <button onclick="window.print()" class="mt-4 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow inline-flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Download / Cetak PDF
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-md p-4 sm:p-8 overflow-hidden">
        <div class="markdown-body font-sans">
            {!! $html !!}
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.5.1/github-markdown.min.css">
<style>
    /* Meng-override reset Tailwind agar markdown tampil sempurna */
    .markdown-body {
        box-sizing: border-box;
        min-width: 200px;
        max-width: 100%;
        margin: 0 auto;
        background-color: transparent;
        line-height: 1.7 !important;
        color: #374151 !important;
        font-family: inherit !important;
    }
    .markdown-body h1, .markdown-body h2, .markdown-body h3, .markdown-body h4 {
        margin-top: 1.5em !important;
        margin-bottom: 0.5em !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
        color: #1e3a8a !important; /* blue-900 */
    }
    .markdown-body h1 { 
        font-size: 2.25rem !important; 
        border-bottom: 2px solid #e5e7eb !important;
        padding-bottom: 0.3em !important;
    }
    .markdown-body h2 { 
        font-size: 1.75rem !important; 
        border-bottom: 1px solid #e5e7eb !important; 
        padding-bottom: 0.3em !important;
        color: #1d4ed8 !important; /* blue-700 */
    }
    .markdown-body h3 { 
        font-size: 1.25rem !important; 
        color: #374151 !important;
    }
    
    .markdown-body p {
        margin-top: 0 !important;
        margin-bottom: 1em !important;
    }
    
    .markdown-body ul, .markdown-body ol {
        margin-top: 0 !important;
        margin-bottom: 1em !important;
        padding-left: 2em !important;
    }
    .markdown-body ol { list-style-type: decimal !important; }
    .markdown-body ul { list-style-type: disc !important; }
    .markdown-body li {
        margin-bottom: 0.5em !important;
        display: list-item !important;
    }
    
    .markdown-body hr {
        margin: 2em 0 !important;
        border: 0 !important;
        border-top: 2px solid #e5e7eb !important;
    }

    /* Print Styles */
    @media print {
        @page {
            margin: 1.5cm;
            size: auto;
        }
        body * {
            visibility: hidden;
        }
        .markdown-body, .markdown-body * {
            visibility: visible;
        }
        .markdown-body {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            background: #fff;
            color: #000 !important;
        }
        
        /* Sembunyikan elemen aplikasi */
        #sidebar, nav, header, aside, .sidebar, #devTimeWidget, .print\:hidden {
            display: none !important;
        }
    }
</style>
@endpush
@endsection
