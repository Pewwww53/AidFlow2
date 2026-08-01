<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidFlow | Add Inventory Items</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col gap-4 min-h-screen bg-[#EFF2F3] text-[#1F1F1F] items-center">
    <div class="flex flex-row gap-2 w-full h-50 bg-red-500 items-center">
        <img accesskey="" src="{{ asset('assets/CSWD.png') }}" alt="Logo" class="w-30 h-30 rounded-full">
        <div>
            <h1 class="text-5xl font-bold text-white">AidFlow</h1>
            <p class="text-lg text-gray-100">
                City Social Welfare and Development of San Juan City
            </p>
        </div>
    </div>
    <main
        class=" bg-white/80 backdrop-blur-xl rounded-4xl border border-gray-200 shadow-xl shadow-gray-200/40 p-4 w-full max-w-2xl">
        <div class="text-center mb-6">
            <h2 class="text-4xl font-extrabold tracking-tight text-[#1F1F1F]">Add Inventory Item</h2>
            <p class="text-sm text-gray-500 mt-2">Add new stock quickly with all item details.</p>
        </div>

        <form method="POST" action="{{ route('phoneFeatures.addInventory.store') }}" class="space-y-4">
            @csrf
            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            <div>
                <label for="item_name" class="ml-4 font-bold">Item Name</label>
                <input id="item_name" name="item_name" type="text" placeholder="Item Name" required
                    class="w-full rounded-[28px] border border-gray-300 bg-gray-100 px-5 py-4 text-lg text-[#1F1F1F] placeholder:text-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100" />
            </div>

            <div>
                <label for="quantity" class="ml-4 font-bold">Quantity</label>
                <input id="quantity" name="quantity" type="number" min="0" placeholder="Quantity" required
                    class="w-full rounded-[28px] border border-gray-300 bg-gray-100 px-5 py-4 text-lg text-[#1F1F1F] placeholder:text-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100" />
            </div>

            <div>
                <label for="category" class="ml-4 font-bold">Category</label>
                <select id="category" name="category" required
                    class="w-full rounded-[28px] border border-gray-300 bg-gray-100 px-5 py-4 text-lg text-[#1F1F1F] focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100">
                    <option value="" disabled selected>Category</option>
                    <option>Food</option>
                    <option>Medicine</option>
                    <option>Water</option>
                    <option>Equipment</option>
                    <option>Other</option>
                </select>
            </div>

            <div>
                <label for="unit" class="ml-4 font-bold">Unit of Measurement</label>
                <select id="unit" name="unit" required
                    class="w-full rounded-[28px] border border-gray-300 bg-gray-100 px-5 py-4 text-lg text-[#1F1F1F] focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100">
                    <option value="" disabled selected>Unit of Measurement</option>
                    <option>Piece</option>
                    <option>Box</option>
                    <option>Pack</option>
                    <option>Litre</option>
                    <option>Kg</option>
                </select>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="date_received" class="ml-4 font-bold">Date Received</label>
                    <input id="date_received" name="date_received" type="date"
                        class="w-full rounded-[28px] border border-gray-300 bg-gray-100 px-5 py-4 text-lg text-[#1F1F1F] focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100" />
                </div>
                <div>
                    <label for="expiration_date" class="ml-4 font-bold">Expiration Date</label>
                    <input id="expiration_date" name="expiration_date" type="date"
                        class="w-full rounded-[28px] border border-gray-300 bg-gray-100 px-5 py-4 text-lg text-[#1F1F1F] focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100" />
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-full bg-white border border-black px-6 py-4 text-xl font-semibold text-black shadow-sm hover:bg-gray-100 transition">
                Submit
            </button>
        </form>

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
    </div>
</body>

</html>