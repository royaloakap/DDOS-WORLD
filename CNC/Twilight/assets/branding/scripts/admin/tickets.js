function handleErrors(xhr, status, error) {
    console.error('AJAX Request Failed:', status);
    toastr['error']('Failed to load tickets: ' + error, 'Error', { "toastClass": "toast-dark" });
}

(function() {
    loadTickets();

    // Function to render ticket cards
    function renderTickets(tickets) {
        console.log('Rendering tickets:', tickets);
        var ticketContainer = document.getElementById('ticketContainer');
        ticketContainer.innerHTML = '';

        tickets.forEach(function(ticket) {
            var cardId = 'ticketCard' + ticket.id;
            var cardHtml = `
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card" id="${cardId}">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-0">${ticket.username} - #${ticket.id}</h5>
                            </div>
                            <span class="badge bg-warning">${ticket.status}</span>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${ticket.title}</p>
                            <p class="card-text"><small class="text-muted">Last updated ${new Date(ticket.date * 1000).toLocaleDateString()}</small></p>
                        </div>
                    </div>
                </div>`;
            ticketContainer.insertAdjacentHTML('beforeend', cardHtml);
            
            var cardElement = document.getElementById(cardId);
            cardElement.addEventListener("click", function() {
                document.cookie = `ticket_id=${ticket.id}; path=/`;
                window.location.href = `ticket_manager?id=${ticket.id}`;
            });
        });
    }
    
    function loadTickets() {
        $.post('/api/admin/allTickets', function(response) {
            var data = $.parseJSON(response);
            ticketData(data);
        }).fail(handleErrors);
    }

    function ticketData(data) {
        console.log('Tickets:', data.tickets);
        renderTickets(data.tickets); // Pass only the tickets array to renderTickets
    }
})();

