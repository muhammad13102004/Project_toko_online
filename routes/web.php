<?php 
 
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\BerandaController; 
use App\Http\Controllers\LoginController; 
use App\Http\Controllers\UserController; 
use App\Http\Controllers\KategoriController; 
use App\Http\Controllers\ProdukController; 
use App\Http\Controllers\CustomerController; 
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\RajaOngkirController;

 
Route::get('/', function () { 
    // return view('welcome'); 
    return redirect()->route('beranda'); 
}); 
 
Route::get('backend/beranda', [BerandaController::class, 'berandaBackend'])
->name('backend.beranda')->middleware('auth'); 
 
Route::get('backend/login', [LoginController::class, 'loginBackend'])
->name('backend.login'); 
Route::post('backend/login', [LoginController::class, 'authenticateBackend'])
->name('backend.login'); 
Route::post('backend/logout', [LoginController::class, 'logoutBackend'])
->name('backend.logout'); 
 
// Route untuk User 
// Route::resource('backend/user', UserController::class)->middleware('auth'); 
Route::resource('backend/user', UserController::class, ['as' => 'backend'])
->middleware('auth'); 
// Route untuk laporan user 
Route::get('backend/laporan/formuser', [UserController::class, 'formUser'])
->name('backend.laporan.formuser')->middleware('auth'); 
Route::post('backend/laporan/cetakuser', [UserController::class, 'cetakUser'])
->name('backend.laporan.cetakuser')->middleware('auth'); 
 
// Route untuk Kategori 
Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend'])
->middleware('auth'); 
 
// Route untuk Produk 
Route::resource('backend/produk', ProdukController::class, ['as' => 'backend'])
->middleware('auth'); 
// Route untuk menambahkan foto 
Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])
->name('backend.foto_produk.store')->middleware('auth'); 
// Route untuk menghapus foto 
Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])
->name('backend.foto_produk.destroy')->middleware('auth'); 
// Route untuk laporan produk 
Route::get('backend/laporan/formproduk', [ProdukController::class, 'formProduk'])
->name('backend.laporan.formproduk')->middleware('auth'); 
Route::post('backend/laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])
->name('backend.laporan.cetakproduk')->middleware('auth'); 
 Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])
->name('produk.detail'); 
Route::get('/produk/kategori/{id}', [ProdukController::class, 
'produkKategori'])->name('produk.kategori');
Route::get('/produk', [ProdukController::class, 'index'])
->name('produk.all');

// Frontend 
Route::get('/beranda', [BerandaController::class, 'index'])
->name('beranda'); 
//API Google 
Route::get('/auth/redirect', [CustomerController::class, 'redirect'])
->name('auth.redirect'); 
Route::get('/auth/google/callback', [CustomerController::class, 'callback'])
->name('auth.callback'); 
// Logout 
Route::post('/logout', [CustomerController::class, 'logout'])
->name('logout'); 
Route::post('/customer/logout', [CustomerController::class, 'logout'])
->name('customer.logout');
Route::resource('backend/customer', CustomerController::class, ['as' => 'backend'])
->middleware('auth'); 


Route::prefix('backend')->name('backend.')->group(function () {
    Route::get('/customer', [CustomerController::class, 'index'])
        ->name('customer.index');

    Route::get('/customer/{id}', [CustomerController::class, 'show'])
        ->name('customer.show');

    Route::get('/customer/{id}/edit', [CustomerController::class, 'edit'])
        ->name('customer.edit');

    Route::put('/customer/{id}', [CustomerController::class, 'update'])
        ->name('customer.update');

    Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])
        ->name('customer.destroy');
});

// Route untuk akun dan keranjang customer
Route::middleware('is.customer')->group(function () {
    // Menampilkan halaman akun
    Route::get('/customer/akun/{id}', [
        CustomerController::class, 'akun'
    ])->name('customer.akun');

    // Mengupdate data akun
    Route::put('/customer/updateakun/{id}', [
        CustomerController::class, 'updateAkun'
    ])->name('customer.updateakun');

    // Menambahkan produk ke keranjang
    Route::post('/add-to-cart/{id}', [
        OrderController::class, 'addToCart'
    ])->name('order.addToCart');

    // Menampilkan keranjang
    Route::get('/cart', [
        OrderController::class, 'viewCart'
    ])->name('order.cart');
    // Ongkir
   Route::get('/list-ongkir', function () {
    $response = Http::withHeaders([
        'key' => config('services.rajaongkir.key')
    ])->get(
        config('services.rajaongkir.base_url') . '/destination/province'
    );

    $provinsi = $response->json('data');

    foreach ($provinsi as $row) {
        echo $row['id'] . ' - ' . $row['name'] . '<br>';
    }
});
Route::get('/get-provinces', [RajaOngkirController::class, 'getProvinces']);

Route::get('/get-cities/{provinceId}', [RajaOngkirController::class, 'getCities']);

Route::get('/ongkir', function () {
    return view('ongkir');
});
Route::get('/cek-ongkir', function () {
    return view('ongkir');
});

Route::post('/cek-ongkir', [RajaOngkirController::class, 'checkOngkir']);
// Group route untuk customer 
Route::middleware('is.customer')->group(function () { 
    // Route untuk menampilkan halaman akun customer 
    Route::get('/customer/akun/{id}', [CustomerController::class, 'akun']) 
        ->name('customer.akun'); 
 
    // Route untuk mengupdate data akun customer 
    Route::put('/customer/updateakun/{id}', [CustomerController::class, 'updateAkun']) 
        ->name('customer.updateakun'); 
 
    // Route keranjang belanja 
    Route::post('add-to-cart/{id}', [OrderController::class, 'addToCart'])
->name('order.addToCart'); 
    Route::get('cart', [OrderController::class, 'viewCart'])->name('order.cart'); 
    Route::post('cart/update/{id}', [OrderController::class, 'updateCart'])
->name('order.updateCart'); 
    Route::post('remove/{id}', [OrderController::class, 'removeFromCart'])
->name('order.remove'); 
 
    // Ongkir 
    Route::post('select-shipping', [OrderController::class, 'selectShipping'])
->name('order.selectShipping'); 
    Route::get('provinces', [OrderController::class, 'getProvinces']); 
    Route::get('cities', [OrderController::class, 'getCities']); 
    Route::post('cost', [OrderController::class, 'getCost']); 
    Route::post('updateongkir', [OrderController::class, 'updateongkir'])
->name('order.updateongkir'); 
}); 
Route::get('selectpayment', [OrderController::class, 'selectPayment'])
    ->name('order.selectpayment');

Route::get('/history', [OrderController::class, 'history'])
    ->name('order.history');

Route::post('/bayar', [OrderController::class, 'payOrder'])
    ->name('order.pay');

Route::get('/backend/pesanan', [OrderController::class, 'statusProses'])
    ->name('pesanan.proses');

Route::get('/backend/pesanan/detail/{id}',
    [OrderController::class, 'detail']
)->name('pesanan.detail');

Route::get('/backend/pesanan/invoice/{id}', [OrderController::class, 'invoice'])
    ->name('pesanan.invoice');

    Route::get('/backend/pesanan/detail/{id}', [OrderController::class, 'detail'])
    ->name('pesanan.detail');

Route::post('/backend/pesanan/update/{id}', [OrderController::class, 'update'])
    ->name('pesanan.update');
});