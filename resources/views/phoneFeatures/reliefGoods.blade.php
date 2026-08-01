<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>AidFlow | Relief Goods Scanner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        #scanner {
            border: 0 !important;
            background: #fff;
        }

        #scanner video {
            width: 100% !important;
            height: auto !important;
            object-fit: cover;
        }

        #scanner__scan_region {
            min-height: 18rem;
            background: #030712;
        }

        #scanner__dashboard {
            padding: 1rem;
            background: #fff;
            color: #374151;
        }

        #scanner__dashboard select,
        #scanner__dashboard button {
            max-width: 100%;
            margin: .35rem;
            border: 1px solid #d1d5db;
            border-radius: .75rem;
            padding: .65rem .85rem;
            background: #fff;
            color: #1f2937;
        }

        #scanner__dashboard button {
            background: #dc2626;
            border-color: #dc2626;
            color: #fff;
            font-weight: 600;
        }

        #scanner img[alt="Info icon"] {
            display: none;
        }

        #scanner a {
            color: #b91c1c !important;
        }
    </style>
</head>

<body class="min-h-screen bg-[#EFF2F3] text-[#1F1F1F]">
    <div class="flex w-full items-center gap-3 bg-red-500 px-4 py-3 sm:px-6">
        <img src="{{ asset('assets/CSWD.png') }}" alt="Logo"
            class="h-14 w-14 shrink-0 rounded-full object-cover sm:h-20 sm:w-20">
        <div>
            <h1 class="text-2xl font-bold leading-none text-white sm:text-4xl">AidFlow</h1>
            <p class="mt-1 max-w-xl text-xs leading-snug text-red-50 sm:text-base">City Social Welfare and Development
                of San Juan City</p>
        </div>
    </div>

    <main class="mx-auto flex w-full max-w-4xl flex-col gap-5 p-3 sm:p-6">
        <div class="rounded-3xl bg-white p-4 shadow-lg sm:p-7">
            <h1 class="text-3xl font-bold leading-tight text-black sm:text-5xl">Relief Goods Scanner</h1>
            <p class="mt-2 mb-5 text-base leading-relaxed text-gray-600 sm:text-lg">Scan relief goods packages to track
                inventory and verify distribution.</p>

            <div class="grid grid-cols-1 gap-6">
                <div class="rounded-3xl border border-gray-200 bg-gray-50 p-4 sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Package Scanner</h2>
                    <div class="relative mx-auto w-full max-w-xl overflow-hidden rounded-2xl bg-black shadow-inner">
                        <div id="scanner" class="w-full bg-white text-gray-600">Scanner placeholder</div>
                    </div>
                    <p id="scanner-status" role="status" aria-live="polite"
                        class="mt-3 text-center text-sm text-gray-600">Align the package QR code inside the frame.</p>
                </div>
            </div>
        </div>

        <a href="{{ route('phoneFeatures') }}"
            class="block text-center px-4 py-4 bg-gray-100 rounded-3xl font-semibold text-gray-800 hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Home Page
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full p-4 text-center">
            @csrf
            <button type="submit"
                class="w-full px-4 py-3 bg-red-900 rounded-lg hover:bg-red-800 text-white font-bold text-2xl">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </main>

    <script>
        const scannerStatus = document.getElementById('scanner-status');
        let lastScannedCode = null;
        let lastScannedAt = 0;

        function onScanSuccess(decodedText) {
            const packageId = decodedText.trim();
            const now = Date.now();

            if (!packageId || (packageId === lastScannedCode && now - lastScannedAt < 2000)) {
                return;
            }

            lastScannedCode = packageId;
            lastScannedAt = now;
            scannerStatus.textContent = `Package scanned: ${packageId}`;
            scannerStatus.classList.remove('text-gray-600', 'text-red-600');
            scannerStatus.classList.add('font-medium', 'text-green-600');
        }

        function onScanFailure() {
            // Unreadable frames are normal while the QR code is being aligned.
        }

        if (typeof Html5QrcodeScanner !== 'undefined') {
            const scanner = new Html5QrcodeScanner('scanner', {
                fps: 10,
                qrbox: (width, height) => {
                    const size = Math.floor(Math.min(width, height) * 0.7);
                    return { width: size, height: size };
                },
                rememberLastUsedCamera: true,
                aspectRatio: 4 / 3,
            }, false);

            scanner.render(onScanSuccess, onScanFailure);
        } else {
            scannerStatus.textContent = 'Camera scanner is unavailable in this browser.';
            scannerStatus.classList.add('text-red-600');
        }
    </script>
</body>

</html>