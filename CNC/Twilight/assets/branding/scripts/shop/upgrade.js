function buyPlan(type) {
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
            $.post('/api/payments/buy', { plan_name: type }, function (data) {
                var response = $.parseJSON(data);
                if (response.status == "success") {
                    toastr['success']('Payment successful, your membership was updated', 'Store', { "toastClass": "toast-dark" });
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
    var i = 0
    function renderPlan(plan) {
        if (i < 4) {
            var container = document.querySelector('.all-plans-cards');
            var myDiv = document.createElement("div")
            myDiv.className = "col-lg mb-md-0 mb-4"
            myDiv.innerHTML = `
        <div class="card border rounded shadow-none">
            <div class="card-body">
                <h3 class="card-title text-center text-capitalize mb-1"></h3>
                <p class="text-center">${plan.name}</p>
                <div class="text-center">
                    <div class="d-flex justify-content-center">
                        <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
                        <h1 class="display-4 mb-0 text-primary">${plan.price}</h1>
                        <sub
                            class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/${plan.expiry}d</sub>
                    </div>
                </div>

                <ul class="ps-3 my-4 pt-2">
                    <li class="mb-2">${plan.duration} Seconds</li>
                    <li class="mb-2">${plan.concurrents} Con</li>
                    <li class="mb-2">Unlimited Test</li>
                    <li class="mb-2">VIP False</li>
                    <li class="mb-0">API ${plan.api}</li>
                </ul>

                <button
                    class="btn btn-primary btn-md waves-effect waves-light" type="submit" onclick="buyPlan('${plan.name}')">Upgrade</button>
            </div>
        </div>
    `
            container.appendChild(myDiv)
        } else if (i >= 4) {
            var container = document.querySelector('.all-plans-cards2');
            var myDiv = document.createElement("div")
            myDiv.className = "col-lg mb-md-0 mb-4"
            myDiv.innerHTML = `
        <div class="card border rounded shadow-none">
            <div class="card-body">
                <h3 class="card-title text-center text-capitalize mb-1"></h3>
                <p class="text-center">${plan.name}</p>
                <div class="text-center">
                    <div class="d-flex justify-content-center">
                        <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
                        <h1 class="display-4 mb-0 text-primary">${plan.price}</h1>
                        <sub
                            class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/${plan.expiry}d</sub>
                    </div>
                </div>

                <ul class="ps-3 my-4 pt-2">
                    <li class="mb-2">${plan.duration} Seconds</li>
                    <li class="mb-2">${plan.concurrents} Con</li>
                    <li class="mb-2">Unlimited Test</li>
                    <li class="mb-2">VIP False</li>
                    <li class="mb-0">API ${plan.api}</li>
                </ul>

                <button class="btn btn-primary btn-md waves-effect waves-light" type="submit" onclick="buyPlan('${plan.name}')">Upgrade</button>
            </div>
        </div>
    `
            container.appendChild(myDiv)
        }
        i++
    }

    function loadPlans(data) {
        if (data.status == "success") {
            data.plans.forEach(function (plan) {
                renderPlan(plan);
            });
        }
    }




    $.post('/api/dashboard/plans', function (data) {
        var json = $.parseJSON(data);
        loadPlans(json);
    })
})()