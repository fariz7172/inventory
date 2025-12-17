<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Neumorphic Styles -->
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #e0e5ec; }
        .neumorphic {
            border-radius: 20px;
            background: #e0e5ec;
            box-shadow:  9px 9px 16px rgb(163,177,198,0.6), -9px -9px 16px rgba(255,255,255, 0.5);
        }
        .neumorphic-inset {
            border-radius: 10px;
            background: #e0e5ec;
            box-shadow: inset 6px 6px 10px 0 rgba(163,177,198, 0.7), inset -6px -6px 10px 0 rgba(255,255,255, 0.8);
        }
        .neumorphic-btn {
            border-radius: 50px;
            background: #e0e5ec;
            box-shadow:  5px 5px 10px rgb(163,177,198,0.6), -5px -5px 10px rgba(255,255,255, 0.5);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .neumorphic-btn:active {
            box-shadow: inset 5px 5px 10px rgb(163,177,198,0.6), inset -5px -5px 10px rgba(255,255,255, 0.5);
        }
        .neumorphic-btn.active {
            color: #2563eb;
            box-shadow: inset 5px 5px 10px rgb(163,177,198,0.6), inset -5px -5px 10px rgba(255,255,255, 0.5);
        }
        
        /* Sidebar Transition */
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 1023px) {
            .sidebar-closed {
                transform: translateX(-100%) !important;
            }
        }

        /* Form Input Styles */
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            outline: none;
            font-size: 0.95rem;
            color: #374151;
            font-weight: 500;
        }
        .form-input::placeholder {
            color: #9CA3AF;
        }
        .form-input:focus {
            outline: none;
        }
        select.form-input {
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.25rem;
            padding-right: 2.5rem;
        }
        select.form-input option {
            background: #e0e5ec;
            color: #374151;
        }
        textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }
        input[type="file"].form-input {
            padding: 0.5rem;
        }
    </style>
</head>
<body class="text-gray-700 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-[#e0e5ec] shadow-xl lg:translate-x-0 transform sidebar-closed border-r border-gray-200">
            <!-- Branding -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800 tracking-wider">INVENTORY</h1>
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-red-500 focus:outline-none">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="p-6 space-y-4 overflow-y-auto h-[calc(100vh-10rem)]">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl neumorphic-btn {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-600 hover:text-blue-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="font-bold">Dashboard</span>
                </a>

                <div class="pt-4 pb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Operations</div>

                <a href="{{ route('inventory.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl neumorphic-btn {{ request()->routeIs('inventory.index') ? 'active' : 'text-gray-600 hover:text-blue-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span class="font-bold">Summary</span>
                </a>

                <a href="{{ route('inventory.tracking') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl neumorphic-btn {{ request()->routeIs('inventory.tracking') ? 'active' : 'text-gray-600 hover:text-blue-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="font-bold">Track Item</span>
                </a>

                <a href="{{ route('inventory.transfer') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl neumorphic-btn {{ request()->routeIs('inventory.transfer') ? 'active' : 'text-gray-600 hover:text-purple-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    <span class="font-bold">Transfer</span>
                </a>

                <div class="pt-4 pb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Master Data</div>

                <a href="{{ route('products.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl neumorphic-btn {{ request()->routeIs('products.*') ? 'active' : 'text-gray-600 hover:text-blue-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="font-bold">Products</span>
                </a>

                <a href="{{ route('warehouses.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl neumorphic-btn {{ request()->routeIs('warehouses.*') ? 'active' : 'text-gray-600 hover:text-blue-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="font-bold">Warehouses</span>
                </a>
            </nav>
            
            <!-- User & Logout -->
            <div class="absolute bottom-0 w-full p-6 border-t border-gray-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-bold text-gray-600">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 neumorphic-btn text-red-500 font-bold hover:text-red-700 text-sm">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
            <!-- Mobile Header -->
            <header class="lg:hidden h-16 flex items-center justify-between px-6 bg-[#e0e5ec] shadow-sm z-40 relative">
                <h1 class="text-xl font-bold text-gray-800">INVENTORY</h1>
                <button onclick="toggleSidebar()" class="neumorphic-btn p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <!-- Page Content -->
            <main class="p-6 lg:p-10 flex-1 overflow-y-auto w-full">
                @if(isset($header))
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $header }}</h2>
                        @if(isset($subheader))
                            <p class="text-gray-500">{{ $subheader }}</p>
                        @endif
                    </div>
                @endif

                @if(session('success'))
                    <div class="neumorphic p-4 mb-8 text-green-600 font-bold border-l-4 border-green-500">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="neumorphic p-4 mb-8 text-red-600 font-bold border-l-4 border-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
        
        <!-- Overlay for mobile -->
        <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            if (sidebar.classList.contains('sidebar-closed')) {
                sidebar.classList.remove('sidebar-closed'); // Open
                sidebar.classList.remove('-translate-x-full'); // Remove tailwind hidden class logic if used
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('sidebar-closed'); // Close
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
