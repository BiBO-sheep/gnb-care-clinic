<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - G&B Care</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Be+Vietnam+Pro:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['"Be Vietnam Pro"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0B2B5E',
                        'primary-light': '#E6F0FA',
                        secondary: '#4A90E2',
                        dark: '#121212',
                        background: '#F8FAFC', 
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(11, 43, 94, 0.08)',
                        'glass': '0 8px 32px 0 rgba(11, 43, 94, 0.05)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background font-sans text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-white/90 backdrop-blur-xl border-r border-gray-100 flex flex-col hidden md:flex z-20 shadow-soft">
        <div class="h-20 flex items-center px-6 border-b border-gray-100">
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 shadow-md shadow-primary/30">
                <i class="fa-solid fa-stethoscope text-sm"></i>
            </div>
            <span class="font-extrabold text-lg text-primary tracking-tight">G&B Care Admin</span>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a href="/admin" class="flex items-center px-4 py-3 mb-6 text-white bg-primary rounded-xl hover:bg-primary/90 transition-all font-bold shadow-md shadow-primary/20">
                <i class="fa-solid fa-database w-6 text-center mr-2"></i> Kembali ke Master Data
            </a>

            <p class="px-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
            
            <a href="/klinik/dashboard" class="flex items-center px-4 py-3 mb-2 rounded-xl transition-all {{ request()->is('klinik/dashboard*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-chart-line w-6 text-center mr-2"></i> Dashboard
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition-all {{ request()->is('klinik/reports*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-print w-6 text-center mr-2"></i> Laporan & Export
            </a>

            <a href="/klinik/queue" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->is('klinik/queue*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-list-ol w-6 text-center mr-2"></i> Monitor Antrean
            </a>

            <a href="/klinik/pasien" class="flex items-center px-4 py-3 mb-2 rounded-xl transition-all {{ request()->is('klinik/pasien*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-address-book w-6 text-center mr-2"></i> Buku Pasien
            </a>

            <a href="/klinik/obat" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->is('klinik/obat*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-pills w-6 text-center mr-2"></i> Kelola Obat
            </a>

            <p class="px-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Medis & Kasir</p>
            
            <a href="/klinik/doctor" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->is('klinik/doctor*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-user-doctor w-6 text-center mr-2"></i> Ruang Dokter
            </a>

            <a href="/klinik/kasir" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->is('klinik/kasir*') ? 'bg-primary-light text-primary font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-primary font-semibold' }}">
                <i class="fa-solid fa-cash-register w-6 text-center mr-2"></i> Pembayaran
            </a>

        </nav>

        <div class="p-4 border-t border-gray-100">
            <form action="/admin/logout" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-bold transition-all">
                    <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center mr-2"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <header class="h-20 bg-white/70 backdrop-blur-xl border-b border-white/50 flex items-center justify-between px-8 z-10 shadow-glass">
            <div>
                <nav class="flex text-gray-400 text-xs font-bold mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="/admin" class="hover:text-primary transition-colors">Admin</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-[10px] mx-1"></i>
                                <span class="text-gray-600">@yield('title', 'Dashboard')</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-extrabold text-gray-800 leading-none">@yield('header', 'Dashboard')</h1>
            </div>

            <div class="flex items-center space-x-6">
                <!-- Global Search -->
                <div class="hidden md:flex relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-primary focus:border-primary block w-64 pl-10 p-2 shadow-sm transition-all" placeholder="Cari data pasien...">
                </div>

                <button class="relative p-2 text-gray-400 hover:text-primary transition-colors">
                    <i class="fa-regular fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-secondary rounded-full border-2 border-white"></span>
                </button>
                
                <div class="h-8 w-px bg-gray-200"></div>

                <div class="flex items-center cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name=Admin+Klinik&background=0B2B5E&color=fff" alt="User Avatar" class="w-10 h-10 rounded-full shadow-soft border-2 border-white">
                    <div class="ml-3 hidden md:block">
                        <p class="text-sm font-bold text-gray-800 leading-tight">Admin Utama</p>
                        <p class="text-xs font-body text-gray-500">Resepsionis</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-background">
            @yield('content')
        </main>
    </div>

</body>
</html>