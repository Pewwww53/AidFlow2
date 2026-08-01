<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>AidFlow | Evacuation Tent Scanner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        #scanner { border: 0 !important; background: #fff; }
        #scanner video { width: 100% !important; height: auto !important; object-fit: cover; }
        #scanner__scan_region { min-height: 18rem; background: #030712; }
        #scanner__dashboard { padding: 1rem; background: #fff; color: #374151; }
        #scanner__dashboard select,
        #scanner__dashboard button { max-width: 100%; margin: .35rem; border: 1px solid #d1d5db; border-radius: .75rem; padding: .65rem .85rem; background: #fff; color: #1f2937; }
        #scanner__dashboard button { background: #dc2626; border-color: #dc2626; color: #fff; font-weight: 600; }
        #scanner img[alt="Info icon"] { display: none; }
        #scanner a { color: #b91c1c !important; }
    </style></head>

<body class="min-h-screen bg-[#EFF2F3] text-[#1F1F1F]">
    <div class="flex w-full items-center gap-3 bg-red-500 px-4 py-3 sm:px-6">
        <img src="{{ asset('assets/CSWD.png') }}" alt="Logo" class="h-14 w-14 shrink-0 rounded-full object-cover sm:h-20 sm:w-20">
        <div>
            <h1 class="text-2xl font-bold leading-none text-white sm:text-4xl">AidFlow</h1>
            <p class="mt-1 max-w-xl text-xs leading-snug text-red-50 sm:text-base">City Social Welfare and Development of San Juan City</p>
        </div>
    </div>

    <main class="mx-auto flex w-full max-w-4xl flex-col gap-5 p-3 sm:p-6">
        <div class="rounded-3xl bg-white p-4 shadow-lg sm:p-7">
            <h1 class="text-3xl font-bold leading-tight text-black sm:text-5xl">Evacuation Tent Scanner</h1>
            <p class="mt-2 mb-5 text-base leading-relaxed text-gray-600 sm:text-lg">Scan tents to check occupancy and evacuation status quickly.</p>

            <div class="grid grid-cols-1 gap-6">
                <div class="rounded-3xl border border-gray-200 bg-gray-50 p-4 sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">QR Scanner</h2>
                    <div
                        class="relative mx-auto w-full max-w-xl overflow-hidden rounded-2xl bg-black shadow-inner">
                        <div id="scanner" class="w-full bg-white text-gray-600">
                            Scanner placeholder
                        </div>


                    </div>
                    <p id="scanner-status" class="mt-3 text-center text-sm text-gray-600">Align the QR code inside the frame for
                        best results.</p>
                    <div id="scan-result" role="status" aria-live="polite" class="mt-3 hidden text-center text-sm font-medium text-green-600"></div>
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

    <script type="module">
        import { barangayList } from '/js/barangayList.js';
        const scanStatus = document.getElementById('scanner-status');
        const scanResult = document.getElementById('scan-result');
        let scanInProgress = false;
        let lastScan = { code: null, time: 0 };

        function showMessage(message, isSuccess = true) {
            scanResult.classList.remove('hidden', 'text-green-600', 'text-red-600');
            scanResult.classList.add(isSuccess ? 'text-green-600' : 'text-red-600');
            scanResult.textContent = message;
        }

        function findBarangay(tentCode) {
            return barangayList.find(barangay =>
                Object.prototype.hasOwnProperty.call(barangay.tents, tentCode)
            );
        }

        async function submitScan(tentCode, confirmUnoccupy = false) {
            if (scanInProgress) return;
            scanInProgress = true;

            try {
                const barangay = findBarangay(tentCode);
                if (!barangay) {
                    showMessage(`Tent ${tentCode} is not listed in barangayList.js.`, false);
                    return;
                }

                const response = await fetch('{{ route("phoneFeatures.evacuation.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        tent_code: tentCode,
                        barangay_code: barangay.code,
                        barangay_name: barangay.name,
                        confirm_unoccupy: confirmUnoccupy
                    })
                });

                const data = await response.json();
                if (data.requires_confirmation) {
                    if (window.confirm(data.message)) {
                        scanInProgress = false;
                        return submitScan(tentCode, true);
                    }
                    showMessage(`Tent ${tentCode} remains occupied.`, false);
                } else if (data.success) {
                    showMessage(data.message);
                } else {
                    showMessage(data.message || 'Unable to record scan.', false);
                }
            } catch (error) {
                dd(error);
                showMessage('Unable to connect to the server. Please try again.', false);
            } finally {
                scanInProgress = false;
            }
        }

        function onScanSuccess(decodedText) {
            const tentCode = decodedText.trim().toUpperCase();
            if (!tentCode) return;

            const now = Date.now();
            if (lastScan.code === tentCode && now - lastScan.time < 2500) return;
            lastScan = { code: tentCode, time: now };

            scanStatus.textContent = `Scanned: ${tentCode}`;
            submitScan(tentCode);
        }

        function onScanFailure() {
            // Called repeatedly by the scanner while it searches for a QR code.
        }

        if (typeof Html5QrcodeScanner !== 'undefined') {
            const scanner = new Html5QrcodeScanner('scanner', {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
            }, false);
            scanner.render(onScanSuccess, onScanFailure);
        } else {
            scanStatus.textContent = 'Camera scanner is unavailable in this browser.';
        }
    </script>
</body>

</html>
