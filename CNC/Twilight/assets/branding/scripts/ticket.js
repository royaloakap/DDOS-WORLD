function handleErrors() {
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
}
var paymentIdCookie = document.cookie.split('; ').find(row => row.startsWith('ticket_id='))?.split('=')[1];

function newTicket() {
    // Get variables from page
    var title = document.getElementById('title').value;
    var message = document.getElementById('message').value;

    var ticketData = {
        title: title,
        message: message
    };

    $.post('/api/tickets/newTicket', JSON.stringify(ticketData), function(response) {
        var data = $.parseJSON(response);
        if (data.status === "success") {
            toastr['success']('Ticket submitted successfully', 'Success', { "toastClass": "toast-dark" });
            window.location.reload();
            document.cookie = `ticket_id=${ticket.id}; path=/`;
        } else if (data.status === "error") {
            toastr['error'](data.message, 'Error', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Failed to submit ticket', 'Error', { "toastClass": "toast-dark" });
        }
    }).fail(handleErrors); 
}

(function() {
    $.post('/api/tickets/history', function (data) {
        var response = $.parseJSON(data);
        if (response.status == "success") {
            const ticketInfoArray = response.tickets;
            const ticketsTableBody = document.getElementById("tickets");
            if (ticketsTableBody) {
                ticketInfoArray.forEach((ticket) => {

                    const newRow = document.createElement("tr");
                    var ticketStatus = ticket.status;

                    if (ticketStatus === 'open') {
                        var status = `<span class="badge bg-warning">Open</span>`;
                    } else if (ticketStatus === 'closed') {
                        var status = `<span class="badge bg-danger">Closed</span>`;
                    } else if (ticketStatus === 'waiting') {
                        var status = `<span class="badge bg-info">Waiting</span>`;
                    } else {
                        var status = `<span class="badge bg-danger">Unknown</span>`;
                    }
                    newRow.innerHTML = `
                        <td class="ticket-id">${ticket.id}</td>
                        <td>${ticket.title}</td>
                        <td>${ticket.date}</td>
                        <td>${status}</td>
                    `;

                    newRow.addEventListener("click", () => {
                        document.cookie = `ticket_id=${ticket.id}; path=/`;
                        window.location.href = `tickets?id=${ticket.id}`;
                    });

                    ticketsTableBody.appendChild(newRow);
                });
            } else {
                console.error("Element with id 'tickets' not found.");
            }
        } else if (response.status == "error") {
            toastr['warning'](response.message, 'Tickets', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Failed to load tickets', 'Error', { "toastClass": "toast-dark" });
        }
    }).fail(handleErrors);
})();
