const ws = new WebSocket('ws://localhost:8080/ws');

const rpsData = [];
const trafficData = [];
const maxDataPoints = 60; // Keep last 60 seconds

const rpsChart = new Chart(document.getElementById('rpsChart'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'RPS',
            data: rpsData,
            borderColor: 'blue',
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { display: true },
            y: { beginAtZero: true }
        }
    }
});

const trafficChart = new Chart(document.getElementById('trafficChart'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Traffic Rate (KB/s)',
            data: trafficData,
            borderColor: 'green',
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { display: true },
            y: { beginAtZero: true }
        }
    }
});

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);

    // Update metrics
    document.getElementById('rps').textContent = data.rps.toFixed(2);
    document.getElementById('traffic-rate').textContent = (data.traffic_rate / 1024).toFixed(2);
    document.getElementById('cpu-usage').textContent = data.cpu_usage.toFixed(2);
    document.getElementById('memory-usage').textContent = data.memory_usage.toFixed(2);

    // Update charts
    rpsData.push(data.rps);
    trafficData.push(data.traffic_rate / 1024); // Convert to KB/s
    if (rpsData.length > maxDataPoints) {
        rpsData.shift();
        trafficData.shift();
    }

    const labels = Array.from({ length: rpsData.length }, (_, i) => i);
    rpsChart.data.labels = labels;
    trafficChart.data.labels = labels;
    rpsChart.update();
    trafficChart.update();

    // Update anomalies table
    const tableBody = document.getElementById('anomalies-table');
    tableBody.innerHTML = '';
    data.anomalies.forEach(anomaly => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-2">${anomaly.SourceIP}</td>
            <td class="px-4 py-2">${anomaly.Score.toFixed(2)}</td>
            <td class="px-4 py-2">${new Date(anomaly.Timestamp).toLocaleString()}</td>
        `;
        tableBody.appendChild(row);
    });
};

ws.onclose = function() {
    console.log('WebSocket connection closed');
};