
@extends('v_layouts.app')

@section('content')

<div class="container">

    <div class="section-title">
        <p>HISTORY</p>
        <h3 class="title">HISTORY PESANAN</h3>
    </div>

    <table class="shopping-cart-table table">
        <thead>
            <tr>
                <th>ID PESANAN</th>
                <th>TANGGAL</th>
                <th>TOTAL BAYAR</th>
                <th>STATUS</th>
                <th>DETAIL</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $item)
                <tr>
                    <td>{{ $item->id }}</td>

                    <td>
                        {{ $item->created_at->format('d M Y H:i') }}
                    </td>

                    <td>
                        Rp. {{ number_format(
                            $item->total_harga + $item->biaya_ongkir,
                            0, ',', '.'
                        ) }}
                    </td>

                    <td>Proses</td>

                    <td>
                        <button class="btn btn-sm btn-warning"
                                type="button">
                            LIHAT DETAIL
                        </button>

                        <button class="btn btn-sm btn-warning"
                                type="button">
                            INVOICE
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        Belum ada history pesanan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
