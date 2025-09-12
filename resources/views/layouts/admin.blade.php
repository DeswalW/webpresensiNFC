<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - Sistem Presensi')</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('styles')
</head>
<body class="bg-gray-50">
    <!-- Mobile overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gradient-to-br from-primary-green to-green-800 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6">
                <div class="text-center mb-8">
                    <div class="mb-4">
                        <img src="{{ asset('img/logosmk.png') }}" alt="Logo SMK" class="w-20 h-20 mx-auto">
                    </div>
                    <h4 class="text-white text-lg font-semibold">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        Sistem Presensi
                    </h4>
                    <small class="text-green-200">Admin Panel</small>
                </div>
                
                <ul class="space-y-2">
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            <span class="sidebar-text">Dashboard</span>
                        </a>
                    </li>
                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.admins.*') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.admins.index') }}">
                            <i class="fas fa-user-shield mr-3"></i>
                            <span class="sidebar-text">Manajemen Admin</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.students.index') }}">
                            <i class="fas fa-users mr-3"></i>
                            <span class="sidebar-text">Data Siswa</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.attendances.*') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.attendances.index') }}">
                            <i class="fas fa-calendar-check mr-3"></i>
                            <span class="sidebar-text">Presensi</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.report') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.report') }}">
                            <i class="fas fa-chart-bar mr-3"></i>
                            <span class="sidebar-text">Laporan</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog mr-3"></i>
                            <span class="sidebar-text">Pengaturan</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white hover:bg-primary-yellow hover:text-primary-black rounded-lg transition-all duration-200 {{ request()->routeIs('admin.activity-logs.*') ? 'bg-primary-yellow text-primary-black font-semibold' : '' }}" 
                           href="{{ route('admin.activity-logs.index') }}">
                            <i class="fas fa-clipboard-list mr-3"></i>
                            <span class="sidebar-text">Log Activity</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top navbar -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-green">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <!-- Logo untuk mobile -->
                            <div class="lg:hidden ml-3">
                                <img src="{{ asset('img/logosmk.png') }}" alt="Logo SMK" class="w-8 h-8">
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 sm:space-x-4">
                            <div class="relative">
                                <button id="userDropdownBtn" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                    <i class="fas fa-user-circle text-xl"></i>
                                    <span class="font-medium hidden sm:block">{{ Auth::guard('admin')->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-sm hidden sm:block"></i>
                                </button>
                                <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden border border-gray-200">
                                    <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-200 sm:hidden">
                                        {{ Auth::guard('admin')->user()->name }}
                                    </div>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="text-green-700 hover:text-green-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="text-red-700 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    @yield('scripts')
    
    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            // Toggle mobile menu
            if (mobileMenuBtn && sidebar && mobileOverlay) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    mobileOverlay.classList.toggle('hidden');
                });
                
                // Close menu when clicking overlay
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                });
                
                // Close menu when clicking on menu items (mobile)
                const menuItems = sidebar.querySelectorAll('a');
                menuItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth < 1024) { // lg breakpoint
                            sidebar.classList.add('-translate-x-full');
                            mobileOverlay.classList.add('hidden');
                        }
                    });
                });
            }
            
            // User dropdown functionality
            const dropdownBtn = document.getElementById('userDropdownBtn');
            const dropdown = document.getElementById('userDropdown');
            
            if (dropdownBtn && dropdown) {
                dropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
                
                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdown.classList.add('hidden');
                    }
                });
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    sidebar.classList.remove('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
