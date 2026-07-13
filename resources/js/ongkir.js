document.addEventListener("DOMContentLoaded", function () {

    const address = document.getElementById('addressSelect');
    const ongkirList = document.getElementById('ongkirList');

    const shippingInput = document.getElementById('shipping_cost');
    const courierInput = document.getElementById('courier');
    const serviceInput = document.getElementById('service');

    const grandTotal = document.getElementById('grandTotal');
    const baseTotalEl = document.getElementById('baseTotal');

    if (!address || !baseTotalEl) return;

    let baseTotal = parseInt(baseTotalEl.value) || 0;

    address.addEventListener('change', function () {

        let selected = this.options[this.selectedIndex];
        let districtId = selected.getAttribute('data-district');

        if (!districtId) {
            ongkirList.innerHTML = "Alamat tidak valid";
            return;
        }

        ongkirList.innerHTML = "Loading ongkir...";

        fetch('/checkout/ongkir', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content')
            },
            body: JSON.stringify({
                district_id: districtId
            })
        })
            .then(res => res.json())
            .then(data => {

                ongkirList.innerHTML = "";

                data.forEach(item => {

                    let el = `
                <label class="flex justify-between items-center border rounded-lg p-3 cursor-pointer hover:bg-gray-50">
                    <div>
                        <p class="font-medium">${item.courier.toUpperCase()} - ${item.service}</p>
                        <p class="text-xs text-gray-500">Estimasi ${item.etd} hari</p>
                    </div>

                    <div class="text-right">
                        <p class="font-semibold">Rp ${item.cost.toLocaleString()}</p>

                        <input type="radio" name="ongkir_option"
                               class="mt-1"
                               data-cost="${item.cost}"
                               data-courier="${item.courier}"
                               data-service="${item.service}">
                    </div>
                </label>
                `;

                    ongkirList.innerHTML += el;
                });

                // 🔥 event pilih ongkir
                document.querySelectorAll('input[name="ongkir_option"]').forEach(radio => {

                    radio.addEventListener('change', function () {

                        let cost = parseInt(this.dataset.cost);

                        shippingInput.value = cost;
                        courierInput.value = this.dataset.courier;
                        serviceInput.value = this.dataset.service;

                        let total = baseTotal + cost;

                        grandTotal.innerText = 'Rp ' + total.toLocaleString();
                    });

                });

            })
            .catch(err => {
                console.error(err);
                ongkirList.innerHTML = "Gagal load ongkir";
            });

    });

});