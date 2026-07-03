<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Customer\ProfileCustomerController;
use App\Http\Controllers\Customer\AddressCustomerController;


// Authentication Routes
require __DIR__.'/auth.php';

use App\Models\Product;
use App\Models\Category;
use App\Models\Berita;
use App\Models\Faq;

// PUBLIC ROUTES (Bisa diakses tanpa login)
Route::get('/', function () {
    // Jika login sebagai admin/pemilik/kasir, arahkan ke dashboard
    if (Auth::check() && in_array(Auth::user()->role, ['admin_master', 'pemilik', 'kasir'])) {
        return redirect()->route('dashboard');
    }
    
    $products = Product::with('category')->latest()->get();
    $faqs = Faq::latest()->get();
    $beritas = Berita::latest()->take(3)->get();
    return view('welcome', compact('products', 'faqs', 'beritas'));
})->name('welcome');

Route::middleware('auth')->group(function () {

    Route::get('/profil-saya',
        [ProfileCustomerController::class,'index'])
        ->name('customer.profile');

    Route::put('/profil-saya',
        [ProfileCustomerController::class,'update'])
        ->name('customer.profile.update');

});

Route::middleware('auth')->group(function () {
    Route::get('/alamat-saya', [AddressCustomerController::class, 'index'])->name('customer.address');
    Route::post('/alamat-saya', [AddressCustomerController::class, 'store'])->name('customer.address.store');
    Route::put('/alamat-saya/{address}', [AddressCustomerController::class, 'update'])->name('customer.address.update');
    Route::delete('/alamat-saya/{address}', [AddressCustomerController::class, 'destroy'])->name('customer.address.destroy');
    Route::post('/alamat-saya/{address}/default', [AddressCustomerController::class, 'setAsDefault'])->name('customer.address.default');

    Route::get('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [\App\Http\Controllers\Customer\CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/payment/{order}', [\App\Http\Controllers\Customer\PaymentController::class, 'show'])->name('payment.show');

    // Riwayat Pesanan Customer
    Route::get('/pesanan-saya', [\App\Http\Controllers\Customer\OrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/pesanan-saya/{order}', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/pesanan-saya/{order}/confirm', [\App\Http\Controllers\Customer\OrderController::class, 'confirm'])->name('customer.orders.confirm');
});

Route::get('/produk-makanan', [ProductController::class, 'produkMakanan'])->name('produk.makanan');
Route::get('/tentang-kami', [PegawaiController::class, 'publicIndex'])->name('tentang-kami');
Route::get('/album-kegiatan', [DokumentasiController::class, 'publicAlbum'])->name('album.public');
Route::get('/infografis-dokumentasi', [DokumentasiController::class, 'publicInfografis'])->name('infografis.public');
Route::get('/video-dokumentasi', [DokumentasiController::class, 'publicVideo'])->name('video.public');
Route::get('/faq-pertanyaan', [FaqController::class, 'publicIndex'])->name('faq.public');

Route::get('/berita/public', function () {
        $beritas = Berita::latest()->get();
        return view('view-berita', compact('beritas'));
    })->name('berita.public');

// Route Diagnosa Email (Hapus setelah berhasil)
Route::get('/test-email', function() {
    try {
        \Illuminate\Support\Facades\Mail::raw('Halo, ini adalah pesan tes dari sistem Toko FAA!', function($message) {
            $message->to('vitnori39@gmail.com')->subject('Tes Pengiriman Email Toko FAA');
        });
        return "SUKSES! Email tes telah dikirim ke vitnori39@gmail.com. Silakan cek inbox/spam.";
    } catch (\Exception $e) {
        return "GAGAL! Terjadi kesalahan: " . $e->getMessage();
    }
});

// Tombol Reset Pendaftaran yang Gagal
Route::get('/reset-pendaftaran/{email}', function($email) {
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        $user->delete();
        return "BERHASIL! Data email $email telah dihapus. Silakan coba DAFTAR LAGI sekarang.";
    }
    return "Data email $email tidak ditemukan di database. Anda seharusnya sudah bisa daftar.";
});

// TEMPORARY ADMIN SETUP (Hapus setelah digunakan)
Route::get('/setup-admin/{email}', function($email) {
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        $user->update(['role' => 'admin_master']);
        return "User {$email} sekarang adalah Admin Master! Silakan hapus route ini di web.php untuk keamanan.";
    }
    return "User tidak ditemukan. Pastikan email sudah terdaftar.";
});

// AUTH PROTECTED PUBLIC FEATURES (Untuk User Terdaftar)
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', function () {
        $cart = session()->get('cart', []);
        return view('keranjang', compact('cart'));
    })->name('cart.index');
    
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');

    // 1. Rute untuk menampilkan halaman Chatbot (Akses lewat browser)
    Route::get('/chatbot-ai', function () {
        return view('chatbot'); // Pastikan nama file blade kamu adalah chatbot.blade.php
    })->name('chatbot.ai');

    // 2. Rute Bridge/Proxy untuk menembak server FastAPI Python
    Route::post('/chatbot/proxy', function (Request $request) {
        $pesanUser = trim((string) $request->input('message'));

        if (blank($pesanUser)) {
            return response()->json(['response' => 'Pesan tidak boleh kosong.'], 400);
        }

        try {
            // Timeout dinaikkan jadi 20 detik (cukup untuk inference model ringan),
            // ditambah connect_timeout terpisah agar koneksi gagal cepat terdeteksi
            // tanpa harus menunggu sampai 20 detik penuh.
            $response = Http::withoutVerifying()
                ->connectTimeout(3)
                ->timeout(20)
                ->post('http://127.0.0.1:5000/chat', [
                    'message' => $pesanUser,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'response' => $data['response'] ?? 'Maaf, asisten tidak memberikan jawaban.',
                ]);
            }

            return response()->json([
                'response' => 'Otak AI merespons, namun terjadi kesalahan internal: '.$response->status(),
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Server FastAPI mati / belum jalan / port salah
            return response()->json([
                'response' => 'Asisten AI sedang tidak dapat dihubungi. Silakan coba beberapa saat lagi. 🙏',
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'response' => 'Koneksi ke server AI gagal. Detail Eror: '.substr($e->getMessage(), 0, 150),
            ], 500);
        }
    })->name('chatbot.proxy'); // Nama ini WAJIB sama dengan yang dipanggil di JavaScript fetch

    // 3. (Opsional, tapi direkomendasikan) Rute untuk cek status model AI
    //    Berguna kalau mau menampilkan indikator "AI sedang siap / masih loading" di frontend.
    Route::get('/chatbot/status', function () {
        try {
            $response = Http::withoutVerifying()
                ->connectTimeout(2)
                ->timeout(5)
                ->get('http://127.0.0.1:5000/health');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['model_ready' => false], 503);

        } catch (\Exception $e) {
            return response()->json(['model_ready' => false, 'error' => 'Server tidak terjangkau'], 503);
        }
    })->name('chatbot.status');

    Route::get('/showroom-3d', function () {
        return view('showroom');
    })->name('showroom.3d');
});


// ==========================================
// PROTECTED ROUTES (DASBOR)
// ==========================================

// 1. Dashboard (Akses: Semua Role Backend)
Route::middleware(['auth', 'role:admin_master,pemilik,kasir'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/export/monthly-sales', [DashboardController::class, 'exportMonthlySales'])->name('dashboard.export.monthly');
});

// 2. Transaksi & Operasional (Akses: Admin & Kasir)
Route::middleware(['auth', 'role:admin_master,kasir'])->group(function () {
    // Stock Routes
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', function () { return redirect()->route('stock-entries.index'); })->name('index');
    });
    Route::resource('stock-entries', StockEntryController::class);

    // Sales / POS Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
        Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::post('/pos/store', [SaleController::class, 'storePos'])->name('pos.store');
        Route::post('/{sale}/confirm', [SaleController::class, 'confirm'])->name('confirm');
    });

    // Orders Management Routes (Online Orders)
    Route::prefix('admin/orders')->name('admin.orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
        Route::put('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('update');
    });

    // Products Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });

    // Categories Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
    });
});

// 3. Laporan (Akses: Admin & Pemilik)
Route::middleware(['auth', 'role:admin_master,pemilik'])->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', function() { return redirect()->route('reports.index'); })->name('sales');
        Route::get('/stock', function() { return redirect()->route('reports.index'); })->name('stock');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    Route::resource('berita', BeritaController::class);
    
    Route::get('faq/export/excel', [FaqController::class, 'exportExcel'])->name('faq.export.excel');
    Route::get('faq/export/pdf',   [FaqController::class, 'exportPdf'])->name('faq.export.pdf');
    Route::resource('faq', FaqController::class);

    Route::prefix('dokumentasi')->name('dokumentasi.')->group(function () {
        Route::get('/album', [DokumentasiController::class, 'album'])->name('album');
        Route::post('/album', [DokumentasiController::class, 'storeAlbum'])->name('album.store');
        Route::put('/album/{album}', [DokumentasiController::class, 'updateAlbum'])->name('album.update');
        Route::delete('/album/{album}', [DokumentasiController::class, 'destroyAlbum'])->name('album.destroy');
        Route::get('/infografis', [DokumentasiController::class, 'infografis'])->name('infografis');
        Route::post('/infografis', [DokumentasiController::class, 'storeInfografis'])->name('infografis.store');
        Route::put('/infografis/{infografis}', [DokumentasiController::class, 'updateInfografis'])->name('infografis.update');
        Route::delete('/infografis/{infografis}', [DokumentasiController::class, 'destroyInfografis'])->name('infografis.destroy');
        Route::get('/video', [DokumentasiController::class, 'video'])->name('video');
        Route::post('/video', [DokumentasiController::class, 'storeVideo'])->name('video.store');
        Route::put('/video/{video}', [DokumentasiController::class, 'updateVideo'])->name('video.update');
        Route::delete('/video/{video}', [DokumentasiController::class, 'destroyVideo'])->name('video.destroy');
    });
});

// 4. Manajemen Master Data & Konten (Akses: HANYA Admin Master)
Route::middleware(['auth', 'role:admin_master'])->group(function () {
    Route::prefix('kelola-user')->name('kelola-user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::resource('pegawai', PegawaiController::class);
});

// ==========================================
// PROFIL & SETTINGS (Akses: Semua Role Backend)
// ==========================================
Route::middleware(['auth', 'role:admin_master,pemilik,kasir'])->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/update', [SettingController::class, 'update'])->name('update');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });
