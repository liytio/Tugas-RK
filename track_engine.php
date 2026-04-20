<?php
header('Content-Type: application/json');

// 1. Target Alumni (Dalam sistem nyata, ini diambil dari Database)
$namaAlumni = "Muhammad Rizky";

// 2. Membangun URL API Crossref
$queryNama = urlencode($namaAlumni);
$urlApi = "https://api.crossref.org/works?query.author={$queryNama}&select=title,author,URL,publisher&rows=2";

// 3. Menarik data asli dari internet menggunakan cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlApi);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// Memberikan identitas aplikasi agar tidak dianggap spam oleh API
curl_setopt($ch, CURLOPT_USERAGENT, 'AlumniTracker_Project/1.0'); 
$response = curl_exec($ch);
curl_close($ch);

// 4. Menerjemahkan dan Mengolah Data
$dataAsli = json_decode($response, true);
$kandidatDitemukan = [];

// Jika server API merespons dan menemukan data
if (isset($dataAsli['message']['items']) && count($dataAsli['message']['items']) > 0) {
    foreach ($dataAsli['message']['items'] as $item) {
        $kandidatDitemukan[] = [
            "sumber" => "Crossref (Akademik)",
            "judul" => isset($item['title'][0]) ? $item['title'][0] : 'Tidak ada judul',
            "link_asli" => isset($item['URL']) ? $item['URL'] : '#'
        ];
    }
    
    echo json_encode([
        "success" => true,
        "status_akhir" => "Teridentifikasi (Akademik)",
        "bukti_data_asli" => $kandidatDitemukan
    ]);
} else {
    echo json_encode([
        "success" => false,
        "status_akhir" => "Belum Ditemukan",
        "bukti_data_asli" => []
    ]);
}
?>