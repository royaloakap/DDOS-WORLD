function handleErrors() {
    console.error("AJAX Request Failed:", status, error);
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
}

function buyPlan(type) {
    console.log('plan_name:', type);
    console.log('buyPlan function started'); // Log when the function starts
    Swal.fire({
        icon: 'question',
        iconColor: '#fff', 
        text: 'Are you sure you want to buy this membership?',
        showCancelButton: true,
        confirmButtonText: 'Buy Now',
        background: '#2f3349',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('/api/payments/buyaddon', { addon_name: type }, function (data) {
                var response = $.parseJSON(data);
                if (response.status == "success") {
                    toastr['success']('Payment successful, your addon was updated', 'Store', { "toastClass": "toast-dark" });
                } else if (response.status == "error") {
                    toastr['warning'](response.message, 'Store', { "toastClass": "toast-dark" });
                } else {
                    toastr['error']('Failed to purchase membership', 'Error', { "toastClass": "toast-dark" });
                }
            }).fail(handleErrors);
        };
    });
}

(function () {
    var i = 0;

    function renderAddon(addon) {
        if (i < 4) {
            var container = document.querySelector('.addons-list');
            var myDiv = document.createElement("div");
            myDiv.className = "col-lg mb-md-0 mb-4";
            myDiv.innerHTML = `
        <div class="card border rounded shadow-none">
            <div class="card-body">
                <h3 class="card-title text-center text-capitalize mb-1"></h3>
                <p class="text-center">${addon.name}</p>
                <div class="text-center">
                    <div class="d-flex justify-content-center">
                        <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
                        <h1 class="display-4 mb-0 text-primary">${addon.price}</h1>
                        <sub class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/${addon.expiry}d</sub>
                    </div>
                </div>
                <button class="btn btn-primary btn-md waves-effect waves-light" type="submit" onclick="buyPlan('${addon.name}')">Upgrade</button>
            </div>
        </div>
    `;
            container.appendChild(myDiv);
        }
        i++;
    }

    function loadAddons(data) {
        if (data.status == "success") {
            data.addons.forEach(addon => renderAddon(addon));
        }
    }

    $.post('/api/dashboard/addons', function (data) {
        var jsonData = $.parseJSON(data);
        console.log("data:", jsonData);
        loadAddons(jsonData);
    });
})();
