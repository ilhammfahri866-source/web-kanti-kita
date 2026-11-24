<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lokasi & Geofence Sekolah
    |--------------------------------------------------------------------------
    | Koordinat sekolah (Latitude, Longitude) dan batas radius pemesanan.
    | Siswa hanya dapat memesan jika terdeteksi di dalam radius sekolah.
    */
    'school_latitude' => (float) env('CANTEEN_SCHOOL_LAT', -6.2088),
    'school_longitude' => (float) env('CANTEEN_SCHOOL_LNG', 106.8456),
    'school_radius_meters' => (int) env('CANTEEN_SCHOOL_RADIUS', 500),
    'require_school_location' => (bool) env('CANTEEN_REQUIRE_LOCATION', false),
    'school_name' => env('CANTEEN_SCHOOL_NAME', 'Area Sekolah KantinKita'),
];

