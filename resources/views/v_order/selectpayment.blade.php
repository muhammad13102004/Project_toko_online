@extends('v_layouts.app')

@section('content')

<div class="container">
    <h3>Pembayaran Pesanan</h3>

    <table class="table table-bordered">
        <tr>
            <td>Subtotal</td>
            <td>
                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td>Kurir</td>
            <td>
                {{ strtoupper($order->kurir) }}
                {{ $order->layanan_ongkir }}
            </td>
        </tr>

        <tr>
            <td>Ongkos Kirim</td>
            <td>
                Rp {{ number_format($order->biaya_ongkir, 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td><strong>Total Pembayaran</strong></td>
            <td>
                <strong>
                    Rp {{ number_format(
                        $order->total_harga + $order->biaya_ongkir,
                        0, ',', '.'
                    ) }}
                </strong>
            </td>
        </tr>
    </table>

</div>

@endsection