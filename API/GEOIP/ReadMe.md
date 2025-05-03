# GeoIP Lookup API

## 📌 Project Overview

GeoIP Lookup API is a web service that provides detailed geographic and network information from an IP address. This simple and efficient API offers a single access point to retrieve location, ISP, and other metadata associated with any public IP address.

## 🛠️ Key Features

- **IP Lookup**: Retrieve comprehensive information via a simple IP address
- **Precise Geographic Data**: Country, region, city, GPS coordinates
- **Network Information**: ASN, ISP, connection type
- **JSON Format**: Structured and easy-to-integrate responses
- **Low Latency**: Optimized for fast responses
- **Cross-Platform**: Works on Windows, Linux, and macOS

## 🔍 Available Information

- **Location**: Country, region, city, postal code, timezone
- **Coordinates**: Latitude and longitude
- **Network**: ASN, ISP name, organization
- **Metadata**: IP type (residential, datacenter, mobile)
- **Domain**: Hostname associated with the IP address (reverse DNS)

## 🚀 Usage

### Main Endpoint

```
GET /api/ip/{IP}
```

Where `{IP}` is the IPv4 or IPv6 address to look up.

### Request Example

```bash
curl http://localhost/api/ip/8.8.8.8
```

### Response Example

```json
{
  "ip": "8.8.8.8",
  "hostname": "dns.google",
  "city": "Mountain View",
  "region": "California",
  "country": "US",
  "loc": "37.4056,-122.0775",
  "org": "AS15169 Google LLC",
  "postal": "94043",
  "timezone": "America/Los_Angeles"
}
```

## 💻 Installation

### Manual Installation

1. Download the binary for your system
2. Make it executable (Linux/macOS):
   ```bash
   chmod +x ApiGeoIP
   ```
3. Move it to a directory included in your PATH:
   ```bash
   # Linux/macOS
   sudo mv ApiGeoIP /usr/local/bin/
   ```

### Starting the Server

To start the API server:

```bash
./ApiGeoIP
```

By default, the server listens on port 80. You can modify this behavior using environment variables.

## 🔧 Configuration

The program can be configured via environment variables:

- `PORT`: Server listening port (default: 80)
- `CACHE_DURATION`: Results caching duration in seconds (default: 3600)
- `RATE_LIMIT`: Maximum number of requests per IP per minute (default: 60)

## 🌐 Integration

### Example with curl

```bash
curl http://localhost/api/ip/1.1.1.1
```

### Example with Python

```python
import requests

def get_ip_info(ip):
    response = requests.get(f"http://localhost/api/ip/{ip}")
    return response.json()
```

### Example with JavaScript

```javascript
async function getIpInfo(ip) {
  const response = await fetch(`http://localhost/api/ip/${ip}`);
  const data = await response.json();
  return data;
}
```

### Example with PHP

```php
function getIpInfo($ip) {
    $response = file_get_contents("http://localhost/api/ip/{$ip}");
    return json_decode($response, true);
}
```

## ⚠️ Limitations

- The tool can only provide information for public IP addresses
- The accuracy of geographic data may vary by region
- In API mode, rate limits are applied to prevent abuse

## 📜 License

This project is distributed under the MIT License. See the `LICENSE` file for more information.

## 🔗 Useful Links

- [RoyalProjets](https://royalprojets.com/)
- [API Documentation](https://royalprojets.com/docs/geoip-api)