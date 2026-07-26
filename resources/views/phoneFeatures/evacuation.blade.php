<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidFlow | Evacuation Tent Scanner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
</head>

<body class="flex flex-col gap-4 min-h-screen bg-[#EFF2F3] text-[#1F1F1F] items-center">
    <div class="flex flex-row gap-2 w-full h-50 bg-red-500 items-center p-4">
        <img src="{{ asset('assets/CSWD.png') }}" alt="Logo" class="w-30 h-30 rounded-full">
        <div>
            <h1 class="text-5xl font-bold text-white">AidFlow</h1>
            <p class="text-lg text-gray-100">City Social Welfare and Development of San Juan City</p>
        </div>
    </div>

    <main class="flex flex-col gap-6 w-full max-w-4xl p-4">
        <div class="bg-white rounded-3xl shadow-lg p-6">
            <h1 class="text-5xl font-bold text-black mb-4">Evacuation Tent Scanner</h1>
            <p class="text-gray-700 mb-6">Scan tents to check occupancy and evacuation status quickly.</p>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">QR Scanner</h2>
                    <div
                        class="relative mx-auto w-full max-w-xl h-80 rounded-3xl overflow-hidden bg-black shadow-inner">
                        <div id="scanner" class="w-full h-full bg-black flex items-center justify-center text-gray-400">
                            Scanner placeholder
                        </div>
                        <div class="pointer-events-none absolute inset-0">
                            <div class="absolute top-4 left-4 w-14 h-14 border-4 border-white rounded-2xl"></div>
                            <div class="absolute top-4 right-4 w-14 h-14 border-4 border-white rounded-2xl"></div>
                            <div class="absolute bottom-4 left-4 w-14 h-14 border-4 border-white rounded-2xl"></div>
                            <div class="absolute bottom-4 right-4 w-14 h-14 border-4 border-white rounded-2xl"></div>
                            <div class="absolute inset-x-0 top-1/2 h-0.5 bg-red-500 opacity-80 animate-pulse"></div>
                        </div>
                    </div>
                    <p id="scanner-status" class="text-sm text-gray-200 mt-3">Align the QR code inside the frame for
                        best results.</p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Manual Tent Entry</h2>
                    <form id="manual-scan-form" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tent Number</label>
                            <input id="tent-code" type="text" name="tent_code" placeholder="Enter tent number"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Barangay Code</label>
                            <input id="barangay-code" type="text" name="barangay_code" placeholder="Enter barangay code"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <button type="submit"
                            class="w-full px-6 py-4 bg-red-600 text-white rounded-2xl text-2xl font-bold hover:bg-red-700 transition">
                            <i class="fas fa-search mr-2"></i> Save Tent Scan
                        </button>
                    </form>
                    <div id="scan-result" class="mt-4 text-sm font-medium text-green-600 hidden"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('phoneFeatures') }}"
                class="block text-center px-4 py-4 bg-gray-100 rounded-3xl font-semibold text-gray-800 hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Scanner Menu
            </a>
            <a href="{{ route('logout') }}"
                class="block text-center px-4 py-4 bg-red-900 rounded-3xl font-semibold text-white hover:bg-red-800">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </main>

    <script>
        const scanStatus = document.getElementById('scanner-status');
        const scanResult = document.getElementById('scan-result');
        const form = document.getElementById('manual-scan-form');
        const tentCodeInput = document.getElementById('tent-code');
        const barangayCodeInput = document.getElementById('barangay-code');

        function showMessage(message, isSuccess = true) {
            scanResult.classList.remove('hidden', 'text-green-600', 'text-red-600');
            scanResult.classList.add(isSuccess ? 'text-green-600' : 'text-red-600');
            scanResult.textContent = message;
            scanResult.classList.remove('hidden');
        }

        async function submitScan(tentCode, barangayCode = null) {
            const response = await fetch('{{ route("phoneFeatures.evacuation.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tent_code: tentCode, barangay_code: barangayCode })
            });

            const data = await response.json();
            if (data.success) {
                showMessage(`Tent ${tentCode} recorded successfully.`);
                tentCodeInput.value = tentCode;
                if (barangayCode) {
                    barangayCodeInput.value = barangayCode;
                }
            } else {
                showMessage('Unable to record scan.', false);
            }
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const tentCode = tentCodeInput.value.trim();
            if (!tentCode) {
                showMessage('Please enter a tent number.', false);
                return;
            }
            await submitScan(tentCode, barangayCodeInput.value.trim() || null);
        });

        function onScanSuccess(decodedText) {
            const tentCode = decodedText.trim();
            if (!tentCode) {
                return;
            }
            scanStatus.textContent = `Scanned: ${tentCode}`;
            submitScan(tentCode);
        }

        function onScanFailure() {
            scanStatus.textContent = 'Unable to read QR code. Please try again.';
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