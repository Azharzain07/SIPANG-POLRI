<x-app-layout>
    {{-- Kita tidak menggunakan header bawaan, jadi biarkan slot ini kosong --}}
    <x-slot name="header"></x-slot>

    {{-- CSS Khusus --}}
    <style>
        .hero-section {
            position: relative;
            height: 85vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: white;
        }
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/hero-background.png') }}");
            background-size: cover;
            background-position: center;
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
            padding: 2rem;
        }
        .hero-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        }
        .hero-content p {
            font-size: 1.25rem;
            margin-top: 1rem;
            text-shadow: 1px 1px 6px rgba(0,0,0,0.7);
        }
        .hero-image {
            position: absolute;
            bottom: 0;
            right: 5%;
            height: 90%;
            z-index: 2;
        }
        .service-card {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            text-align: left;
            transition: all 0.3s ease;
            border-top: 5px solid transparent;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .service-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .service-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .service-card p {
            font-size: 0.9rem;
            color: #6B7280; /* gray-500 */
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .service-card a {
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .feature-icon {
            flex-shrink: 0;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        .feature-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: white;
        }
        .about-image, .about-content, .contact-info, .contact-map {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .is-visible {
            opacity: 1;
            transform: none;
        }
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: #4B5563; /* gray-600 */
        }
        .contact-icon {
            flex-shrink: 0;
            width: 2.5rem;
            height: 2.5rem;
            background-color: #E0E7FF; /* indigo-100 */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        .contact-icon svg {
            width: 1.25rem;
            height: 1.25rem;
            color: #4338CA; /* indigo-700 */
        }
    </style>

    <!-- HERO SECTION -->
    <div class="hero-section">
        <div class="hero-background"></div>
        <div class="hero-content">
            <h1>Sistem Informasi Perencanaan Anggaran</h1>
            <p>Profesional, Modern, dan Terpercaya dalam pengelolaan anggaran Bagren Polres Garut.</p>
        </div>
        <img src="{{ asset('images/foto-kapolres.png') }}" alt="Kapolres" class="hero-image">
    </div>

    <!-- KARTU LAYANAN -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="service-card shadow-md" style="border-top-color: #D69E2E;">
                    <div class="icon" style="color: #D69E2E;">&#x1f4ca;</div>
                    <h3>Pengajuan Anggaran</h3>
                    <p>Membuat usulan dan rencana anggaran untuk berbagai kegiatan operasional dan pengembangan di lingkungan Polres Garut secara terstruktur.</p>
                    <a href="{{ route('pengajuan.create') }}" style="color: #B8860B;">Akses Layanan &rarr;</a>
                </div>
                <div class="service-card shadow-md" style="border-top-color: #3B82F6;">
                    <div class="icon" style="color: #3B82F6;">&#x1f4b8;</div>
                    <h3>Review & Persetujuan</h3>
                    <p>Memantau status pengajuan yang telah dikirim dan melihat riwayat persetujuan atau penolakan anggaran oleh pimpinan.</p>
                    <a href="{{ auth()->check() && auth()->user()->role == 'admin' ? route('admin.pengajuan.index') : route('pengajuan.index') }}" style="color: #1D4ED8;">Akses Layanan &rarr;</a>
                </div>
                <div class="service-card shadow-md" style="border-top-color: #10B981;">
                    <div class="icon" style="color: #10B981;">&#x1f4d1;</div>
                    <h3>Laporan & Arsip</h3>
                    <p>Mengakses laporan terpusat, mengunduh data pengajuan dalam format PDF, dan melihat arsip digital untuk keperluan audit dan perencanaan.</p>
                    <a href="{{ auth()->check() && auth()->user()->role == 'admin' ? route('admin.pengajuan.index') : '#' }}" style="color: #047857;">Akses Layanan &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN TENTANG KAMI -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="about-content">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">Tentang Sistem Kami</h2>
                        <p class="text-gray-600 mb-6">Sistem Informasi Perencanaan Anggaran (SIPA) Bagren Polres Garut adalah platform digital terintegrasi yang dirancang untuk merevolusi cara pengelolaan anggaran. Tujuan kami adalah menciptakan alur kerja yang transparan, efisien, dan akuntabel dari pengajuan hingga persetujuan.</p>
                        <!-- ... daftar fitur ... -->
                        <a href="#" class="mt-4 inline-block font-semibold text-blue-600 hover:text-blue-800">Lihat Selengkapnya &rarr;</a>
                    </div>
                </div>
                <div class="about-image">
                    <img src="{{ asset('images/login_illustration.png') }}" alt="Fitur Aplikasi" class="rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN CONTACT US -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Hubungi Kami</h2>
                <p class="text-gray-600 mt-2">Kami siap membantu Anda. Hubungi kami melalui detail di bawah ini.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="contact-info bg-white p-8 rounded-lg shadow-lg">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Email</h4>
                            <p>bagren@polresgarut.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                           <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Telepon</h4>
                            <p>(0262) 123-4567</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Alamat</h4>
                            <p>Jl. Jend. Sudirman No.204, Sucikaler, Kec. Karangpawitan, <br>Kabupaten Garut, Jawa Barat 44182</p>
                        </div>
                    </div>
                </div>
                <div class="contact-map h-96 rounded-lg overflow-hidden shadow-lg">
                    <!-- PETA GOOGLE MAPS YANG SUDAH DIPERBARUI -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.261270281201!2d107.8966296749988!3d-7.211246792801452!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68b0f025178a99%3A0x1b181283995874a!2sPolres%20Garut!5e0!3m2!1sen!2sid!4v1726583777553!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        const animatedElements = document.querySelectorAll('.about-content, .about-image, .contact-info, .contact-map');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    </script>
</x-app-layout>

