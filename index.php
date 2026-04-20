<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelacakan Alumni OSINT - Ragil Tri Prasytio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <div class="min-h-screen flex flex-col">
        <nav class="bg-white border-b border-slate-200 sticky top-0 z-10">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800 leading-none">AlumniTracker</h1>
                        <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">OSINT Engine v1.0</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600 hidden md:block">Selamat Datang, <strong>Admin</strong></span>
                    <button onclick="jalankanPelacakan()" id="btn-track" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-full transition-all flex items-center gap-2 shadow-lg shadow-blue-200">
                        <i class="fas fa-search"></i>
                        <span>Mulai Pelacakan</span>
                    </button>
                </div>
            </div>
        </nav>

        <main class="container mx-auto px-6 py-8 flex-grow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="text-slate-500 text-xs font-bold uppercase mb-1">Total Target</div>
                    <div class="text-3xl font-bold">1</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-green-500">
                    <div class="text-slate-500 text-xs font-bold uppercase mb-1 text-green-600">Teridentifikasi</div>
                    <div id="stat-teridentifikasi" class="text-3xl font-bold">0</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-yellow-500">
                    <div class="text-slate-500 text-xs font-bold uppercase mb-1 text-yellow-600">Perlu Verifikasi</div>
                    <div id="stat-verifikasi" class="text-3xl font-bold">0</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-slate-400">
                    <div class="text-slate-500 text-xs font-bold uppercase mb-1">Belum Ditemukan</div>
                    <div id="stat-belum" class="text-3xl font-bold">1</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Hasil Pelacakan Publik</h2>
                    <div class="text-xs text-slate-400 italic">*Data diperbarui secara real-time via API</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                                <th class="py-4 px-6">Nama Alumni</th>
                                <th class="py-4 px-6">Program Studi</th>
                                <th class="py-4 px-6">Status OSINT</th>
                                <th class="py-4 px-6">Temuan Informasi</th>
                                <th class="py-4 px-6">Bukti</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-alumni">
                            <tr id="row-empty">
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center opacity-40">
                                        <i class="fas fa-database text-4xl mb-3"></i>
                                        <p class="text-sm">Belum ada data pelacakan yang dijalankan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-slate-200 py-6">
            <div class="container mx-auto px-6 text-center text-slate-500 text-sm">
                &copy; 2026 Alumni Tracking System. Project Daily 2 - Rekayasa Kebutuhan.
            </div>
        </footer>
    </div>

    <script>
        async function jalankanPelacakan() {
            const btn = document.getElementById('btn-track');
            const tbody = document.getElementById('tabel-alumni');
            const statTeridentifikasi = document.getElementById('stat-teridentifikasi');
            const statBelum = document.getElementById('stat-belum');

            // Set Loading State
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> <span>Memproses...</span>';
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-20 text-center text-blue-600">
                        <i class="fas fa-satellite-dish animate-bounce text-4xl mb-4"></i>
                        <p class="font-semibold">Menghubungkan ke API Publik & Mencari Rekam Jejak...</p>
                    </td>
                </tr>
            `;

            try {
                // Melakukan fetch ke track_engine.php
                const response = await fetch('track_engine.php', { method: 'POST' });
                const data = await response.json();

                if (data.success && data.bukti_data_asli.length > 0) {
                    tbody.innerHTML = ''; // Bersihkan loading
                    
                    // Update Statistik (Sederhana)
                    statTeridentifikasi.innerText = "1";
                    statBelum.innerText = "0";

                    // Render baris data dari temuan asli
                    data.bukti_data_asli.forEach((item) => {
                        tbody.innerHTML += `
                            <tr class="hover:bg-blue-50/50 transition-colors border-b border-slate-100 last:border-0">
                                <td class="py-5 px-6 font-semibold text-slate-800">Muhammad Rizky</td>
                                <td class="py-5 px-6 text-slate-600 text-sm">Informatika</td>
                                <td class="py-5 px-6">
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">
                                        <i class="fas fa-check-circle"></i> ${data.status_akhir}
                                    </span>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="text-xs font-bold text-blue-600 uppercase mb-1">${item.sumber}</div>
                                    <div class="text-sm text-slate-700 line-clamp-1">${item.judul}</div>
                                </td>
                                <td class="py-5 px-6">
                                    <a href="${item.link_asli}" target="_blank" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 font-bold text-xs transition-colors group">
                                        <span>LIHAT JURNAL</span>
                                        <i class="fas fa-external-link-alt group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="py-20 text-center text-red-500">Data tidak ditemukan di internet.</td></tr>`;
                }
            } catch (error) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-20 text-center text-red-500">Koneksi API Gagal. Pastikan track_engine.php dapat diakses.</td></tr>`;
            } finally {
                // Reset Button State
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-search"></i> <span>Mulai Pelacakan</span>';
            }
        }
    </script>
</body>
</html>