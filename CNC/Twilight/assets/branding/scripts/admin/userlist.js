function handleErrors() {
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
}

(function() {
    function renderUser(user) {
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
                        <a class="dropdown-item" href="javascript:void(0);" onclick="deleteUser2('${user.username}')"><i class="ti ti-trash me-1"></i>Delete</a>
                    </div>
                </div>
            </td>
        </tr>`;
    }

    function loadUsers(data) {
        if (data.status == "success") {
            const UsersList = $('#user-list');
            data.users.forEach(function(user) {
                const UserItem = renderUser(user);
                UsersList.append(UserItem);
            });
        }
    }

    // Load users
    $.post('/api/admin/user-list', function(data) {
        loadUsers(data);
    });

})();

function deleteUser2(username) {
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
            console.log('User deleted successfully');
            location.reload();
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
