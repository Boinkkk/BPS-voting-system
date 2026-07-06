@extends('layouts.app')

@section('content')
<!-- Header Banner & Profile Info -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 relative">
    <div class="h-40 bg-[#0D8ABC] w-full relative">
        <!-- Optional pattern overlay could go here -->
    </div>
    
    <div class="px-8 pb-8 relative">
        <div class="flex justify-between items-end -mt-16 mb-4">
            <div class="flex items-end">
                <div class="w-32 h-32 rounded-xl bg-white p-1 shadow-md border border-gray-100 z-10">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EBF4FF&color=0D8ABC&size=128" alt="Profile" class="w-full h-full rounded-lg object-cover">
                </div>
                
                <div class="ml-6 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <div class="flex items-center text-gray-500 mt-1">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <span class="text-sm font-medium">NIP: {{ $user->pegawai ? $user->pegawai->nip : '-' }}</span>
                    </div>
                </div>
            </div>
            
            <button class="bg-[#0D8ABC] hover:bg-sky-800 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors flex items-center mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Profile
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Employee Details -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h2 class="text-xl font-bold text-gray-900">Employee Details</h2>
            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-green-200">
                Active Personnel
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 mb-6 border-b border-gray-100 pb-6">
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Position</p>
                <p class="text-sm font-semibold text-gray-900">{{ $user->pegawai ? $user->pegawai->jabatan : '-' }}</p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Department</p>
                <p class="text-sm font-semibold text-gray-900">{{ $user->pegawai && $user->pegawai->departemen ? $user->pegawai->departemen->nama : '-' }}</p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Email Address</p>
                <p class="text-sm font-semibold text-gray-900">{{ $user->email }}</p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Office Location</p>
                <p class="text-sm font-semibold text-gray-900">Gedung 2, Lt. 5, BPS Pusat</p>
            </div>
        </div>
        
        <div>
            <p class="text-xs text-gray-500 font-medium mb-3">Technical Competencies</p>
            <div class="flex flex-wrap gap-2">
                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200">R Programming</span>
                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200">Python (Pandas/Scikit-Learn)</span>
                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200">Survey Methodology</span>
                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200">Data Visualization</span>
                <span class="bg-gray-50 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200">+3 More</span>
            </div>
        </div>
    </div>
    
    <!-- Selection Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-[#0D8ABC]/30 p-8 border-t-4 border-t-[#0D8ABC]">
        <h2 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Selection Summary</h2>
        
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center mr-3 border border-sky-100">
                    <svg class="w-5 h-5 text-[#0D8ABC]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Total Participations</p>
                    <p class="text-2xl font-bold text-gray-900 leading-none">12</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </div>
        
        <div class="mb-8">
            <div class="flex justify-between items-end mb-2">
                <p class="text-xs text-gray-500 font-medium tracking-wider uppercase">Current Cycle Progress</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div class="bg-[#0D8ABC] h-2 rounded-full" style="width: 75%"></div>
            </div>
            <div class="flex justify-between text-xs">
                <span class="font-semibold text-gray-900">Phase: Interview</span>
                <span class="font-bold text-gray-900">75%</span>
            </div>
        </div>
        
        <div class="space-y-4">
            <div class="flex items-start">
                <div class="mt-0.5 mr-3">
                    <div class="h-4 w-4 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-700">Administrative Screening Passed</p>
            </div>
            <div class="flex items-start">
                <div class="mt-0.5 mr-3">
                    <div class="h-4 w-4 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-700">Technical Competency Exam Done</p>
            </div>
            <div class="flex items-start">
                <div class="mt-0.5 mr-3">
                    <div class="h-4 w-4 rounded-full border-2 border-gray-300"></div>
                </div>
                <p class="text-sm font-medium text-gray-400">Final Board Review Pending</p>
            </div>
        </div>
    </div>
</div>

<!-- Security & Preferences -->
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <h2 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Security & Preferences</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1 -->
        <a href="#" class="block p-5 rounded-lg border border-gray-200 hover:border-sky-300 hover:bg-sky-50/30 transition-colors group">
            <div class="flex justify-between items-start mb-3">
                <div class="text-sky-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Change Password</h3>
            <p class="text-xs text-gray-500">Update your security credentials every 90 days.</p>
        </a>
        
        <!-- Card 2 -->
        <a href="#" class="block p-5 rounded-lg border border-gray-200 hover:border-sky-300 hover:bg-sky-50/30 transition-colors group">
            <div class="flex justify-between items-start mb-3">
                <div class="text-sky-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Two-Factor Auth</h3>
            <p class="text-xs text-gray-500">Highly recommended for data access security.</p>
        </a>
        
        <!-- Card 3 -->
        <a href="#" class="block p-5 rounded-lg border border-gray-200 hover:border-sky-300 hover:bg-sky-50/30 transition-colors group">
            <div class="flex justify-between items-start mb-3">
                <div class="text-sky-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Notification Settings</h3>
            <p class="text-xs text-gray-500">Control how you receive system alerts and updates.</p>
        </a>
    </div>
</div>
@endsection
