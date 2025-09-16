<x-app-layout>
    {{-- Kita tidak menggunakan header bawaan, jadi biarkan slot ini kosong --}}
    <x-slot name="header"></x-slot>

    {{-- CSS Khusus untuk Hero Section --}}
    <style>
         main {
        padding-top: 0 !important;
    }
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
            text-align: left;
            padding: 2rem;
            max-width: 50%;
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
    </style>

    <div class="hero-section">
        <div class="hero-background"></div>
        
        <div class="hero-content">
            <h1>Sistem Informasi Perencanaan Anggaran</h1>
            <p>Profesional, Modern, dan Terpercaya dalam pengelolaan anggaran Bagren Polres Garut.</p>
        </div>

        <img src="{{ asset('images/foto-kapolres.png') }}" alt="Kapolres" class="hero-image">
    </div>

    {{-- Perhatikan: div di bawah ini memiliki padding (py-12), tapi hero-section tidak --}}
    

</x-app-layout>