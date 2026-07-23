@extends('features.layout')

@section('title', 'Evacuation - AidFlow')
@section('page-title', 'Evacuation')

@section('content')
    <style>
        .map-label-tooltip {
            background: rgba(255, 255, 255, 0.88);
            color: #1f2937;
            border: 1px solid rgba(148, 163, 184, 0.45);
            border-radius: 0.75rem;
            padding: 0.25rem 0.5rem;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.12);
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            pointer-events: none;
        }

        .map-label-tooltip.leaflet-tooltip-bottom {
            margin-top: -6px;
        }
    </style>
    <div class="flex gap-4 min-h-[calc(100vh-120px)]">
        <div class="bg-white rounded-[20px] border-[3px] border-[#8a1c1c] p-4 shadow-sm">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-[#960505]">Evacuation List</h2>
                        <p class="text-sm text-gray-600">Current occupied tents and barangay evacuation records.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button id="resetBtn"
                            class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">Reset</button>
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:flex-row lg:items-start">
                    <label class="sr-only" for="searchInput">Search tent ID</label>
                    <div class="flex items-center gap-2 border border-gray-300 rounded-lg px-3 py-2 bg-white flex-1">
                        <i class="fas fa-search text-gray-500"></i>
                        <input id="searchInput" type="search" placeholder="Search Tent ID"
                            class="w-full border-none bg-transparent text-sm text-gray-700 outline-none" />
                    </div>

                    <label class="sr-only" for="barangayFilter">Barangay</label>
                    <select id="barangayFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#960505] lg:w-[220px]">
                        <option value="all">All</option>
                        <option>Balong-Bato</option>
                        <option>Salapan</option>
                        <option>West Crame</option>
                        <option>Greenhills</option>
                        <option>Santa Lucia</option>
                        <option>Addition Hills</option>
                        <option>San Perfecto</option>
                        <option>Corazon de Jesus</option>
                        <option>Pasadena</option>
                        <option>Little Baguio</option>
                        <option>Maytunas</option>
                        <option>Rivera</option>
                        <option>Pedro Cruz</option>
                        <option>Progreso</option>
                        <option>Tibagan</option>
                    </select>
                </div>

                <div class="border border-gray-300 rounded-[14px] overflow-hidden">
                    <div class="overflow-x-auto ">
                        <table class="min-w-full text-sm text-left text-gray-800">
                            <thead class="bg-[#960505] text-white">
                                <tr>
                                    <th class="px-3 py-3 font-normal">Date</th>
                                    <th class="px-3 py-3 font-normal">Tent ID</th>
                                    <th class="px-3 py-3 font-normal">Barangay</th>
                                </tr>
                            </thead>
                            <tbody id="evacuationTableBody">
                                @forelse($recentScannedTents as $scan)
                                    @php
                                        $scanDate = !empty($scan['scannedAt']) ? \Carbon\Carbon::parse($scan['scannedAt'])->format('m/d/Y') : date('m/d/Y');
                                        $tentCode = $scan['tentCode'] ?? $scan['tent_id'] ?? 'T-001';
                                        $barangay = $scan['barangay'] ?? $scan['barangayName'] ?? $scan['barangay_name'] ?? 'Balong-Bato';
                                    @endphp
                                    <tr class="border-b border-gray-200 last:border-0">
                                        <td class="px-3 py-3 whitespace-nowrap">{{ $scanDate }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap">{{ $tentCode }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap">{{ $barangay }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/15/2026</td>
                                        <td class="px-3 py-3">BB-001</td>
                                        <td class="px-3 py-3">Balong-Bato</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/15/2026</td>
                                        <td class="px-3 py-3">BB-002</td>
                                        <td class="px-3 py-3">Balong-Bato</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/15/2026</td>
                                        <td class="px-3 py-3">BB-003</td>
                                        <td class="px-3 py-3">Balong-Bato</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/15/2026</td>
                                        <td class="px-3 py-3">BB-004</td>
                                        <td class="px-3 py-3">Balong-Bato</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/17/2026</td>
                                        <td class="px-3 py-3">S-001</td>
                                        <td class="px-3 py-3">Salapan</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/17/2026</td>
                                        <td class="px-3 py-3">S-002</td>
                                        <td class="px-3 py-3">Salapan</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/17/2026</td>
                                        <td class="px-3 py-3">S-003</td>
                                        <td class="px-3 py-3">Salapan</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-3 py-3">06/25/2026</td>
                                        <td class="px-3 py-3">WC-001</td>
                                        <td class="px-3 py-3">West Crame</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-3">06/25/2026</td>
                                        <td class="px-3 py-3">WC-002</td>
                                        <td class="px-3 py-3">West Crame</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-4">
            <div class="flex flex-col gap-4 sm:flex-row">
                <div
                    class="bg-white rounded-[20px] border-[3px] border-[#8a1c1c] p-4 shadow-sm flex-1 flex items-center gap-3">
                    <div
                        class="w-14 h-14 rounded-[18px] border-[3px] border-[#8a1c1c] bg-[#ff6b6b] flex items-center justify-center text-[#1f0000]">
                        <i class="fas fa-campground text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-700 mb-0.5">Occupied Tents</p>
                        <p class="text-[28px] leading-none font-medium">{{ number_format($occupiedTentsCount ?? 0) }}</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-[20px] border-[3px] border-[#8a1c1c] p-4 shadow-sm flex-1 flex items-center gap-3">
                    <div
                        class="w-14 h-14 rounded-[18px] border-[3px] border-[#8a1c1c] bg-[#ff6b6b] flex items-center justify-center text-[#1f0000]">
                        <i class="fas fa-university text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-700 mb-0.5">Occupied Barangays</p>
                        <p class="text-[28px] leading-none font-medium">{{ number_format($barangayCount ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 bg-white rounded-[20px] border-[3px] border-[#8a1c1c] p-4 shadow-sm h-full">
                <h2 class="text-lg text-[#be3d3d]">Barangay Occupancy</h2>
                <div class="border border-gray-400 w-full h-full rounded-[14px] overflow-hidden bg-gray-50">
                    <div id="evacuation-map" class="w-full h-full"></div>
                </div>

            </div>
        </div>
    </div>


    @push('scripts')
        @php
            $tableRows = $recentScannedTents->isNotEmpty()
                ? $recentScannedTents->map(function ($scan) {
                    return [
                        'date' => !empty($scan['scannedAt'])
                            ? \Carbon\Carbon::parse($scan['scannedAt'])->format('m/d/Y')
                            : date('m/d/Y'),
                        'tentId' => $scan['tentCode'] ?? $scan['tent_id'] ?? 'T-001',
                        'barangay' => $scan['barangay'] ?? $scan['barangayName'] ?? $scan['barangay_name'] ?? 'Balong-Bato',
                    ];
                })->values()
                : collect([
                    ['date' => '06/15/2026', 'tentId' => 'BB-001', 'barangay' => 'Balong-Bato'],
                    ['date' => '06/15/2026', 'tentId' => 'BB-002', 'barangay' => 'Balong-Bato'],
                    ['date' => '06/15/2026', 'tentId' => 'BB-003', 'barangay' => 'Balong-Bato'],
                    ['date' => '06/15/2026', 'tentId' => 'BB-004', 'barangay' => 'Balong-Bato'],
                    ['date' => '06/17/2026', 'tentId' => 'S-001', 'barangay' => 'Salapan'],
                    ['date' => '06/17/2026', 'tentId' => 'S-002', 'barangay' => 'Salapan'],
                    ['date' => '06/17/2026', 'tentId' => 'S-003', 'barangay' => 'Salapan'],
                    ['date' => '06/25/2026', 'tentId' => 'WC-001', 'barangay' => 'West Crame'],
                    ['date' => '06/25/2026', 'tentId' => 'WC-002', 'barangay' => 'West Crame'],
                ]);
        @endphp
        <script type="module">
            import { barangayList } from '/js/barangayList.js';

            const tableRows = @json($tableRows);


            const occupancyData = @json($occupancyData ?? []);
            const searchInput = document.getElementById('searchInput');
            const barangayFilter = document.getElementById('barangayFilter');
            const resetBtn = document.getElementById('resetBtn');
            const tableBody = document.getElementById('evacuationTableBody');

            const renderTable = (rows) => {
                tableBody.innerHTML = rows.map(row => `
                                                                                                                        <tr class="border-b border-gray-200 last:border-0">
                                                                                                                        <td class="px-3 py-3 whitespace-nowrap">${row.date}</td>
                                                                                                                        <td class="px-3 py-3 whitespace-nowrap">${row.tentId}</td>
                                                                                                                        <td class="px-3 py-3 whitespace-nowrap">${row.barangay}</td>
                                                                                                                        </tr>
                                                                                                `).join('');
            };

            const applyFilters = () => {
                const text = searchInput.value.trim().toLowerCase();
                const barangay = barangayFilter.value;
                const filtered = tableRows.filter(row => {
                    const matchesText = !text || row.tentId.toLowerCase().includes(text) || row.date.toLowerCase().includes(text) || row.barangay.toLowerCase().includes(text);
                    const matchesBarangay = barangay === 'all' || !barangay || row.barangay === barangay;
                    return matchesText && matchesBarangay;
                });
                renderTable(filtered);
            };

            searchInput.addEventListener('input', applyFilters);
            barangayFilter.addEventListener('change', applyFilters);

            resetBtn.addEventListener('click', () => {
                searchInput.value = '';
                barangayFilter.value = 'all';
                renderTable(tableRows);
            });

            renderTable(tableRows);

            const barangayMap = {};
            barangayList.forEach(b => {
                barangayMap[b.name] = {
                    code: b.code,
                    maxTents: Object.keys(b.tents).length,
                };
            });

            const getStyleForFeature = (feature) => {
                const info = barangayMap[feature.properties.name];
                if (!info) {
                    return { color: '#555', fillColor: '#999', fillOpacity: 0.4, weight: 1.5 };
                }
                const occupied = occupancyData[info.code] ?? 0;
                const ratio = info.maxTents > 0 ? occupied / info.maxTents : 0;
                let fill = '#289432';
                let border = '#1C4D23';
                let className = 'glow-green';
                if (ratio > 0) {
                    fill = '#948628';
                    border = '#c2410c';
                    className = 'glow-amber';
                }
                if (ratio >= 0.8) {
                    fill = '#ef4444';
                    border = '#991b1b';
                    className = 'glow-red';
                }
                return {
                    color: border,
                    fillColor: fill,
                    fillOpacity: 0.7,
                    weight: 1.5,
                    className: className,
                };
            };

            const getTooltipText = (feature) => {
                const info = barangayMap[feature.properties.name];
                if (!info) return feature.properties.name || 'Unknown';
                const occupied = occupancyData[info.code] ?? 0;
                const ratio = info.maxTents > 0 ? occupied / info.maxTents : 0;
                const percent = Math.round(ratio * 100);
                return `${feature.properties.name} — ${occupied}/${info.maxTents} occupied (${percent}%)`;
            };

            const updateLayerTooltips = (geoLayer) => {
                if (!geoLayer) return;
                geoLayer.eachLayer((layer) => {
                    const feature = layer.feature;
                    if (!feature?.properties?.name) return;
                    const tooltipText = getTooltipText(feature);
                    if (layer.getTooltip()) {
                        layer.setTooltipContent(tooltipText);
                    } else {
                        layer.bindTooltip(tooltipText, {
                            direction: 'center',
                            className: 'map-label-tooltip',
                        });
                    }
                    layer.setStyle(getStyleForFeature(feature));
                });
            };

            document.addEventListener('DOMContentLoaded', () => {
                const map = L.map('evacuation-map', {
                    zoomControl: false,
                    dragging: false,
                    doubleClickZoom: false,
                    scrollWheelZoom: false,
                    attributionControl: false,
                });

                fetch('/geojson/sanjuan.geojson')
                    .then(response => response.json())
                    .then(data => {
                        const geoLayer = L.geoJSON(data, {
                            pointToLayer(feature, latlng) {
                                return L.circleMarker(latlng, { radius: 0, opacity: 0, fillOpacity: 0 });
                            },
                            style(feature) {
                                return getStyleForFeature(feature);
                            },
                            onEachFeature(feature, layer) {
                                const info = barangayMap[feature.properties.name];
                                if (!info) return;
                                layer.bindTooltip(getTooltipText(feature), {
                                    direction: 'center',
                                    className: 'map-label-tooltip',
                                });
                                layer.on('mouseover', () => {
                                    const el = layer.getElement ? layer.getElement() : layer._path;
                                    if (el) el.classList.add('glow-hover');
                                    if (layer.bringToFront) layer.bringToFront();
                                });
                                layer.on('mouseout', () => {
                                    const el = layer.getElement ? layer.getElement() : layer._path;
                                    if (el) el.classList.remove('glow-hover');
                                    try { layer.setStyle(getStyleForFeature(feature)); } catch (e) { }
                                });
                            },
                        }).addTo(map);

                        updateLayerTooltips(geoLayer);
                        setTimeout(() => {
                            map.invalidateSize();
                            const bounds = geoLayer.getBounds();
                            if (bounds.isValid()) {
                                map.fitBounds(bounds, { padding: [16, 16], maxZoom: 16 });
                            }
                        }, 200);
                    });

                if (window.firebaseDatabase && typeof ref === 'function' && typeof onValue === 'function') {
                    const occupancyRef = ref(window.firebaseDatabase, 'occupiedTents');
                    onValue(occupancyRef, (snapshot) => {
                        const data = snapshot.val() ?? {};
                        Object.keys(occupancyData).forEach(key => delete occupancyData[key]);
                        Object.values(data).forEach(entry => {
                            const barangayCode = entry?.barangayCode ?? entry?.barangay_code ?? null;
                            if (barangayCode) {
                                occupancyData[barangayCode] = (occupancyData[barangayCode] ?? 0) + 1;
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection