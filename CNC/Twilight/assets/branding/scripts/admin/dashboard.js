(function () {

    function handleErrors() {
        toastr['warning']('Failed to load dashboard data', 'Error', { "toastClass": "toast-dark" });
    }

    const layer4_network = document.querySelector('#network_load_layer4');
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
    function updateLayer4(data) {
        network_loadConfig.series = [{ data: data.Layer4 }]
        const network_load = new ApexCharts(layer4_network, network_loadConfig);
        network_load.render();
    }

    function populateData(data) {
        $('#profit').text('$'+data.profitCount);
        $('#users').text(data.userCount);
        $('#dailyAttacks').text(data.dailyAttackCount);

        loadUsers(data.users);
        loadPlans(data.plans);
    }

    function loadUsers(users) {
        var userSelect = document.getElementById("usersForm")
        
        for (var i = 0;i < users.length; i++) {
            var option = document.createElement("option")
                option.value = users[i].id;
                option.text = users[i].username;
                console.log(option);
                userSelect.appendChild(option);
        }
    }

    function loadPlans(plans) {
        var planSelect = document.getElementById("plansForm")
        
        for (var i = 0;i < plans.length; i++) {
            var option = document.createElement("option")
                option.value = plans[i].name;
                option.text = plans[i].name;
                console.log(option);
                planSelect.appendChild(option);
        }
    }


    $.post('/api/dashboard/running-attacks', function (data) {
        var json = $.parseJSON(data)
        console.log(json.Layer4)
        updateLayer4(json);
    }).fail(handleErrors);

    $.post('/api/admin/data', function (data) {
        var json = $.parseJSON(data);
        populateData(json);
    }).fail(handleErrors);


})();