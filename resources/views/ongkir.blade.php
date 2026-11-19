<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Ongkos Kirim</title>
</head>
<body>

<h2>Cek Ongkos Kirim</h2>

<select id="province">
    <option value="">Pilih Provinsi</option>
</select>

<select id="city" disabled>
    <option value="">Pilih Kota</option>
</select>

<input type="number" id="weight" placeholder="Berat (gram)">

<select id="courier">
    <option value="">Pilih Kurir</option>
    <option value="jne">JNE</option>
    <option value="tiki">TIKI</option>
    <option value="pos">POS Indonesia</option>
</select>

<button type="button" onclick="checkOngkir()">Cek Ongkir</button>

<div id="result"></div>

<script>
    const province = document.getElementById('province');
    const city = document.getElementById('city');

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
        const weight = document.getElementById('weight').value;
        const courier = document.getElementById('courier').value;
        const destination = city.value;

        if (!destination || !weight || !courier) {
            alert('Lengkapi semua pilihan terlebih dahulu!');
            return;
        }

        document.getElementById('result').innerHTML = 'Menghitung ongkir...';

        fetch('/cek-ongkir', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                origin: 23,
                destination: destination,
                weight: weight,
                courier: courier
            })
        })
        .then(response => response.json())
        .then(result => {
    const hasil = document.getElementById('result');

    if (result.meta && result.meta.status === 'success') {
        hasil.innerHTML = '';

        result.data.forEach(function(ongkir) {
            const estimasi = ongkir.etd.replace('day', 'hari');

            hasil.innerHTML += `
                ${ongkir.service} : ${ongkir.cost} Rupiah (${estimasi})
                <br>
            `;
        });
    } else {
        hasil.innerHTML = 'Gagal mendapatkan ongkos kirim.';
    }
})
        .catch(error => {
            document.getElementById('result').innerHTML =
                'Gagal menghitung ongkir.';
            console.error(error);
        });
    }
</script>

</body>
</html>
