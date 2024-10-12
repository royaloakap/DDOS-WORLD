function handleErrors() {
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
}

(function() {
    // Variable to store the current page number
    var currentPage = 1;

    function renderUser(user) {
        return `<option value="${user.username}" selected>${user.username}</option>`;
    }

    function renderUserTable(user) {
        return `<tr>
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.conns}</td>
            <td>${user.servers}</td>
            <td>${user.duration}</td>
            <td>${user.permissions}</td>
            <td>
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v fa-2x"></i></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-pencil me-1"></i>Edit</a>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="deleteUser('${user.username}')"><i class="ti ti-trash me-1"></i>Delete</a>
                    </div>
                </div>
            </td>
        </tr>`;
    }
    function loadUsers(page) {
        $.post('/api/admin/user-list', { page: page }, function(data) {
            if (data.status == "success") {
                const UsersList = $('#userOption');
                UsersList.empty(); // Clear previous options
                data.users.forEach(function(user) {
                    const UserItem = renderUser(user);
                    UsersList.append(UserItem);
                });

                const UserTable = $('#userTable');
                UserTable.empty(); // Clear previous table rows
                data.users.forEach(function(user) {
                    const UserItem = renderUserTable(user);
                    UserTable.append(UserItem);
                });

                // Update pagination links
                updatePagination(data.totalPages, page);
            } else {
                handleErrors();
            }
        });
    }

    // Function to update pagination links
    function updatePagination(totalPages, currentPage) {
        var paginationList = $('.pagination');
        paginationList.empty(); // Clear previous pagination links

        for (var i = 1; i <= totalPages; i++) {
            var listItem = $('<li class="page-item"><a class="page-link" href="#">' + i + '</a></li>');
            if (i === currentPage) {
                listItem.addClass('active');
            }
            listItem.click(function() {
                loadUsers($(this).text());
            });
            paginationList.append(listItem);
        }
    }

    // Load users for the first page
    loadUsers(currentPage);

})();

function UpdateUser() {
    // Retrieve values from form fields
    var username = document.getElementById('userOption').value;
    var concurrents = parseInt(document.getElementById('concurrents').value);
    var servers = parseInt(document.getElementById('servers').value);
    var balance = parseInt(document.getElementById('balance').value);
    var mbt = parseInt(document.getElementById('mbt').value); 
    // Get the value of the date input field
    var expireDateValue = document.getElementById("expire").value;

    // Convert the date string to a Date object
    var expireDate = new Date(expireDateValue);

    // Get the Unix timestamp for the whole date
    var unixTimestamp = expireDate.getTime() / 1000;

    // Check if any field is empty or invalid
    if (!username || isNaN(concurrents) || isNaN(servers) || isNaN(balance) || isNaN(mbt)) {
        toastr['error']('Please fill in all fields with valid values!', 'Error', { "toastClass": "toast-dark" });
        return; // Exit the function if validation fails
    }
    
    // Retrieve selected roles from the multi-select dropdown
    var roleOptions = document.getElementById('roleOption').selectedOptions;
    var roles = Array.from(roleOptions).map(option => option.value);

    // Prepare the data to be sent in the AJAX request
    var userData = {
        username: username,
        concurrents: concurrents,
        servers: servers,
        duration: mbt,
        ranks: roles.map(role => ({ name: role, has: true })),
        expiry: unixTimestamp,
        balance: balance
    };
    console.log('User Data:', userData);
    // Make a POST request to the server
    fetch('/api/admin/update-user', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => {
        if (response.ok) {
            toastr['success']('User updated successfully', 'Success', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Update Error', 'Error', { "toastClass": "toast-dark" });
        }
    })
    .catch(error => {
        toastr['error']('Failed to update user: ' + error.message, 'Error', { "toastClass": "toast-dark" });
    });
}

function deleteUser(username) {
    var deleteUser = {
        username: username
    };
    console.log('User Data:', deleteUser);
    // Make a POST request to the server
    fetch('/api/admin/delete-user', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(deleteUser)
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
            toastr['success']('User deleted successfully', 'Error', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Update Error', 'Error', { "toastClass": "toast-dark" });
        }
    })
    .catch(error => {
        console.error('Failed to delete user:', error.message);
        toastr['error']('Failed to delete user: ' + error.message, 'Error', { "toastClass": "toast-dark" });
    });
}
