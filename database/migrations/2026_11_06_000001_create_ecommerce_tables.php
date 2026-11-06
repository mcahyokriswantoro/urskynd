<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'weight')) {
                $table->unsignedInteger('weight')->default(200)->after('size'); // gram
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(100)->after('weight');
            }
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('courier', 60)->default('J&T Express');
            $table->string('service', 60)->default('EZ (Reguler)');
            $table->text('match_cities')->nullable();     // dipisah koma
            $table->text('match_provinces')->nullable();  // dipisah koma
            $table->unsignedInteger('cost_first_kg')->default(10000);
            $table->unsignedInteger('cost_next_kg')->default(10000);
            $table->string('etd', 40)->default('2-3 hari');
            $table->unsignedBigInteger('free_shipping_min')->nullable(); // gratis ongkir jika subtotal >= nilai ini
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // pending_payment, paid, processing, shipped, delivered, completed, cancelled
            $table->string('status', 30)->default('pending_payment');
            // unpaid, paid, expired, failed, refunded
            $table->string('payment_status', 20)->default('unpaid');
            $table->string('payment_gateway', 30)->default('midtrans');
            $table->string('payment_method', 60)->nullable();
            $table->string('payment_reference', 120)->nullable();
            $table->string('snap_token', 120)->nullable();
            $table->string('snap_redirect_url', 255)->nullable();
            $table->string('payment_proof', 255)->nullable();
            $table->timestamp('payment_expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->string('recipient_name', 120);
            $table->string('recipient_phone', 30);
            $table->string('province', 80);
            $table->string('city', 80);
            $table->string('district', 80)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('address');

            $table->foreignId('shipping_zone_id')->nullable()->constrained('shipping_zones')->nullOnDelete();
            $table->string('shipping_zone_name', 120)->nullable();
            $table->string('courier', 60)->default('J&T Express');
            $table->string('courier_service', 60)->nullable();
            $table->string('shipping_etd', 40)->nullable();
            $table->unsignedInteger('total_weight')->default(0); // gram
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('total')->default(0);

            $table->string('tracking_number', 60)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 255)->nullable();

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('affiliate_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('weight')->default(0); // gram per pcs
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->string('title', 150);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('affiliate_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliate_sales', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('product_id')->constrained('orders')->nullOnDelete();
            }
        });

        // Tarif zona default J&T Express dari gudang Lamongan (bisa diubah Admin)
        $now = now();
        $lamonganCities = 'Lamongan, Babat, Paciran, Brondong, Sukodadi, Deket, Tikung, Turi, Kalitengah, Karanggeneng, Sekaran, Maduran, Laren, Solokuro, Karangbinangun, Glagah, Sarirejo, Kembangbahu, Sugio, Kedungpring, Modo, Ngimbang, Bluluk, Sukorame, Sambeng, Mantup, Pucuk';
        $jatimCities = 'Malang, Batu, Kediri, Blitar, Tulungagung, Trenggalek, Nganjuk, Madiun, Magetan, Ngawi, Ponorogo, Pacitan, Pasuruan, Probolinggo, Lumajang, Jember, Bondowoso, Situbondo, Banyuwangi, Sampang, Pamekasan, Sumenep, Madura';
        $javaBaliCities = 'Jakarta, Bogor, Depok, Tangerang, Bekasi, Bandung, Cimahi, Cirebon, Tasikmalaya, Sukabumi, Karawang, Garut, Semarang, Solo, Surakarta, Magelang, Pekalongan, Tegal, Salatiga, Purwokerto, Banyumas, Cilacap, Kudus, Jepara, Pati, Klaten, Yogyakarta, Jogja, Sleman, Bantul, Serang, Cilegon, Denpasar, Badung, Gianyar, Tabanan, Singaraja';
        $javaBali = 'DKI Jakarta, Jawa Barat, Jawa Tengah, DI Yogyakarta, Banten, Bali';
        $outerCities = 'Medan, Banda Aceh, Padang, Bukittinggi, Pekanbaru, Dumai, Batam, Tanjungpinang, Jambi, Palembang, Pangkalpinang, Bengkulu, Bandar Lampung, Metro, Pontianak, Singkawang, Palangkaraya, Banjarmasin, Banjarbaru, Balikpapan, Samarinda, Bontang, Tarakan, Manado, Bitung, Gorontalo, Palu, Mamuju, Makassar, Parepare, Palopo, Kendari, Baubau, Mataram, Bima, Lombok, Kupang';
        $outerIslands = 'Aceh, Sumatera Utara, Sumatera Barat, Riau, Kepulauan Riau, Jambi, Sumatera Selatan, Bangka Belitung, Bengkulu, Lampung, Kalimantan Barat, Kalimantan Tengah, Kalimantan Selatan, Kalimantan Timur, Kalimantan Utara, Sulawesi Utara, Gorontalo, Sulawesi Tengah, Sulawesi Barat, Sulawesi Selatan, Sulawesi Tenggara, Nusa Tenggara Barat, Nusa Tenggara Timur';
        $eastCities = 'Ambon, Tual, Ternate, Tidore, Jayapura, Merauke, Timika, Mimika, Biak, Nabire, Sorong, Manokwari, Wamena';

        DB::table('shipping_zones')->insert([
            ['name' => 'Sesama Lamongan', 'match_cities' => $lamonganCities, 'match_provinces' => null, 'cost_first_kg' => 8000, 'cost_next_kg' => 5000, 'etd' => '1 hari', 'free_shipping_min' => 150000, 'sort_order' => 1, 'is_default' => false],
            ['name' => 'Sekitar Lamongan (Gerbangkertosusila & Tetangga)', 'match_cities' => 'Gresik, Surabaya, Tuban, Bojonegoro, Jombang, Mojokerto, Sidoarjo, Bangkalan', 'match_provinces' => null, 'cost_first_kg' => 10000, 'cost_next_kg' => 7000, 'etd' => '1-2 hari', 'free_shipping_min' => null, 'sort_order' => 2, 'is_default' => false],
            ['name' => 'Jawa Timur Lainnya', 'match_cities' => $jatimCities, 'match_provinces' => 'Jawa Timur', 'cost_first_kg' => 12000, 'cost_next_kg' => 9000, 'etd' => '2-3 hari', 'free_shipping_min' => null, 'sort_order' => 3, 'is_default' => false],
            ['name' => 'Pulau Jawa & Bali', 'match_cities' => $javaBaliCities, 'match_provinces' => $javaBali, 'cost_first_kg' => 18000, 'cost_next_kg' => 14000, 'etd' => '2-4 hari', 'free_shipping_min' => null, 'sort_order' => 4, 'is_default' => false],
            ['name' => 'Sumatera, Kalimantan, Sulawesi & Nusa Tenggara', 'match_cities' => $outerCities, 'match_provinces' => $outerIslands, 'cost_first_kg' => 35000, 'cost_next_kg' => 28000, 'etd' => '3-6 hari', 'free_shipping_min' => null, 'sort_order' => 5, 'is_default' => false],
            ['name' => 'Maluku & Papua', 'match_cities' => $eastCities, 'match_provinces' => 'Maluku, Maluku Utara, Papua, Papua Barat, Papua Barat Daya, Papua Tengah, Papua Pegunungan, Papua Selatan', 'cost_first_kg' => 60000, 'cost_next_kg' => 50000, 'etd' => '5-9 hari', 'free_shipping_min' => null, 'sort_order' => 6, 'is_default' => true],
        ]);

        DB::table('shipping_zones')->update([
            'courier' => 'J&T Express',
            'service' => 'EZ (Reguler)',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::table('affiliate_sales', function (Blueprint $table) {
            if (Schema::hasColumn('affiliate_sales', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
        });

        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('cart_items');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'stock']);
        });
    }
};

