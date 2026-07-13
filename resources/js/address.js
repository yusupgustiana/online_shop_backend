document.addEventListener("DOMContentLoaded", function () {

    const province = document.getElementById('province');
    const city = document.getElementById('city');
    const district = document.getElementById('district');

    if (!province) return;

    // 🔥 TAMBAH INI (PROVINSI → SIMPAN NAMA)
    province.addEventListener('change', function () {
        document.getElementById('prov_name').value =
            this.options[this.selectedIndex].text;
    });

    // PROVINSI → KOTA
    province.addEventListener('change', function () {

        let provinceId = this.value;

        if (!provinceId) return;

        city.disabled = true;
        city.innerHTML = '<option>Loading...</option>';

        fetch(`/rajaongkir/cities/${provinceId}`)
            .then(res => res.json())
            .then(res => {

                city.innerHTML = '<option value="">Pilih Kota</option>';
                district.innerHTML = '<option value="">Pilih Kecamatan</option>';
                district.disabled = true;

                res.data.forEach(item => {
                    city.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });

                city.disabled = false;
            });
    });

    // 🔥 TAMBAH INI (KOTA → SIMPAN NAMA)
    city.addEventListener('change', function () {
        document.getElementById('city_name').value =
            this.options[this.selectedIndex].text;
    });

    // KOTA → KECAMATAN
    city.addEventListener('change', function () {

        let cityId = this.value;

        if (!cityId) return;

        district.disabled = true;
        district.innerHTML = '<option>Loading...</option>';

        fetch(`/rajaongkir/districts/${cityId}`)
            .then(res => res.json())
            .then(res => {

                district.innerHTML = '<option value="">Pilih Kecamatan</option>';

                res.data.forEach(item => {
                    district.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });

                district.disabled = false;
            });
    });

    // 🔥 TAMBAH INI (KECAMATAN → SIMPAN NAMA)
    district.addEventListener('change', function () {
        document.getElementById('district_name').value =
            this.options[this.selectedIndex].text;
    });

});