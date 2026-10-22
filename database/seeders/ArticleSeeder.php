<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Update product prices
        $prices = [89000, 119000, 149000, 169000, 129000, 139000, 189000, 99000, 155000, 175000, 109000, 145000];
        $i = 0;
        foreach (Product::all() as $product) {
            $product->price = $prices[$i % count($prices)];
            $product->save();
            $i++;
        }

        // Seed Articles
        $articles = [
            [
                'title' => 'Panduan Memperbaiki Skin Barrier yang Rusak dalam 14 Hari',
                'slug' => 'panduan-memperbaiki-skin-barrier-rusak',
                'category' => 'Tips & Perawatan',
                'read_time' => '4 menit',
                'author_name' => 'dr. Amanda Sp.KK',
                'author_role' => 'Dermatologist Specialist',
                'excerpt' => 'Kenali tanda skin barrier yang rusak seperti kemerahan, rasa perih saat memakai skincare, dan tekstur kasar. Simak langkah pemulihannya.',
                'content' => '<h2>Apa Itu Skin Barrier?</h2>
<p>Skin barrier (lapisan pelindung kulit) adalah lapisan terluar kulit yang berfungsi menjaga kelembapan alami dan melindungi kulit dari bakteri, polusi, serta iritan lingkungan. Saat skin barrier rusak, kulit akan kehilangan hidrasi dengan cepat dan mudah mengalami peradangan.</p>

<h3>Tanda-tanda Skin Barrier Mengalami Kerusakan:</h3>
<ul>
    <li>Kulit terasa perih, terbakar, atau gatal saat mengaplikasikan produk biasa.</li>
    <li>Kemerahan yang menetap dan kulit tampak bersisik.</li>
    <li>Breakout jerawat tiba-tiba di area yang biasanya bersih.</li>
    <li>Kulit terasa kencang tertarik meski terlihat berminyak (dehidrasi).</li>
</ul>

<h3>Langkah 14 Hari Mengembalikan Kesehatan Skin Barrier:</h3>
<ol>
    <li><strong>Kembali ke Basic Skincare:</strong> Hentikan penggunaan zat aktif eksfoliasi (AHA, BHA, Retinol) untuk sementara. Gunakan hanya pembersih lembut berbusa sedikit, pelembap, dan tabir surya.</li>
    <li><strong>Pilih Kandungan Ceramide & Centella:</strong> Ceramide, Hyaluronic Acid, dan Cica bekerja secara sinergis memperbaiki membran lipid kulit.</li>
    <li><strong>Jangan Mandi Air Terlalu Panas:</strong> Air hangat suam-suam kuku adalah suhu terbaik agar lipid pelindung tidak terkikis.</li>
    <li><strong>Gunakan Sunscreen Physical/Mineral:</strong> Untuk kulit sensitif dan meradang, tabir surya berbasis Zinc Oxide lebih ramah dan minim sensasi perih.</li>
</ol>
<p>Dengan disiplin menerapkan basic skincare yang menenangkan, lapisan pelindung kulit Anda akan pulih dan kembali glowing alami dalam kurun waktu 2 minggu.</p>',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Niacinamide vs Salicylic Acid: Mana yang Tepat untuk Kulit Berjerawat?',
                'slug' => 'niacinamide-vs-salicylic-acid-kulit-berjerawat',
                'category' => 'Kandungan Aktif',
                'read_time' => '5 menit',
                'author_name' => 'Apt. Nadia S.Farm',
                'author_role' => 'Cosmetic Formulator & Pharmacist',
                'excerpt' => 'Kedua bahan ini sangat populer untuk mengatasi jerawat dan minyak berlebih. Ketahui perbedaan cara kerja, keunggulan, serta cara mengombinasikannya.',
                'content' => '<h2>Mengenal Dua Superstar Anti-Jerawat</h2>
<p>Ketika berhadapan dengan masalah pori tersumbat, sebum berlebih, dan jerawat membandel, Niacinamide dan Salicylic Acid (BHA) adalah dua bahan yang paling sering direkomendasikan dokter kulit. Namun, keduanya memiliki mekanisme aksi yang berbeda.</p>

<h3>Salicylic Acid (BHA) - Sang Pembersih Pori</h3>
<p>Salicylic acid bersifat <em>oil-soluble</em> (larut dalam minyak), sehingga mampu menembus ke dalam pori-pori terdalam untuk mencairkan sumbatan sebum, sel kulit mati, dan bakteri penyebab jerawat (C. acnes). Sangat ideal untuk komedo hitam (blackhead), komedo putih, dan jerawat papula.</p>

<h3>Niacinamide (Vitamin B3) - Sang Penenang & Pencerah</h3>
<p>Niacinamide bekerja mengatur produksi minyak dari kelenjar sebasea tanpa membuat kulit kering, sekaligus meredakan inflamasi kemerahan jerawat dan memudarkan noda hitam bekas jerawat (PIH).</p>

<h3>Bolehkah Dipakai Bersamaan?</h3>
<p>Bisa! Anda dapat menggunakan Salicylic Acid di malam hari (2-3 kali seminggu) dan Niacinamide setiap pagi sebelum sunscreen. Kombinasi ini memberikan hasil optimal: pori-pori bersih bebas komedo sekaligus bekas jerawat cepat pudar.</p>',
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Cara Tepat Membaca Hasil AI Skin Check URSKYND',
                'slug' => 'cara-tepat-membaca-hasil-ai-skin-check',
                'category' => 'Edukasi Kulit',
                'read_time' => '3 menit',
                'author_name' => 'dr. Reza Pratama Sp.DV',
                'author_role' => 'Dermatologist Specialist',
                'excerpt' => 'Pahami 6 metrik utama evaluasi kulit AI: hidrasi, pori-pori, jerawat, tekstur, kerutan, dan tingkat sensitivitas untuk memilih skincare yang tepat sasaran.',
                'content' => '<h2>Teknologi AI Facial Diagnostics</h2>
<p>URSKYND memanfaatkan visi komputer dan model pembelajaran mesin dermatologi terlatih untuk memetakan kondisi mikro permukaan kulit wajah Anda dari satu foto selfie berkualitas tinggi.</p>

<h3>6 Parameter Utama yang Dianalisis:</h3>
<ul>
    <li><strong>Skor Hidrasi:</strong> Menunjukkan kemampuan kulit menahan kelembapan intraseluler. Skor di bawah 60% menandakan Anda perlu hidrasi ekstra seperti Hyaluronic Acid toner.</li>
    <li><strong>Deteksi Pori & Sebum:</strong> Mengukur kepadatan pori di zona T (dahi, hidung, dagu).</li>
    <li><strong>Indeks Jerawat & Inflamasi:</strong> Mengidentifikasi jerawat aktif, kemerahan (eritema), dan potensi breakout.</li>
    <li><strong>Tekstur & Kehalusan:</strong> Menilai kerataan sel kulit mati di lapisan stratum korneum.</li>
    <li><strong>Garis Halus & Elastisitas:</strong> Deteksi dini garis tawa, kerutan halus di sekitar mata atau dahi.</li>
    <li><strong>Skin Age Indicator:</strong> Perkiraan usia biologis kulit berbanding usia kronologis Anda.</li>
</ul>

<p>Hasil analisis ini secara otomatis diselaraskan dengan database produk kami untuk merumuskan rutinitas personal yang paling efektif bagi profil kulit spesifik Anda.</p>',
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Pentingnya Reapply Sunscreen: Mitos vs Fakta Tabir Surya',
                'slug' => 'pentingnya-reapply-sunscreen-mitos-vs-fakta',
                'category' => 'Tips & Perawatan',
                'read_time' => '4 menit',
                'author_name' => 'dr. Sarah Wijaya Sp.KK',
                'author_role' => 'Photodermatology Specialist',
                'excerpt' => 'Benarkah sunscreen di dalam ruangan tidak perlu di-reapply? Mengapa sinar UVA tetap menembus kaca jendela dan merusak kolagen kulit Anda.',
                'content' => '<h2>Mengapa Sunscreen Harus Diaplikasikan Ulang?</h2>
<p>Filter tabir surya—baik organik maupun mineral—akan mengalami degradasi fotokimia setelah terpapar radiasi sinar matahari, keringat, gesekan masker, serta minyak alami wajah. Rata-rata efektivitas perlindungan optimal hanya bertahan sekitar 2 hingga 3 jam.</p>

<h3>Mitos Umum Seputar Tabir Surya:</h3>
<ul>
    <li><strong>Mitos:</strong> Di dalam ruangan tidak perlu sunscreen.<br><strong>Fakta:</strong> Kaca jendela biasa hanya menyaring sinar UVB (penyebab sunburn), tetapi sinar UVA (penyebab penuaan dini dan flek melasma) tembus hingga 75%.</li>
    <li><strong>Mitos:</strong> SPF 50 cukup dipakai sekali sehari.<br><strong>Fakta:</strong> Nilai SPF hanya mengukur durasi perlindungan terhadap UVB, bukan ketahanan formula terhadap gesekan dan minyak kulit.</li>
</ul>

<h3>Tips Reapply Praktis Tanpa Merusak Makeup:</h3>
<p>Gunakan sunscreen berbentuk stick (sun stick) atau cushion sunscreen yang ditepuk lembut di atas riasan. Pastikan juga area leher, telinga, dan punggung tangan terlindungi dengan baik.</p>',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Tren Skincare Slow-Aging: Kenapa Lebih Aman daripada Anti-Aging Instan',
                'slug' => 'tren-skincare-slow-aging-lebih-aman',
                'category' => 'Tren & Riset',
                'read_time' => '5 menit',
                'author_name' => 'Tim Medis URSKYND',
                'author_role' => 'Editorial Medical Team',
                'excerpt' => 'Pendekatan slow-aging berfokus pada kesehatan seluler jangka panjang daripada hasil instan yang berisiko merusak integritas kulit.',
                'content' => '<h2>Pergeseran Paradigma: Dari Anti-Aging ke Slow-Aging</h2>
<p>Dunia kecantikan medis kini beralih dari narasi agresif "menghapus kerutan dalam 3 hari" menuju filosofi <em>Slow-Aging</em>. Konsep ini memprioritaskan pemeliharaan kesehatan seluler, hidrasi mendalam, dan perlindungan antioksidan secara berkelanjutan.</p>

<h3>Pilar Utama Slow-Aging:</h3>
<ul>
    <li><strong>Antioksidan Kuat:</strong> Melindungi sel dari stres oksidatif akibat polusi perkotaan dan sinar biru (HEV light).</li>
    <li><strong>Stimulasi Kolagen Alami:</strong> Peptida (Copper Peptides, Matrixyl) dan Bakuchiol sebagai alternatif retinol yang lebih ramah kulit sensitif.</li>
    <li><strong>Kualitas Tidur & Mikrobioma Kulit:</strong> Memperkuat flora bakteri baik kulit dengan prebiotik dan probiotik topikal.</li>
</ul>
<p>Merawat kulit dengan sabar dan bahan berkualitas akan menghasilkan kulit yang kenyal, elastis, dan bersinar awet muda secara natural.</p>',
                'published_at' => now()->subDays(11),
            ],
            [
                'title' => 'Kapan Saat yang Tepat untuk Konsultasi dengan Dokter Kulit?',
                'slug' => 'kapan-saat-tepat-konsultasi-dokter-kulit',
                'category' => 'Edukasi Kulit',
                'read_time' => '4 menit',
                'author_name' => 'dr. Amanda Sp.KK',
                'author_role' => 'Dermatologist Specialist',
                'excerpt' => 'Tidak semua masalah kulit bisa diselesaikan dengan produk over-the-counter (OTC). Ketahui sinyal penting kapan Anda membutuhkan supervisi medis.',
                'content' => '<h2>Skincare Rumahan vs Penanganan Medis</h2>
<p>Banyak orang menghabiskan jutaan rupiah mencoba berbagai serum viral di media sosial, namun masalah jerawat atau flek justru semakin parah. Memahami batasan produk kosmetik sangat krusial.</p>

<h3>Tanda Anda Perlu Konsultasi Dokter:</h3>
<ul>
    <li>Jerawat kistik (nodul/kista) yang besar, terasa nyeri di bawah kulit, dan meninggalkan bopeng (atrophic scars).</li>
    <li>Ruam merah disertai rasa panas terbakar yang menyerupai gejala Rosacea atau Dermatitis Seboroik.</li>
    <li>Flek melasma yang membandel dan tidak memudar setelah 3 bulan memakai bahan pencerah OTC.</li>
    <li>Reaksi alergi kontak mendadak (bengkak, lenting berair, gatal hebat).</li>
</ul>

<p>Melalui layanan <strong>Konsultasi URSKYND</strong>, Anda dapat berdiskusi langsung dengan dokter spesialis kulit untuk mendapatkan evaluasi akurat serta panduan resep medis yang aman dan teruji klinis.</p>',
                'published_at' => now()->subDays(14),
            ],
        ];

        foreach ($articles as $data) {
            Article::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
