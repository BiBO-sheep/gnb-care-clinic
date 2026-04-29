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
                        primary: '#006A6A',
                        primaryLight: '#E0F7F7',
                        secondary: '#FF7F50',
                        dark: '#121212',
                        background: '#F4F3F1', // Warna background kalem khas Flutter lu
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background font-sans text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex z-20 shadow-sm">
        <div class="h-20 flex items-center px-6 border-b border-gray-100">
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 shadow-md shadow-primary/30">
                <i class="fa-solid fa-stethoscope text-sm"></i>
            </div>
            <span class="font-extrabold text-lg text-primary tracking-tight">G&B Care Admin</span>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="px-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
            
            <a href="#" class="flex items-center px-4 py-3 bg-primaryLight text-primary rounded-xl font-bold transition-all">
                <i class="fa-solid fa-house w-6 text-center mr-2"></i> Dashboard
            </a>
            
            <a href="#" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-primary rounded-xl font-semibold transition-all">
                <i class="fa-solid fa-users w-6 text-center mr-2"></i> Pasien
            </a>
            
            <a href="#" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-primary rounded-xl font-semibold transition-all">
                <i class="fa-solid fa-list-ol w-6 text-center mr-2"></i> Monitor Antrean
            </a>

            <p class="px-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Medis & Kasir</p>
            
            <a href="/admin/doctor" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-primary rounded-xl font-semibold transition-all">
    <i class="fa-solid fa-user-doctor w-6 text-center mr-2"></i> Ruang Dokter
</a>

           <a href="/admin/kasir" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-primary rounded-xl font-semibold transition-all">
    <i class="fa-solid fa-cash-register w-6 text-center mr-2"></i> Pembayaran
</a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <a href="#" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-bold transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center mr-2"></i> Keluar
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-8 z-10">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">@yield('header', 'Dashboard')</h1>
                <p class="text-sm font-body text-gray-500">@yield('subheader', 'Selamat datang di panel kendali G&B Care.')</p>
            </div>

            <div class="flex items-center space-x-6">
                <button class="relative p-2 text-gray-400 hover:text-primary transition-colors">
                    <i class="fa-regular fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-secondary rounded-full border-2 border-white"></span>
                </button>
                
                <div class="h-8 w-px bg-gray-200"></div>

                <div class="flex items-center cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name=Admin+Klinik&background=006A6A&color=fff" alt="User Avatar" class="w-10 h-10 rounded-full shadow-sm border-2 border-white">
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