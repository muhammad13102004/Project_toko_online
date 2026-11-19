<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Pengiriman</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #344054;
        }

        .container {
            border: 1px solid #333;
            padding: 10px;
            max-width: 680px;
            margin: 20px auto;
        }

        h2 {
            font-size: 20px;
            margin: 0 0 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
            color: #292d32;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 12px 0 6px;
        }

        select, input, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            color: #344054;
        }

        button {
            background: #30343b;
            color: white;
            border: none;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            text-align: left;
        }

        th, td {
            padding: 14px 5px;
            border-bottom: 1px solid #ddd;
        }

        th {
            font-size: 11px;
        }

        .pilih {
            background: tomato;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>PILIH PENGIRIMAN</h2>

    <label>Provinsi Tujuan</label>
    <select id="province">
        <option value="">Pilih Provinsi</option>
    </select>

    <label>Kota Tujuan</label>
    <select id="city" disabled>
        <option value="">Pilih Kota</option>
    </select>

    <label>Kurir</label>
    <select id="courier">
        <option value="">Pilih Kurir</option>
        <option value="jne">JNE</option>
        <option value="tiki">TIKI</option>
        <option value="pos">POS Indonesia</option>
    </select>

    <label>Alamat</label>
    <textarea id="alamat" rows="2"
        placeholder="Masukkan alamat tujuan"></textarea>

    <label>Kode Pos</label>
    <input type="text" id="kodepos"
        placeholder="Masukkan kode pos">

    <br><br>

    <button type="button" onclick="checkOngkir()">
        CEK ONGKIR
    </button>

    <p id="pesan"></p>

    <table>
        <thead>
            <tr>
                <th>LAYANAN</th>
                <th>BIAYA</th>
                <th>ESTIMASI PENGIRIMAN</th>
                <th>TOTAL BERAT</th>
                <th>TOTAL HARGA</th>
                <th>BAYAR</th>
            </tr>
        </thead>

        <tbody id="result">
            <tr>
                <td colspan="6">Silakan cek ongkir terlebih dahulu.</td>
            </tr>
        </tbody>
    </table>

</div>
<form action="{{ route('order.updateongkir') }}" method="POST"
      id="formPengiriman">
    @csrf

    <input type="hidden" name="kurir" id="pilihKurir">
    <input type="hidden" name="layanan_ongkir" id="pilihLayanan">
    <input type="hidden" name="biaya_ongkir" id="pilihBiaya">
    <input type="hidden" name="estimasi_ongkir" id="pilihEstimasi">
    <input type="hidden" name="total_berat" id="pilihBerat">

    <input type="hidden" name="alamat" id="pilihAlamat">
    <input type="hidden" name="pos" id="pilihPos">

    <input type="hidden" name="city_name" id="pilihKota">
    <input type="hidden" name="province_name" id="pilihProvinsi">
</form>
<script>
    const province = document.getElementById('province');
    const city = document.getElementById('city');

    // Data sementara, sesuaikan dengan data pesanan
    const totalBerat = 2480;
    const totalHarga = 200000;

    // Mengambil provinsi
    fetch('/get-provinces')
        .then(response => response.json())
        .then(result => {
            result.data.forEach(item => {
                province.innerHTML +=
                    `<option value="${item.id}">${item.name}</option>`;
            });
        });

    // Mengambil kota
    province.addEventListener('change', function () {
        city.innerHTML = '<option value="">Pilih Kota</option>';
        city.disabled = true;

        if (!this.value) return;

        fetch('/get-cities/' + this.value)
            .then(response => response.json())
            .then(result => {
                result.data.forEach(item => {
                    city.innerHTML +=
                        `<option value="${item.id}">${item.name}</option>`;
                });

                city.disabled = false;
            });
    });

    // Menghitung ongkir
    function checkOngkir() {
        const courier = document.getElementById('courier').value;

        if (!city.value || !courier) {
            alert('Pilih kota dan kurir terlebih dahulu!');
            return;
        }

        document.getElementById('pesan').innerText =
            'Sedang menghitung ongkir...';

        fetch('/cek-ongkir', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                origin: 23,
                destination: city.value,
                weight: totalBerat,
                courier: courier
            })
        })
        .then(response => response.json())
        .then(result => {
            const tbody = document.getElementById('result');
            tbody.innerHTML = '';

            if (result.meta && result.meta.status === 'success') {
                document.getElementById('pesan').innerText =
                    'Ongkos kirim berhasil dihitung.';

                result.data.forEach(ongkir => {
                    const estimasi = ongkir.etd.replace('day', 'hari');

                    tbody.innerHTML += `
                        <tr>
                            <td>${ongkir.service}</td>
                            <td>${Number(ongkir.cost).toLocaleString('id-ID')} Rupiah</td>
                            <td>${estimasi}</td>
                            <td>${totalBerat} Gram</td>
                            <td>Rp. ${totalHarga.toLocaleString('id-ID')}</td>
                            <td>
                             <button type="button"
    onclick='pilihPengiriman(
        ${JSON.stringify(ongkir.service)},
        ${Number(ongkir.cost)},
        ${JSON.stringify(estimasi)}
    )'
    style="background-color: #ff5e3a; color: white; border: none; padding: 10px 14px; cursor: pointer;">
    PILIH PENGIRIMAN
</button>
</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML =
                    '<tr><td colspan="6">Ongkir tidak ditemukan.</td></tr>';

                document.getElementById('pesan').innerText =
                    'Gagal mendapatkan ongkos kirim.';
            }
        })
        .catch(error => {
            console.error(error);
            document.getElementById('pesan').innerText =
                'Terjadi kesalahan saat menghitung ongkir.';
        });
    }

    // Memilih layanan pengiriman
    function pilihPengiriman(service, biaya, estimasi) {

    document.getElementById('pilihKurir').value =
        document.getElementById('courier').value;

    document.getElementById('pilihLayanan').value = service;
    document.getElementById('pilihBiaya').value = biaya;
    document.getElementById('pilihEstimasi').value = estimasi;

    document.getElementById('pilihBerat').value = totalBerat;

    document.getElementById('pilihAlamat').value =
        document.getElementById('alamat').value;

    document.getElementById('pilihPos').value =
        document.getElementById('kodepos').value;

    document.getElementById('pilihKota').value =
        city.options[city.selectedIndex].text;

    document.getElementById('pilihProvinsi').value =
        province.options[province.selectedIndex].text;

    document.getElementById('formPengiriman').submit();
}
</script>

</body>
</html>
