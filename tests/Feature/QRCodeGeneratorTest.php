<?php

use App\Http\Controllers\Features\QRCodeController;

it('renders a qr generator with tent and food pack options', function () {
    $html = view('features.qrcode.index')->render();

    expect($html)
        ->toContain('Generate QR Code')
        ->toContain('Tent Code')
        ->toContain('Food Pack');
});
