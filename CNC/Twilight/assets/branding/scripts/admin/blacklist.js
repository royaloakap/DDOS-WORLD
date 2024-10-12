function UpdateBlacklist() {
    var host =   document.getElementById('host').value;


    // Check if any field is empty
    if (host === '') {
        toastr['error']('Please fill in all fields!', 'Error', { "toastClass": "toast-dark" });
        return; // Exit the function if validation fails
    }
    
    // Prepare the data to be sent in the AJAX request
    var BlacklistData = {
        host: host,
    };
    console.log('Blacklist:', BlacklistData);
    // Make a POST request to the server
    post('/api/admin/blacklist', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(BlacklistData)
    })
    .then(response => {
        if (response.ok) {
            console.log('News updated successfully');
            toastr['success']('News updated successfully', 'Success', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Update Error', 'Error', { "toastClass": "toast-dark" });
        }
    })
    .catch(error => {
        console.error('Failed to update News:', error.message);
        toastr['error']('Failed to update News: ' + error.message, 'Error', { "toastClass": "toast-dark" });
    });
}