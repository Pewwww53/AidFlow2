@extends('features.layout')

@section('title', 'QR Code Scanner - AidFlow')
@section('page-title', 'QR Code Scanner')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-900">Tent QR Code Scanner</h2>

    <!-- Scanner Container -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Scanner</h3>
            <div id="scanner" style="width: 100%;"></div>
        </div>

        <!-- Manual Input -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Manual Entry</h3>
            <form id="manualForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tent Code</label>
                    <input type="text" id="tentCode" placeholder="Enter tent code" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barangay Code (Optional)</label>
                    <input type="text" id="barangayCode" placeholder="Enter barangay code" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-check mr-2"></i> Record Scan
                </button>
            </form>

            <!-- Recent Scans -->
            <div class="mt-6">
                <h4 class="font-bold text-gray-900 mb-3">Recent Scans</h4>
                <div id="recentScans" class="space-y-2 max-h-96 overflow-y-auto">
                    <p class="text-gray-600 text-sm">No scans yet</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import QR Scanner Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    let recentScans = [];

    function onScanSuccess(decodedText, decodedResult) {
        recordScan(decodedText);
    }

    function onScanFailure(error) {
        // Ignore scan failures
    }

    // Initialize scanner
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "scanner",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    // Manual form submission
    document.getElementById('manualForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const tentCode = document.getElementById('tentCode').value;
        recordScan(tentCode, document.getElementById('barangayCode').value);
        document.getElementById('manualForm').reset();
    });

    function recordScan(tentCode, barangayCode = null) {
        fetch('{{ route("qrcode.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tent_code: tentCode,
                barangay_code: barangayCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addRecentScan(tentCode, barangayCode);
                showNotification('Scan recorded successfully', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error recording scan', 'error');
        });
    }

    function addRecentScan(tentCode, barangayCode) {
        const now = new Date().toLocaleTimeString();
        recentScans.unshift({ tentCode, barangayCode, time: now });
        
        if (recentScans.length > 5) {
            recentScans.pop();
        }
        
        updateRecentScans();
    }

    function updateRecentScans() {
        const container = document.getElementById('recentScans');
        if (recentScans.length === 0) {
            container.innerHTML = '<p class="text-gray-600 text-sm">No scans yet</p>';
            return;
        }
        
        container.innerHTML = recentScans.map(scan => `
            <div class="p-2 bg-gray-100 rounded text-sm">
                <div class="font-medium text-gray-900">${scan.tentCode}</div>
                <div class="text-gray-600">${scan.barangayCode || 'N/A'} • ${scan.time}</div>
            </div>
        `).join('');
    }

    function showNotification(message, type) {
        // Simple notification - you can enhance this
        alert(message);
    }

    // Add CSRF token meta tag if not present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const token = document.createElement('meta');
        token.name = 'csrf-token';
        token.content = '{{ csrf_token() }}';
        document.head.appendChild(token);
    }
</script>
@endsection
