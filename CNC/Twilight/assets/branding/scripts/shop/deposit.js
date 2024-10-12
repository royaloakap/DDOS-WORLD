function handleErrors() {
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
}

function generateTransaction() {
 
    var amount=$('#amount').val();	
    var coin=$('#crypto').val();
    var coupon=$('#coupon').val();

    toastr['info']('Transaction is being generated...', 'Payments', { "toastClass": "toast-dark" });

    $.post('/api/payments/create', {amount, coin, coupon}, function(data) {
        var response = $.parseJSON(data);
        if (response.status == "success") {
            document.cookie = `payment_id=${response.id}; path=/`;

            window.location.href = 'transaction';
        } else if (response.status == "error") {
            toastr['warning'](response.message, 'Payments', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Failed to generate transaction', 'Error', { "toastClass": "toast-dark" });
        }

    }).fail(handleErrors);

}

function updatePayments() {
    $.post('/api/payments/history', function (data) {
        var response = $.parseJSON(data);
        if (response.status == "success") {

            const paymentInfoArray = response.payments;

            const transactionsTableBody = document.getElementById("transactions");

            transactionsTableBody.innerHTML = '';

            paymentInfoArray.forEach((payment) => {
                const newRow = document.createElement("tr");
                var cryptoNames = {
                    btc: 'bitcoin',
                    eth: 'ethereum',
                    ltc: 'litecoin',
                    xmr: 'monero',
                    usdttrc20: 'USDT TRC20',
                    usdterc20: 'USDT ERC20',
                    bnbmainnet: 'Binance Coin Mainnet',
                    trx: 'TRON'
                };

                var cryptoCoin = payment.coin;
                var cryptoName = cryptoNames[cryptoCoin] || 'Unknown Crypto';

                var paymentStatus = payment.status;

                if (paymentStatus === 'waiting') {
                    var status = `<span id="payment_status" class="badge bg-warning">Waiting for transaction</span>`;
                } else if (paymentStatus === 'confirming') {
                    var status = `<span id="payment_status" class="badge bg-primary">Waiting for confirmations</span>`;
                } else if (paymentStatus === 'finished') {
                    var status = `<span id="payment_status" class="badge bg-success">Payment confirmed</span>`;
                } else if (paymentStatus === 'expired') { 
                    var status = `<span id="payment_status" class="badge bg-danger">Payment expired</span>`;
                } else if (paymentStatus === 'partially_paid') {
                    var status = `<span id="payment_status" class="badge bg-danger">Partial funds (contact support)</span>`;
                } else {
                    var status = `<span id="payment_status" class="badge bg-warning">Unknown</span>`;
                }
                newRow.innerHTML = `
                    <td class="transaction-id"><a href="#">#${payment.id}</a></td>
                    <td>${status}</td>
                    <td>$${payment.amount}</td>
                    <td>${cryptoName}</td>
                    <td>${new Date(payment.creation_date).toLocaleDateString()}</td>
                `;

                newRow.addEventListener("click", () => {
                    document.cookie = `payment_id=${payment.transaction}; path=/`;

                    window.location.href = 'transaction';
                });
                transactionsTableBody.appendChild(newRow);
            });
        } else if (response.status == "error") {
            toastr['warning'](response.message, 'Payments', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Failed to load transactions', 'Error', { "toastClass": "toast-dark" });
        }
    }).fail(handleErrors);
}

updatePayments();

setInterval(function () {
    updatePayments();
}, 3000);
