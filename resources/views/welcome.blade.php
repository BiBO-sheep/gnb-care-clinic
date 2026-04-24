<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G&B Care Clinic - Solusi Kesehatan Digital Anda</title>
    
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans selection:bg-primary selection:text-white">

    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <span class="font-extrabold text-xl tracking-tight text-primary">G&B Care Clinic</span>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="#beranda" class="text-gray-600 hover:text-primary font-semibold transition">Beranda</a>
                    <a href="#keunggulan" class="text-gray-600 hover:text-primary font-semibold transition">Keunggulan</a>
                    <a href="#layanan" class="text-gray-600 hover:text-primary font-semibold transition">Layanan</a>
                </div>

                <div class="hidden md:flex">
                    <a href="#download" class="bg-primary hover:bg-[#004f54] text-white px-6 py-2.5 rounded-full font-bold shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5">
                        <i class="fa-brands fa-google-play mr-2"></i> Unduh Aplikasi
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] rounded-full bg-primaryLight opacity-50 blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] rounded-full bg-orange-100 opacity-50 blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primaryLight text-primary font-bold text-sm mb-6 border border-primary/20">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        Klinik Digital Modern
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 text-gray-900">
                        Kesehatan Anda, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Prioritas Utama Kami</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 mb-8 font-body max-w-2xl mx-auto lg:mx-0">
                        Rasakan kemudahan membuat janji temu, memantau antrean secara real-time, hingga menebus resep obat langsung dari genggaman tangan Anda.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#download" class="bg-primary hover:bg-[#004f54] text-white px-8 py-4 rounded-full font-bold shadow-xl shadow-primary/30 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-mobile-screen"></i> Gunakan Aplikasi
                        </a>
                        <a href="#keunggulan" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 px-8 py-4 rounded-full font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                <div class="relative hidden md:block">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white transform rotate-2 hover:rotate-0 transition-all duration-500">
                        <img src="https://images.unsplash.com/photo-1551076805-e166946c9eb9?q=80&w=800&auto=format&fit=crop" alt="Dokter G&B Clinic" class="w-full h-auto object-cover">
                        <div class="absolute bottom-6 left-[-20px] bg-white p-4 rounded-2xl shadow-xl flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                                <i class="fa-solid fa-check-double"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold">Layanan Aktif</p>
                                <p class="text-sm font-extrabold text-gray-900">Tanpa Antre Lama</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="keunggulan" class="py-20 bg-white border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Mengapa Memilih <span class="text-primary">G&B Care?</span></h2>
                <p class="text-gray-500 font-body max-w-2xl mx-auto">Kami menggabungkan pelayanan medis profesional dengan teknologi canggih untuk memberikan pengalaman berobat yang nyaman dan transparan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-8 rounded-3xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-primary/20 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-primaryLight rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-check text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Booking Digital</h3>
                    <p class="text-gray-500 font-body text-sm leading-relaxed">Atur jadwal konsultasi dengan dokter pilihan Anda kapan saja, langsung dari aplikasi.</p>
                </div>

                <div class="p-8 rounded-3xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-secondary/20 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users-viewfinder text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Live Queue Monitor</h3>
                    <p class="text-gray-500 font-body text-sm leading-relaxed">Pantau nomor antrean secara real-time. Tidak perlu lagi menunggu berjam-jam di ruang tunggu klinik.</p>
                </div>

                <div class="p-8 rounded-3xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-primary/20 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-primaryLight rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-prescription text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Resep Digital Terintegrasi</h3>
                    <p class="text-gray-500 font-body text-sm leading-relaxed">Terima catatan diagnosis dan resep obat secara digital. Langsung terhubung ke bagian Farmasi.</p>
                </div>

                <div class="p-8 rounded-3xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-secondary/20 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-credit-card text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Pembayaran Mudah</h3>
                    <p class="text-gray-500 font-body text-sm leading-relaxed">Bayar tagihan klinik melalui QRIS, Transfer Bank, atau E-Wallet langsung di dalam aplikasi.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="download" class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-primary z-0"></div>
        <div class="absolute inset-0 z-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Siap Memulai Perjalanan Sehat Anda?</h2>
            <p class="text-primaryLight font-body text-lg mb-10 max-w-2xl mx-auto">
                Dapatkan akses penuh ke layanan kesehatan G&B Care Clinic dalam satu genggaman. Unduh aplikasi kami sekarang juga. Gratis!
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="#" class="flex items-center gap-4 bg-black text-white px-6 py-3 rounded-2xl hover:scale-105 transition-transform w-full sm:w-auto border border-gray-800">
                    <i class="fa-brands fa-google-play text-3xl"></i>
                    <div class="text-left">
                        <p class="text-[10px] font-bold text-gray-300 uppercase tracking-wide">Get it on</p>
                        <p class="text-lg font-bold leading-none">Google Play</p>
                    </div>
                </a>
                
                <a href="#" class="flex items-center gap-4 bg-white text-black px-6 py-3 rounded-2xl hover:scale-105 transition-transform w-full sm:w-auto shadow-xl">
                    <i class="fa-brands fa-apple text-3xl"></i>
                    <div class="text-left">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Download on the</p>
                        <p class="text-lg font-bold leading-none">App Store</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center gap-3 mb-4 md:mb-0">
                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white">
                    <i class="fa-solid fa-stethoscope text-sm"></i>
                </div>
                <span class="font-bold text-lg text-white">G&B Care Clinic</span>
            </div>
            
            <p class="text-sm font-body text-gray-500 text-center md:text-left">
                &copy; 2026 G&B Care Clinic. Hak Cipta Dilindungi.<br>
                Sistem Terintegrasi Flutter & Laravel.
            </p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>