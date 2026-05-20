<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterviewSimulatorController extends Controller
{
    /**
     * Bank pertanyaan interview dikelompokkan per kategori karir.
     */
    private $questions = [
        'general' => [
            'label' => 'Umum / HRD',
            'icon'  => 'fas fa-user-tie',
            'color' => 'slate',
            'questions' => [
                ['q' => 'Ceritakan tentang diri Anda.', 'tip' => 'Tetap profesional – fokus pada latar belakang, keahlian utama, dan alasan Anda tertarik dengan posisi ini. Gunakan struktur Masa Kini–Masa Lalu–Masa Depan.'],
                ['q' => 'Apa kelebihan terbesar Anda?', 'tip' => 'Pilih 2–3 kelebihan yang relevan dengan pekerjaan. Dukung setiap kelebihan dengan contoh nyata.'],
                ['q' => 'Apa kelemahan terbesar Anda?', 'tip' => 'Pilih kelemahan yang jujur, tapi sampaikan sebagai hal yang sedang Anda perbaiki. Hindari jawaban klise seperti "Saya terlalu pekerja keras."'],
                ['q' => 'Di mana Anda melihat diri Anda dalam 5 tahun ke depan?', 'tip' => 'Tunjukkan ambisi yang sejalan dengan pertumbuhan perusahaan. Sebutkan keterampilan yang ingin Anda kembangkan.'],
                ['q' => 'Mengapa Anda ingin keluar dari pekerjaan saat ini?', 'tip' => 'Tetap positif. Fokus pada peluang pertumbuhan di perusahaan baru daripada mengkritik perusahaan lama.'],
                ['q' => 'Mengapa kami harus merekrut Anda?', 'tip' => 'Rangkum 3 keahlian utama Anda yang relevan, berikan contoh singkat yang berorientasi hasil, lalu tutup dengan antusiasme Anda terhadap posisi ini.'],
            ],
        ],
        'data-scientist' => [
            'label' => 'Data Scientist',
            'icon'  => 'fas fa-chart-pie',
            'color' => 'blue',
            'questions' => [
                ['q' => 'Jelaskan perbedaan antara supervised dan unsupervised learning.', 'tip' => 'Supervised learning menggunakan data berlabel; unsupervised menemukan pola dalam data tanpa label. Berikan contoh nyata masing-masing (misal: deteksi spam vs. segmentasi pelanggan).'],
                ['q' => 'Bagaimana Anda menangani data yang hilang (missing data) dalam dataset?', 'tip' => 'Sebutkan beberapa strategi: penghapusan (listwise/pairwise), imputasi mean/median, imputasi berbasis model (KNN, regresi), dan kapan masing-masing tepat digunakan.'],
                ['q' => 'Apa itu overfitting dan bagaimana cara mencegahnya?', 'tip' => 'Overfitting = model menghafal data latih. Pencegahan: cross-validation, regularisasi (L1/L2), dropout, mengurangi kompleksitas model, menambah data.'],
                ['q' => 'Jelaskan tradeoff bias-variance.', 'tip' => 'Bias tinggi = underfitting; variance tinggi = overfitting. Model yang baik menyeimbangkan keduanya. Gunakan analogi diagram sederhana untuk menjelaskan.'],
                ['q' => 'Ceritakan proyek data science yang pernah Anda selesaikan.', 'tip' => 'Gunakan format STAR: Situasi, Tugas, Aksi, Hasil. Sebutkan alat yang digunakan, metrik yang dioptimasi, dan dampak bisnisnya.'],
            ],
        ],
        'software-engineer' => [
            'label' => 'Software Engineer',
            'icon'  => 'fas fa-laptop-code',
            'color' => 'indigo',
            'questions' => [
                ['q' => 'Jelaskan perbedaan antara REST dan GraphQL.', 'tip' => 'REST menggunakan endpoint tetap per resource; GraphQL menggunakan satu endpoint di mana klien menentukan data yang dibutuhkan. Sebutkan trade-off (over-fetching, kompleksitas).'],
                ['q' => 'Apa itu prinsip SOLID?', 'tip' => 'S–Single Responsibility, O–Open/Closed, L–Liskov Substitution, I–Interface Segregation, D–Dependency Inversion. Berikan contoh singkat minimal untuk dua prinsip.'],
                ['q' => 'Bagaimana Anda memastikan kualitas kode dalam tim?', 'tip' => 'Sebutkan code review, linting, automated testing (unit, integration, e2e), CI/CD pipeline, dan standar dokumentasi.'],
                ['q' => 'Jelaskan cara kerja database index.', 'tip' => 'Index adalah struktur data (biasanya B-tree) yang mempercepat pembacaan dengan mengorbankan kecepatan penulisan dan penyimpanan tambahan. Sebutkan kapan TIDAK perlu menggunakan index.'],
                ['q' => 'Ceritakan bug yang sulit yang pernah Anda perbaiki.', 'tip' => 'Gunakan STAR. Soroti proses debugging: reproduksi, isolasi, analisis akar masalah, perbaikan, dan uji regresi.'],
            ],
        ],
        'ui-ux-designer' => [
            'label' => 'Desainer UI/UX',
            'icon'  => 'fas fa-pen-nib',
            'color' => 'purple',
            'questions' => [
                ['q' => 'Ceritakan proses desain Anda dari awal sampai akhir.', 'tip' => 'Jelaskan proses end-to-end: Riset → Definisi → Ideasi → Prototipe → Uji → Iterasi. Sebutkan alat yang digunakan (Figma, Maze, dll.).'],
                ['q' => 'Bagaimana Anda menangani feedback desain yang tidak Anda setujui?', 'tip' => 'Tunjukkan empati, ajukan pertanyaan klarifikasi, presentasikan data untuk mendukung keputusan Anda, dan tunjukkan kemauan berkompromi.'],
                ['q' => 'Apa perbedaan antara UX dan UI?', 'tip' => 'UX = pengalaman dan kesan keseluruhan; UI = elemen visual dan interaktif. UI yang bagus tetap bisa punya UX yang buruk.'],
                ['q' => 'Bagaimana Anda membuat desain yang aksesibel?', 'tip' => 'Sebutkan panduan WCAG, rasio kontras warna (4.5:1 untuk teks normal), navigasi keyboard, dukungan screen reader, dan alt text untuk gambar.'],
                ['q' => 'Ceritakan saat desain Anda secara signifikan meningkatkan sebuah metrik.', 'tip' => 'Berikan metrik before/after yang konkret (misal: conversion rate, waktu penyelesaian tugas). Jelaskan riset, keputusan desain, dan hasil A/B test.'],
            ],
        ],
        'product-manager' => [
            'label' => 'Product Manager',
            'icon'  => 'fas fa-tasks',
            'color' => 'emerald',
            'questions' => [
                ['q' => 'Bagaimana Anda memprioritaskan fitur ketika semuanya terasa mendesak?', 'tip' => 'Jelaskan framework: RICE (Reach, Impact, Confidence, Effort), MoSCoW, atau model Kano. Tunjukkan cara Anda menyeimbangkan nilai bisnis vs. usaha.'],
                ['q' => 'Bagaimana Anda mendefinisikan kesuksesan sebuah produk?', 'tip' => 'Hubungkan ke tujuan bisnis → OKR → metrik utama (DAU, retensi, NPS, revenue). Tekankan leading vs. lagging indicators.'],
                ['q' => 'Bagaimana Anda bekerja dengan tim engineering saat ada ketidaksepakatan?', 'tip' => 'Tunjukkan pemahaman Anda tentang batasan teknis. Jelaskan bagaimana Anda menyelaraskan "mengapa" sebelum "apa", gunakan data untuk menyelesaikan perbedaan, dan menjaga kepercayaan.'],
                ['q' => 'Bagaimana Anda mengumpulkan dan menggunakan feedback pelanggan?', 'tip' => 'Sebutkan kualitatif (wawancara, uji kegunaan) dan kuantitatif (survei, analytics). Jelaskan cara Anda mensintesis insight menjadi kebutuhan yang bisa ditindaklanjuti.'],
                ['q' => 'Ceritakan tentang kegagalan produk dan apa yang Anda pelajari.', 'tip' => 'Jujur. Fokus pada indikator yang terlewat, apa yang akan Anda lakukan secara berbeda, dan pola pikir berkembang yang Anda kembangkan.'],
            ],
        ],
    ];

    /**
     * Tampilkan halaman utama simulator interview.
     */
    public function index()
    {
        $categories = $this->questions;
        // Strip questions array so we only pass meta for the index
        $categoryMeta = [];
        foreach ($categories as $key => $cat) {
            $categoryMeta[$key] = [
                'label' => $cat['label'],
                'icon'  => $cat['icon'],
                'color' => $cat['color'],
                'count' => count($cat['questions']),
            ];
        }
        return view('interview.index', compact('categoryMeta'));
    }

    /**
     * Tampilkan pertanyaan untuk kategori karir tertentu.
     */
    public function show($category)
    {
        if (!array_key_exists($category, $this->questions)) {
            abort(404);
        }
        $cat = $this->questions[$category];
        return view('interview.show', compact('cat', 'category'));
    }
}
