<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVOTA - Login</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            -webkit-font-smoothing: antialiased;
        }
        /* Subtle texture for professional depth */
        .bg-texture {
            background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.02) 1px, transparent 0);
            background-size: 24px 24px;
        }
        /* Active button scale interaction */
        .btn-active:active {
            transform: scale(0.96);
        }
        .input-focus-glow:focus-within {
            box-shadow: 0 0 0 4px rgba(0, 145, 218, 0.1);
        }
    </style>
</head>
<body class="bg-background text-on-surface min-h-screen flex flex-col items-center overflow-x-hidden bg-texture transition-all selection:bg-primary-container selection:text-on-primary-container">
    
    @yield('content')

    <!-- Footer -->
    <footer class="w-full py-8 flex flex-col items-center gap-2 mt-auto relative z-10">
        <div class="flex gap-4 md:gap-6 mb-2">
            <a class="font-label-sm text-label-sm md:text-[14px] text-on-surface-variant hover:text-primary transition-colors" href="#">Privacy Policy</a>
            <a class="font-label-sm text-label-sm md:text-[14px] text-on-surface-variant hover:text-primary transition-colors" href="#">Terms of Service</a>
            <a class="font-label-sm text-label-sm md:text-[14px] text-on-surface-variant hover:text-primary transition-colors" href="#">Contact Support</a>
        </div>
        <p class="font-label-sm text-label-sm md:text-[14px] text-on-surface-variant text-center opacity-70">
            &copy; {{ date('Y') }} Badan Pusat Statistik (BPS). All rights reserved.
        </p>
    </footer>

    @stack('scripts')
</body>
</html>
