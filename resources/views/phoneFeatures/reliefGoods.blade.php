<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidFlow | Relief Goods Scanner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <h1 class="text-5xl font-bold text-black mb-4">Relief Goods Scanner</h1>
            <p class="text-gray-700 mb-6">Scan relief goods packages to track inventory and verify distribution.</p>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Package Scanner</h2>
                    <div id="scanner"
                        class="w-full h-72 rounded-2xl bg-white border border-dashed border-gray-300 flex items-center justify-center text-gray-500">
                        Scanner placeholder
                    </div>
                    <p class="text-sm text-gray-500 mt-3">Point your camera at the relief package QR code.</p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Manual Package Data</h2>
                    <form method="POST" action="#" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Package ID</label>
                            <input type="text" name="package_id" placeholder="Enter package ID"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Contents</label>
                            <input type="text" name="contents" placeholder="Describe package contents"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <button type="submit"
                            class="w-full px-6 py-4 bg-red-600 text-white rounded-2xl text-2xl font-bold hover:bg-red-700 transition">
                            <i class="fas fa-check mr-2"></i> Save Package
                        </button>
                    </form>
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
</body>

</html>