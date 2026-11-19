<?php 
 
namespace App\Http\Controllers; 
 
use Illuminate\Http\Request; 
use App\Models\Customer; 
use App\Models\User; 
use Illuminate\Support\Facades\Auth; 
use Laravel\Socialite\Facades\Socialite; 
use Illuminate\Support\Facades\Hash; 
use App\Helpers\ImageHelper; 
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // Redirect ke halaman login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback setelah login Google berhasil
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();

            // Cari user berdasarkan email Google
            $user = User::where('email', $socialUser->email)
                ->first();

            // Jika user belum terdaftar, buat user baru
            if (!$user) {
                $user = User::create([
                    'nama' => $socialUser->name,
                    'email' => $socialUser->email,
                    'role' => '2',
                    'status' => 1,
                    'password' => Hash::make(
                        Str::random(32)
                    ),
                ]);
            }

            // Pastikan data customer tersedia
            Customer::firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'google_id' => $socialUser->id,
                    'google_token' => $socialUser->token,
                ]
            );

            // Login user
            Auth::login($user);

            // Regenerasi session setelah login
            request()->session()->regenerate();

            // Arahkan ke halaman beranda
            return redirect()->intended('beranda');

        } catch (\Exception $e) {
            // Catat detail error untuk diperiksa
            report($e);

            return redirect('/')
                ->with(
                    'error',
                    'Terjadi kesalahan saat login dengan Google.'
                );
        }
    }

    // Logout pengguna
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda telah berhasil logout.');
    }

    // Menampilkan daftar customer
    public function index()
    {
        $customer = Customer::with('user')
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.v_customer.index', [
            'judul' => 'Customer',
            'sub' => 'Halaman Customer',
            'index' => $customer,
        ]);
    }
    
    // Menampilkan detail customer
    public function show($id)
    {
        $customer = Customer::with('user')->findOrFail($id);

        return view('backend.v_customer.show', [
            'judul' => 'Detail Customer',
            'sub' => 'Detail Customer',
            'customer' => $customer,
        ]);
    }

    // Menampilkan form edit customer
    public function edit($id)
    {
        $customer = Customer::with('user')->findOrFail($id);

        return view('backend.v_customer.edit', [
            'judul' => 'Ubah Customer',
            'sub' => 'Ubah Customer',
            'customer' => $customer,
        ]);
    }

    // Menyimpan perubahan customer
    public function update(Request $request, $id)
    {
        $customer = Customer::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:user,email,' .
                ($customer->user->id ?? 0),
            'alamat' => 'nullable|string|max:255',
            'pos' => 'nullable|string|max:20',
        ]);

        if (!$customer->user) {
            return back()->withErrors([
                'customer' => 'Data user untuk customer ini tidak ditemukan.',
            ]);
        }

        $customer->user->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
        ]);

        $customer->update([
            'alamat' => $validated['alamat'] ?? null,
            'pos' => $validated['pos'] ?? null,
        ]);

        return redirect()
            ->route('backend.customer.index')
            ->with('success', 'Data customer berhasil diubah.');
    }

    // Menghapus customer
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->delete();

        return redirect()
            ->route('backend.customer.index')
            ->with('success', 'Data customer berhasil dihapus.');
    }
    public function akun($id) 
{ 
    $loggedInCustomerId = Auth::user()->id; 
    // Cek apakah ID yang diberikan sama dengan ID customer yang sedang login 
    if ($id != $loggedInCustomerId) { 
        // Redirect atau tampilkan pesan error 
        return redirect()->route('backend.customer.akun', [
    'id' => $loggedInCustomerId
])->with('msgError', 'Anda tidak berhak mengakses akun ini.');
    } 
    $customer = Customer::where('user_id', $id)->firstOrFail(); 
    return view('v_customer.edit', [ 
        'judul' => 'Customer', 
        'subJudul' => 'Akun Customer', 
        'edit' => $customer 
    ]); 
} 
 
public function updateAkun(Request $request, $id) 
{ 
    $customer = Customer::where('user_id', $id)->firstOrFail(); 
    $rules = [ 
        'nama' => 'required|max:255', 
        'hp' => 'required|min:10|max:13', 
        'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024', 
    ]; 
    $messages = [ 
        'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau 
gif.', 
        'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.' 
    ]; 
 
    if ($request->email != $customer->user->email) { 
        $rules['email'] = 'required|max:255|email|unique:customer'; 
    } 
    if ($request->alamat != $customer->alamat) { 
        $rules['alamat'] = 'required'; 
    } 
    if ($request->pos != $customer->pos) { 
        $rules['pos'] = 'required'; 
    } 
 
    $validatedData = $request->validate($rules, $messages); 
    // menggunakan ImageHelper 
    if ($request->file('foto')) { 
        //hapus gambar lama 
        if ($customer->user->foto) { 
            $oldImagePath = public_path('storage/img-customer/') . $customer->user->foto; 
             if (file_exists($oldImagePath)) { 
                unlink($oldImagePath); 
            } 
        } 
        $file = $request->file('foto'); 
        $extension = $file->getClientOriginalExtension(); 
        $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension; 
        $directory = 'storage/img-customer/'; 
        // Simpan gambar dengan ukuran yang ditentukan 
        ImageHelper::uploadAndResize($file, $directory, $originalFileName, 385, 400); // null (jika tinggi otomatis) 
        // Simpan nama file asli di database 
        $validatedData['foto'] = $originalFileName; 
    } 
 
    $customer->user->update($validatedData); 
 
    $customer->update([ 
        'alamat' => $request->input('alamat'), 
        'pos' => $request->input('pos'), 
    ]); 
    return redirect()->route('backend.customer.akun', $id)->with('success', 'Data berhasil 
diperbarui'); 
} 


}
