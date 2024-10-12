(function() {

    function handleErrors() {
        console.error("AJAX Request Failed:", status, error);
        toastr['warning']('Failed to load dashboard data', 'Error', { "toastClass": "toast-dark" });
    }

    const layer4_network = document.querySelector('#network_load_layer4');
            const layer7_network = document.querySelector('#network_load_layer7');
            network_loadConfig = {
                chart: {
                    height: 90,
                    type: 'area',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: true
                    }
                },
                markers: {
                    colors: 'transparent',
                    strokeColors: 'transparent'
                },
                grid: {
                    show: false
                },
                colors: ['#28c76f'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.8,
                        stops: [0, 95, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                xaxis: {
                    show: true,
                    lines: {
                        show: false
                    },
                    labels: {
                        show: false
                    },
                    stroke: {
                        width: 0
                    },
                    axisBorder: {
                        show: false
                    }
                },
                yaxis: {
                    stroke: {
                        width: 0
                    },
                    show: false
                },
                tooltip: {
                    enabled: true,
                    custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                        const dataPoint = series[seriesIndex][dataPointIndex];
                        return '<div class="tooltip-inner">' + '<span class="tooltip-label">Running attacks: </span>' + '<span class="tooltip-value">' + dataPoint + '</span>' + '</div>';
                    }
                },
            };
            network_loadConfig1 = {
                chart: {
                    height: 90,
                    type: 'area',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: true
                    }
                },
                markers: {
                    colors: 'transparent',
                    strokeColors: 'transparent'
                },
                grid: {
                    show: false
                },
                colors: ['#28c76f'],
                fill: {
                    type: 'gradient',
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.8,
                        stops: [0, 95, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                xaxis: {
                    show: true,
                    lines: {
                        show: false
                    },
                    labels: {
                        show: false
                    },
                    stroke: {
                        width: 0
                    },
                    axisBorder: {
                        show: false
                    }
                },
                yaxis: {
                    stroke: {
                        width: 0
                    },
                    show: false
                },
                tooltip: {
                    enabled: true,
                    custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                        const dataPoint = series[seriesIndex][dataPointIndex];
                        return '<div class="tooltip-inner">' + '<span class="tooltip-label">Running attacks: </span>' + '<span class="tooltip-value">' + dataPoint + '</span>' + '</div>';
                    }
                },

            };
            function updateLayer4(data) {
                network_loadConfig.series = [{ data: data.Layer4 }]
                const network_load = new ApexCharts(layer4_network, network_loadConfig);
                network_load.render();
            }

            function updateLayer7(data) {
                network_loadConfig1.series = [{ data: data.Layer7 }];
                const network_load1 = new ApexCharts(layer7_network, network_loadConfig1);
                network_load1.render();
            }

            function renderTableRow(server) {
                let status;
                if (server.status == "Online") {
                    status = `<span class="badge bg-success">Online</span>`;
                } else {
                    status = `<span class="badge bg-danger">Offline</span>`;
                }
                return `
                <tr>
                    <td>${server.type} - ${server.name}</td>
                    <td>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: ${server.load}%" aria-valuenow="${server.load}" aria-valuemin="0" aria-valuemax="100">${server.load}%</div>
                    </div>
                    </td>
                    <td><span class="badge bg-primary">${server.type}</span></td>
                    <td>${status}</td>
                </tr>
                `;
            }
            
            function renderCard(server, cardId) {
                let status;
                if (server.status == "Online") {
                    status = `<span class="badge bg-success">Online</span>`;
                } else {
                    status = `<span class="badge bg-danger">Offline</span>`;
                }
            
                return `
                    <div class="col-md-12 col-12 mb-4">
                        <div class="card" id="${cardId}">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">${server.type} - ${server.name}</h5>
                                </div> 
                                <span class="badge bg-primary">${server.type}</span>
                            </div>
                            <div class="card-body">
                                <p>Server Load:</p>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: ${server.load}%" aria-valuenow="${server.load}" aria-valuemin="0" aria-valuemax="100">${server.load}%</div>
                                </div>
                                <div class="row justify-content-center"><!-- Centering the row -->
                                    <div class="col-md-4 mb-4">
                                        <small class="text-muted">Running</small>
                                        <br>
                                        ${server.runningAttacks} / ${server.slots}
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <small class="text-muted">Response Time</small>
                                        <br>
                                        ${status}
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <small class="text-muted">Server Name</small>
                                        <br>
                                        ${server.name}
                                    </div>
                                </div>
                                <div class="col-md-8 mb-8">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update Server</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            function loadServerCards(servers, containerLayer4, containerLayer7) {
                const rowLayer4 = $('<div class="row"></div>');
            
                servers['serversLayer4'].forEach(function(server, index) {
                    const cardId = `server-layer4-${index}`;
                    const card = renderCard(server, cardId);
                    const col = $('<div class="col-lg-4 col-md-6 col-sm-12 mb-4"></div>'); // Adjust column width for different screen sizes
                    col.append(card);
                    rowLayer4.append(col);
                });
            
                containerLayer4.append(rowLayer4);
            
                const rowLayer7 = $('<div class="row"></div>'); // Create a new row for serversLayer7
            
                servers['serversLayer7'].forEach(function(server, index) {
                    const cardId = `server-layer7-${index}`;
                    const card = renderCard(server, cardId);
                    const col = $('<div class="col-lg-4 col-md-6 col-sm-12 mb-4"></div>'); // Adjust column width for different screen sizes
                    col.append(card);
                    rowLayer7.append(col);
                });
            
                containerLayer7.append(rowLayer7);
            }
            
            function loadServerTable(servers, containerLayer4, containerLayer7) {
                servers['serversLayer4'].forEach(function(server) {
                    const tableRow = renderTableRow(server);
                    containerLayer4.append(tableRow);
                });
            
                servers['serversLayer7'].forEach(function(server) {
                    const tableRow = renderTableRow(server);
                    containerLayer7.append(tableRow);
                });
            }
            
            function loadServerData(servers) {
                const serverTableLayer4 = $('#server-data-layer4');
                const serverTableLayer7 = $('#server-data-layer7');
            
                const serverCardsLayer4 = $('#test');
                const serverCardsLayer7 = $('#test7');
            
                serverTableLayer4.empty();
                serverTableLayer7.empty();
                serverCardsLayer4.empty();
                serverCardsLayer7.empty();
            
                loadServerCards(servers, serverCardsLayer4, serverCardsLayer7);
                loadServerTable(servers, serverTableLayer4, serverTableLayer7);
            }
        

            function populateData(data) {
                $('#bal').text(data.userInfo.balance);
                const expiry = formatDate(data.userInfo.membership_expire);
                console.log('user expiry', data.userInfo.membership_expire);
                $('#expiry').text(expiry);
                $('#layer7_network').text(`${data.networkInfo.Layer7}/${data.networkInfo.Layer7Total} slots in use`);
                $('#layer4_network').text(`${data.networkInfo.Layer4}/${data.networkInfo.Layer4Total} slots in use`);
                loadTimeline(data.News);
                loadServerData(data);
            }

            function formatDate(timestamp) {
                const time = timestamp * 1000;
                console.log(timestamp, time);
                const date = new Date(time);
                const options = { month: 'long', day: 'numeric', year: 'numeric' };
                return date.toLocaleDateString('en-US', options)
            }

            function renderTimeline(item) {
                const formattedDate = formatDate(item.Date);
                return `
                <li class="timeline-item pb-4 timeline-item-success border-left-dashed">
                                                <span class="timeline-point timeline-point-primary"><i class="fa-regular fa-circle"></i></span>
                                                <div class="timeline-event pb-3">
                                                  <div class="timeline-header mb-sm-0 mb-3">
                                                    <h6 class="mb-0">${item.Title}</h6>
                                                    <span class="text-muted">${formattedDate}</span>
                                                  </div>
                                                  <p>
                                                      ${item.From}
                                                  </p>
                                                  <div class="d-flex justify-content-between">
                                                    <h6>${item.Content}</h6>
                                                    <div class="d-flex">
                                                          <i class="fa-regular fa-user"></i>
                                                    </div>
                                                  </div>
                                                </div>
                                              </li>
                                              `
            }

            function loadTimeline(news) {
                const timelineList = $('#timeline-list')
                news.forEach(function (item) {
                    const timelineItem = renderTimeline(item);
                    timelineList.append(timelineItem);
                })
            }

            $.post('/api/dashboard/running-attacks', function (data) {
                var json = $.parseJSON(data)
                console.log(json.Layer4)
                updateLayer4(json);
                updateLayer7(json);
            }).fail(handleErrors);


            $.post('/api/dashboard/data', function (data) {
                var json = $.parseJSON(data)
                console.log(json);
                populateData(json);
            }).fail(handleErrors);

})();


function UpdateNews() {
    var title   =   document.getElementById('title').value;
    var from    =   "Admin";
    var content =   document.getElementById('content').value;
// Get the current date without the time components
var currentDate = new Date();
currentDate.setHours(0, 0, 0, 0); // Set hours, minutes, seconds, and milliseconds to 0

// Convert the date to a Unix timestamp
var unixTimestamp = Math.floor(currentDate.getTime() / 1000); // Divide by 1000 to convert milliseconds to seconds


    // Check if any field is empty
    if (title === '' || content === '') {
        toastr['error']('Please fill in all fields!', 'Error', { "toastClass": "toast-dark" });
        return; // Exit the function if validation fails
    }
    
    // Prepare the data to be sent in the AJAX request
    var userData = {
        title: title,
        from: from,
        content: content,
        date: unixTimestamp
    };
    console.log('News Data:', userData);
    // Make a POST request to the server
    fetch('/api/admin/news', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
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
    $.post('/api/dashboard/data', function (data) {
        var json = $.parseJSON(data)
        console.log(json);
        populateData(json);
    }).fail(handleErrors);
}