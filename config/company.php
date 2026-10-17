<?php

return [
    'name' => env('COMPANY_NAME', 'SI MARKET'),
    'address' => env('COMPANY_ADDRESS', 'Jl. Raya Pasar No. 10, Kota Anda, Indonesia'),
    'phone' => env('COMPANY_PHONE', '(021) 1234-5678'),
    'email' => env('COMPANY_EMAIL', 'simarket@example.com'),

    'ttd_dibuat' => env('TTD_DIBUAT', 'Pemilik Toko'),
    'ttd_dibuat_nip' => env('TTD_DIBUAT_NIP', '-'),
    'ttd_diperiksa' => env('TTD_DIPERIKSA', 'Kasir'),
    'ttd_diperiksa_nip' => env('TTD_DIPERIKSA_NIP', '-'),
    'ttd_menyetujui' => env('TTD_MENYETUJUI', 'Pengelola'),
    'ttd_menyetujui_nip' => env('TTD_MENYETUJUI_NIP', '-'),
];
