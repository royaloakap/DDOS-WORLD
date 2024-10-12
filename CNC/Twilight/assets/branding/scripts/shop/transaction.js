function handleErrors() {
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
}

var paymentIdCookie = document.cookie.split('; ').find(row => row.startsWith('payment_id='))?.split('=')[1];

$.post('/api/payments/status', { payment_id: paymentIdCookie }, function (response) {
    var data = $.parseJSON(response);
    console.log(data);
    if (data.status == "success") {
        updatePaymentStatus(data);
    } else if (data.status == "error") {
        toastr['warning'](data.message, 'Payment', { "toastClass": "toast-dark" });
    } else {
        toastr['error']('Failed to load transaction', 'Error', { "toastClass": "toast-dark" });
    }
}).fail(handleErrors);

function updatePaymentStatus(data) {
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

    var cryptoCoin = data.payment_info.crypto_coin;
    var cryptoName = cryptoNames[cryptoCoin] || data.payment_info.crypto_coin;

    $('#payment_crypto').val(cryptoName);
    $('#payment_address').val(data.payment_info.address);
    $('#payment_address').val(data.payment_info.crypto_address);
    $('#payment_amount').val(data.payment_info.crypto_amount);
    $('#payment_received').val(data.payment_info.recieved);

    var expirationDate = new Date(data.payment_info.expires);
    /*var formattedExpirationDate = new Intl.DateTimeFormat('en-US', {
        month: 'long', // Display the month in long format
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        timeZone: 'UTC'
    }).format(expirationDate);*/

    $('#payment_expire').val('20.02.2025');

    $('#loader_show').css('display', 'none');
    $('#qr_code_show').css('display', 'flex');
    $('#qr_code').attr('src', data.payment_info.qr_code);
    $('#payment_id').text('Payment #'+data.payment_info.id);
    var paymentStatus = data.payment_info.status;
    var statusElement = $('#payment_status');

    if (paymentStatus === 'waiting') {
        statusElement.removeClass('bg-success bg-warning bg-primary bg-danger').addClass('bg-warning');
        statusElement.text('Waiting for transaction');
    } else if (paymentStatus === 'confirming') {
        statusElement.removeClass('bg-success bg-warning bg-primary bg-danger').addClass('bg-primary');
        statusElement.text('Waiting for confirmations');
    } else if (paymentStatus === 'finished') {
        statusElement.removeClass('bg-success bg-warning bg-primary bg-danger').addClass('bg-success');
        statusElement.text('Payment confirmed');
    } else if (paymentStatus === 'expired') {
        statusElement.removeClass('bg-success bg-warning bg-primary bg-danger').addClass('bg-danger');
        statusElement.text('Payment expired');
    } else if (paymentStatus === 'partially_paid') {
        statusElement.removeClass('bg-success bg-warning bg-primary bg-danger').addClass('bg-danger');
        statusElement.text('Partial funds (contact support)');
    } else {
        statusElement.removeClass('bg-success bg-warning bg-primary bg-danger').addClass('bg-warning');
        statusElement.text('Loading payment information...');
    }
}

setInterval(function () {
    $.post('/api/payments/status', { payment_id: paymentIdCookie }, function (response) {
        var data = $.parseJSON(response);
        if (data.status == "success") {
            updatePaymentStatus(data);
        } else if (data.status == "error") {
            toastr['warning'](data.message, 'Payment', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Failed to load transaction', 'Error', { "toastClass": "toast-dark" });
        }
    }).fail(handleErrors);
}, 1000);