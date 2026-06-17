<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Multi-Aktor Demo
        $admin = User::create([
            'name' => 'Admin Mayapada',
            'email' => 'admin@mayapada.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $supplierKU = User::create([
            'name' => 'Supplier Kencana Ungu',
            'email' => 'kencana@supplier.com',
            'password' => Hash::make('supplier'),
            'role' => 'supplier',
            'brand_name' => 'Kencana Ungu',
            'contact' => '+62 812-3400-5600',
        ]);

        $supplierGP = User::create([
            'name' => 'Supplier Gajah Putih',
            'email' => 'gajah@supplier.com',
            'password' => Hash::make('supplier'),
            'role' => 'supplier',
            'brand_name' => 'Gajah Putih',
            'contact' => '+62 821-4500-6700',
        ]);

        $customerBudi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('buyer'),
            'role' => 'customer',
            'contact' => '+62 899-7000-8000',
        ]);

        // 2. Buat Kategori
        $catDaster = Category::create(['name' => 'Daster', 'slug' => 'daster', 'description' => 'Koleksi Daster Kencana Ungu & Gajah Putih']);
        $catBatik = Category::create(['name' => 'Batik', 'slug' => 'batik', 'description' => 'Pakaian Batik Motif Pontianak & Kalimantan']);

        // 3. Buat Produk Sampel (SESUAIKAN DENGAN KOLOM BARU)
        $prod1 = Product::create([
            'supplier_id' => $supplierKU->id,
            'supplier_brand' => 'Kencana Ungu',
            'sku' => 'KU-DAS-01',
            'name' => 'Baju Daster Kencana Ungu Tulip',
            'slug' => 'baju-daster-kencana-ungu-tulip',
            'category_id' => $catDaster->id,
            'description' => 'Daster premium Kencana Ungu bermotif bunga Tulip. Bahan rayon super adem, awet, dan nyaman dipakai sehari-hari.',
            'wholesale_price' => 1000000, // Tambahan: Harga modal lusinan
            'wholesale_unit' => 'lusin',  // Tambahan: Satuan
            'price' => 120000,            // Harga Eceran Admin
            'stock' => 15,
            'image_path' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=500&q=80',
            'gallery_images' => [         // Tambahan: Array Galeri Interaktif
                'https://images.unsplash.com/photo-1544441893-675973e31985?w=500&q=80',
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=500&q=80'
            ],
            'is_published' => true,       // Tambahan: Agar langsung tayang di halaman konsumen
        ]);

        // Tambah Varian Produk 1 (Stok Granular)
        ProductVariant::create(['product_id' => $prod1->id, 'name' => 'S - MERAH', 'sku' => 'KU-DAS-01-S-ME', 'price' => 120000, 'stock' => 8]);
        ProductVariant::create(['product_id' => $prod1->id, 'name' => 'M - BIRU', 'sku' => 'KU-DAS-01-M-BI', 'price' => 120000, 'stock' => 7]);

        $prod2 = Product::create([
            'supplier_id' => $supplierGP->id,
            'supplier_brand' => 'Gajah Putih',
            'sku' => 'GP-DAS-02',
            'name' => 'Baju Daster Gajah Putih Klasik',
            'slug' => 'baju-daster-gajah-putih-klasik',
            'category_id' => $catDaster->id,
            'description' => 'Daster harian merk Gajah Putih Klasik. Bahan katun dingin bermotif batik parang modern.',
            'wholesale_price' => 950000,  // Tambahan
            'wholesale_unit' => 'lusin',  // Tambahan
            'price' => 110000,            // Harga Eceran Admin
            'stock' => 20,
            'image_path' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&q=80',
            'gallery_images' => [],       // Tambahan: Galeri kosong
            'is_published' => true,       // Tambahan: Langsung rilis
        ]);

        ProductVariant::create(['product_id' => $prod2->id, 'name' => 'ALL SIZE - HIJAU', 'sku' => 'GP-DAS-02-AS-HI', 'price' => 110000, 'stock' => 20]);
    }
}
