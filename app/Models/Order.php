<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   public $timestamps = true; 
    protected $table = "order"; 
    protected $fillable = [ 
        'customer_id', 
        'total_harga', 
        'status', 
        'noresi', 
        'kurir', 
        'layanan_ongkir', 
        'biaya_ongkir', 
        'estimasi_ongkir', 
        'total_berat', 
        'alamat', 
        'pos' 
    ]; 
 
    public function orderItems() 
    { 
        return $this->hasMany(OrderItem::class); 
    } 
 
  public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id', 'id');
}
    public function selectPayment()
{
    $customer = Customer::where('user_id', Auth::id())->first();

    $order = Order::where('customer_id', $customer->id)
        ->where('status', 'pending')
        ->with('orderItems.produk')
        ->first();

    if (!$order || $order->orderItems->count() == 0) {
        return redirect()->route('order.cart')
            ->with('error', 'Pesanan tidak ditemukan.');
    }

    if (!$order->biaya_ongkir) {
        return redirect()->route('order.selectShipping')
            ->with('error', 'Silakan pilih pengiriman terlebih dahulu.');
    }

    return view('v_order.selectpayment', compact('order'));
}
}
