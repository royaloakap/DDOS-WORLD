
function handleErrors() {
    toastr['error']('Failed to call the API', 'Error', { "toastClass": "toast-dark" });
    document.getElementById("layer4_button").disabled = false;
    document.getElementById("layer7_button").disabled = false;
    document.getElementById("StopAll_button").disabled = false;
}



function startAttack() {
    var host = $("#target").val();
    var port = $("#port").val();
    var duration = $("#duration").val();
    var method = $("#method").val();
    var pps = $("#pps").val();
    
    var threadSlider = document.getElementById("threadSlider");
    var threads = threadSlider.noUiSlider.get();

    var concurrentslider = document.getElementById("concurrentSlider");
    var concurrents = concurrentslider.noUiSlider.get();
    console.log(concurrents);

    document.getElementById("startatk").disabled = true;

    $.post('/api/start', {
        host,
        port,
        duration,
        method,
        concurrents,
        threads,
        pps
    }, function (response) {
        console.log(response.error)
        var json = $.parseJSON(response);
        console.log(json)
        if (json.status == "success") {
            toastr['success']('Attack sent succesfully', 'Attacks', { "toastClass": "toast-dark" });
            document.getElementById("startatk").disabled = false;
            attacks();
        } else if (json.status == "error") {
            toastr['warning'](json.message, 'Attacks', { "toastClass": "toast-dark" });
            document.getElementById("startatk").disabled = false;
            attacks();
        } else {
        document.getElementById("startatk").disabled = false;
        attacks();
        }
    })
};

function loadMethods() {
    var methodSelect = document.getElementById("method");

    $.post('/api/dashboard/user_id', function (data) {
        var json = $.parseJSON(data);
        console.log
        console.log("data2", json);
        
        if (json.user_membership === "Free") {
            // If user membership is "Free", display only one method
            var option = document.createElement("option");
            option.value = Free;
            option.text = Free;
            console.log(option);
            methodSelect.appendChild(option);
        }
    }).fail(handleErrors);

    $.post('/api/attacks/methods', function (response) {
        var json = $.parseJSON(response);
        if (json.status == "success") {
            console.log(json.methods);
            if (json.user_membership !== "Free") {
                // If user membership is not "Free", display all available methods
                for (var i = 0; i < json.methods.length; i++) {
                    var option = document.createElement("option");
                    option.value = json.methods[i].method;
                    option.text = json.methods[i].panel_method;
                    console.log(option);
                    methodSelect.appendChild(option);
                }
            }
        } else if (json.status == "error") {
            toastr['warning'](response.message, 'Attacks', { "toastClass": "toast-dark" });
        }
    });
}


var countdownIntervals = {}; // Store intervals for countdowns

function attacks() {
    $.post('/api/attacks/running', function (response) {
        var json = $.parseJSON(response);
        if (json.status == "success") {
            var table = $('#attacks')
            table.empty();
            console.log(json);
            json.data.forEach(function (attack) {
                var target = attack.target.includes('http') ? new URL(attack.target).host : attack.target;
                var rowID = attack.id;
                var expires = attack.expires;
                var countdown;

                if (expires == 0 || expires < 0) {
                    countdown = 'expired';
                } else {
                    countdown = '<div id="expire' + rowID + '">' + expires + '</div>'
                    if (!countdownIntervals[rowID]) {
                        countdownIntervals[rowID] = setInterval(function () {
                            var countdownElement = $('#expire' + rowID);
                            if (!countdownElement.length) {
                                clearInterval(countdownIntervals[rowID]);
                                delete countdownIntervals[rowID];
                                return;
                            }

                            expires--;
                            console.log(rowID, expires);
                            if (expires == 0 || expires < 0) {
                                clearInterval(countdownIntervals[rowID]);
                                delete countdownIntervals[rowID];
                                update_attacks();
                            } else {
                                countdownElement.html(expires);
                            }
                        }, 1000);
                    }
                }
                var action = '';
                if (parseInt(attack.date_sent) + parseInt(attack.time) > Math.floor(Date.now() / 1000) && attack.stopped !== 1) {
                    action = '<button onclick="stop(' + rowID + ')" id="action-btn-' + rowID + '" class="btn btn-danger btn-md" type="button">STOP</button>';
                }

                var row = '<tr>' +
                    '<td style="text-align:center;">' + rowID + '</td>' +
                    '<td style="text-align:center;">' + target + '</td>' +
                    '<td style="text-align:center;">' + countdown + '</td>' +
                    '<td style="text-align:center;">' + attack.method + '</td>' +
                    '<td style="text-align:center;">' + action + '</td>' +
                    '</tr>';

                table.append(row);
            })
        }else if (json.status == "error") {
            toastr['warning'](json.message, 'Attacks', { "toastClass": "toast-dark" });
        } else {
            toastr['error']('Failed to load attacks', 'Error', { "toastClass": "toast-dark" });
        }
    }).fail(handleErrors);
}


var isUpdateAttacksRunning = false;

function update_attacks() {
    if (isUpdateAttacksRunning) {
        return;
    }

    // Set the lock to prevent further calls
    isUpdateAttacksRunning = true;

    if (isUpdateAttacksRunning === false) {
        attacks();

        isUpdateAttacksRunning = false;
    }

    // The rest of your code for handling updates, if any, goes here

    // After completing the update, unlock the function
}

function stop(id) {

    $.post('/api/stop/'+id, function(data) {
        var response = $.parseJSON(data)
        if (response.status == "success") {
            toastr['success']('Attack stopped successfully', 'Attacks', { "toastClass": "toast-dark" });
            attacks();
        } else if (response.status == "error") {
            toastr['warning'](response.message, 'Attacks', { "toastClass": "toast-dark" });
            attacks();
        } else {
            toastr['error']('Failed to stop attack, try again later', 'Error', { "toastClass": "toast-dark" });
            attacks();
        }
    }).fail(handleErrors);
}

$(window).on('load', function () {
    loadMethods();
    attacks();
    size = document.getElementById('concurrentSlider');
    if (typeof size !== undefined && size !== null) {
        // Range
        noUiSlider.create(size, {
            start: 1,
            step: 1,
            range: {
                min: 1,
                max: 10
            },
            tooltips: false,
            pips: {
                mode: 'steps',
                stepped: true,
                density: 5
            }
        });
    }
    size = document.getElementById('threadSlider');
    if (typeof size !== undefined && size !== null) {
        // Range
        noUiSlider.create(size, {
            start: 1,
            step: 1,
            range: {
                min: 1,
                max: 12
            },
            tooltips: false,
            pips: {
                mode: 'steps',
                stepped: true,
                density: 5
            }
        });
    }
});

