(function() {
    function handleErrors() {
        toastr['warning']('Failed to load dashboard data', 'Error', { "toastClass": "toast-dark" });
    }

    function populateData(data) {
        if (data && data.userInfo && data.News) {
            const expiry = formatDate(data.userInfo.membership_expire);
            console.log('user expiry', data.userInfo.membership_expire);
            $('#expiry').text(expiry);
        } else {
            handleErrors();
        }
    }

    function formatDate(timestamp) {
        const time = timestamp * 1000;
        console.log(timestamp, time);
        const date = new Date(time);
        const options = { month: 'long', day: 'numeric', year: 'numeric' };
        return date.toLocaleDateString('en-US', options)
    }

    $.post('/api/dashboard/data', function (data) {
        var json = $.parseJSON(data)
        console.log(json);
        populateData(json);
    }).fail(handleErrors);
})();