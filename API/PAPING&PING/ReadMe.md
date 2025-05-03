# PaPing & Ping API

## 📌 Project Overview

PaPing & Ping API is a web service that allows checking the availability and latency of servers and ports via HTTP requests. This tool uses a rotating proxy system to perform TCP (PaPing) and ICMP (Ping) connectivity tests from different sources, providing a more comprehensive view of a service's accessibility.

## 🛠️ Key Features

- **TCP Port Testing (PaPing)**: Verification of the accessibility of a specific port on a server
- **ICMP Connectivity Testing (Ping)**: Verification of the general availability of a server
- **Rotating Proxy System**: Tests performed via different sources to avoid restrictions
- **JSON Format**: Structured and easy-to-integrate responses
- **Low Latency**: Optimized for fast responses
- **Cross-Platform**: Works on Windows, Linux, and macOS

## 🔍 Available Information

- **Connection Status**: Indicates if the server/port is accessible
- **Response Time**: Latency measured for each test
- **Proxy Details**: Information about the proxies used for each test
- **Statistics**: Summary of tests performed with success rate

## 🚀 Usage

### PaPing Endpoint (Port Testing)

```
GET /paping?ip={IP}&port={PORT}
```

Where `{IP}` is the IP address or hostname to test and `{PORT}` is the TCP port to check.

### Ping Endpoint (Connectivity Testing)

```
GET /ping?ip={IP}
```

Where `{IP}` is the IP address or hostname to test.

### PaPing Request Example

```bash
curl http://localhost/paping?ip=8.8.8.8&port=53
```

### Ping Request Example

```bash
curl http://localhost/ping?ip=8.8.8.8
```

### PaPing Response Example

```json
{
  "ip": "8.8.8.8",
  "port": "53",
  "results": [
    {
      "status": "up",
      "response_time": "45.125ms",
      "proxy": "203.0.113.1:8080"
    },
    {
      "status": "up",
      "response_time": "78.532ms",
      "proxy": "198.51.100.2:3128"
    },
    {
      "status": "down",
      "error": "server unreachable (8.8.8.8:53): i/o timeout",
      "proxy": "192.0.2.3:8080"
    }
  ]
}
```

### Ping Response Example

```json
{
  "ip": "8.8.8.8",
  "results": [
    {
      "status": "up",
      "response_time": "32.456ms",
      "proxy": "203.0.113.1:8080"
    },
    {
      "status": "up",
      "response_time": "65.789ms",
      "proxy": "198.51.100.2:3128"
    },
    {
      "status": "down",
      "error": "ping failed: exit status 1",
      "proxy": "192.0.2.3:8080"
    }
  ]
}
```

## 💻 Installation

### Prerequisites

- Go 1.16+
- Proxy file (proxy.txt)

### Manual Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/paping-ping-api.git
   cd paping-ping-api
   ```

2. Create a `proxy.txt` file containing a list of proxies (one per line) in the format `IP:PORT`:
   ```
   203.0.113.1:8080
   198.51.100.2:3128
   192.0.2.3:8080
   ```

3. Compile the program:
   ```bash
   go build -o PapingApi
   ```

4. Make it executable (Linux/macOS):
   ```bash
   chmod +x PapingApi
   ```

### Starting the Server

To start the API server:

```bash
./PapingApi
```

By default, the server listens on port 80. You can modify this behavior by editing the source code.

## 🔧 Technical Operation

### Proxy System

The API uses a rotating proxy system to perform tests. Proxies are loaded from a `proxy.txt` file at server startup. For each test, a proxy is randomly selected. If a proxy fails, it is removed from the list to avoid future failures.

### TCP Tests (PaPing)

For TCP tests, the API attempts to establish a TCP connection to the specified IP and port. If the connection succeeds, the response time is measured and returned. If the connection fails, an error is returned.

### ICMP Tests (Ping)

For ICMP tests, the API uses the operating system's `ping` command to send ICMP packets to the specified IP. The behavior is adapted according to the operating system (Windows or Linux/macOS).

## 🌐 Integration

### Example with Python

```python
import requests

def check_port(ip, port):
    response = requests.get(f"http://localhost/paping?ip={ip}&port={port}")
    return response.json()

def check_server(ip):
    response = requests.get(f"http://localhost/ping?ip={ip}")
    return response.json()
```

### Example with JavaScript

```javascript
async function checkPort(ip, port) {
  const response = await fetch(`http://localhost/paping?ip=${ip}&port=${port}`);
  return await response.json();
}

async function checkServer(ip) {
  const response = await fetch(`http://localhost/ping?ip=${ip}`);
  return await response.json();
}
```

## ⚠️ Limitations

- The API requires a file of valid proxies to function correctly
- ICMP tests (ping) may be blocked by some firewalls
- The accuracy of tests depends on the quality of the proxies used
- In case of intensive use, rate limits may be applied

## 📜 License

This project is distributed under the MIT License. See the `LICENSE` file for more information.

## 🔗 Useful Links

- [RoyalProjets](https://royalprojets.com/)
- [API Documentation](https://royalprojets.com/docs/paping-ping-api)