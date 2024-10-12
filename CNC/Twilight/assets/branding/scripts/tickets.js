function getCookie(name) {
    const value = "; " + document.cookie;
    const parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}
function handleErrors() {
    console.log("AJAX Request Failed:", status);
    toastr['error']('Failed to load ticket: ' , 'Error', { "toastClass": "toast-dark" });
}


function renderMessages(messages) {
    var messagesContainer = document.getElementById('message');
    getCurrentUserID().then(function(currentUserID) {
        console.log("Current User ID:", currentUserID);
        
        // Remove existing messages
        while (messagesContainer.firstChild) {
            messagesContainer.removeChild(messagesContainer.firstChild);
        }

        messages.forEach(function(message) {
            var messageDiv = document.createElement('div');
            messageDiv.classList.add('message');

            var messageText = document.createElement('p');
            messageText.textContent = message.message;

            var dateFooter = document.createElement('div');
            dateFooter.classList.add('date-footer');
            dateFooter.textContent = new Date(message.created_at).toLocaleDateString(); // Format the date

            console.log("Message User ID:", message.user_id);
            if (message.user_id === currentUserID) {
                messageDiv.classList.add('user-message'); // Apply CSS class for user message
            } else {
                messageDiv.classList.add('message'); // Apply CSS class for other user's message
            }

            messageDiv.appendChild(messageText);
            messageDiv.appendChild(dateFooter);

            messagesContainer.appendChild(messageDiv);
        });
    }).catch(function(error) {
        console.error('Failed to fetch current user ID:', error);
    });
}


// Define the getCurrentUserID function
function getCurrentUserID() {
    // Make an AJAX request to fetch the current user's ID
    return $.get('/api/dashboard/user_id').then(function(data) {
        return data.user_id;
    }).fail(function(xhr, status, error) {
        console.error('Failed to fetch current user ID:', error);
    });
}

function updateTicket() {
    // Get the ticket ID and message from the form
    var ticketIdStr = getCookie('ticket_id');
    var messageInput = document.getElementById('messageInput');
    var message = messageInput.value;
    var ticketId = parseInt(ticketIdStr, 10);
    
    // Create the JSON data object
    var jsonData = {
        ticketid: ticketId,
        message: message
    };

    // Send a POST request to the API endpoint
    $.ajax({
        url: '/api/tickets/update',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(jsonData),
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.status === "success") {
                // Clear the input after successful update
                messageInput.value = "";
            } else if (data.status === "error") {
                toastr['error'](data.message, 'Error', { "toastClass": "toast-dark" });
            } else {
                toastr['error']('Failed to submit ticket', 'Error', { "toastClass": "toast-dark" });
            }
        },
        error: function(xhr, status, error) {
            toastr['error']('Failed to submit ticket: ' + error, 'Error', { "toastClass": "toast-dark" });
        }
    });
}

var lastMessageId = null;

//on load
(function() {
    fetchAndRenderNewMessages();
})();


function updateMessagesPeriodically() {
    setInterval(function() {
        fetchAndRenderNewMessages();
    }, 1000);
}

function fetchAndRenderNewMessages() {
    var ticketId = getCookie('ticket_id');
    console.log("Ticket ID:", ticketId);

    // Send a request to fetch new messages since the last displayed message
    $.post('/api/tickets/messages', { ticketid: ticketId, lastMessageId: lastMessageId }).done(function(data) {
        console.log(data);
        var jsonData = $.parseJSON(data);

        // Update lastMessageId with the latest message ID
        if (jsonData.messages.length > 0) {
            lastMessageId = jsonData.messages[jsonData.messages.length - 1].id;
        }

        // Render the new messages
        renderMessages(jsonData.messages);
    });
}


updateMessagesPeriodically();