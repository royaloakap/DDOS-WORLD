# Funnel - API Gateway for Attack Distribution

## Overview

Funnel is a lightweight API gateway designed to distribute attack requests across multiple backend APIs. It acts as a centralized entry point that forwards attack requests to configured target endpoints based on the specified method. This tool is particularly useful for load balancing, redundancy, and managing multiple attack APIs through a single interface.

## Features

- **Centralized API Gateway**: Single endpoint for multiple backend services
- **Method-Based Routing**: Route requests to different backends based on attack method
- **API Key Authentication**: Secure access with API key validation
- **Configurable Timeouts**: Set connection timeouts for backend services
- **Path Encoding Support**: Option to URL-encode parameters for target APIs
- **Verbose Logging**: Optional detailed logging for debugging and monitoring
- **Rate Limiting**: Maximum attack duration limits per method

## Configuration

Funnel is configured through a JSON file (`funnels.json`) with the following structure:

```json
{
    "listener": "127.0.0.1:8090",
    "api_key": "YOUR_API_KEY",
    "timeout": 3,
    "methods": {
        "method_name": {
            "enabled": true,
            "maxtime": 8000,
            "targets": [
                {
                    "method": "backend_method_name",
                    "target": "https://backend-api.example.com/attack",
                    "pathEncoding": true,
                    "verbosity": true
                }
            ]
        }
    }
}
```

### Configuration Parameters

- **listener**: The IP and port where Funnel will listen for incoming requests
- **api_key**: Authentication key required for all requests
- **timeout**: Connection timeout in seconds for backend services
- **methods**: Dictionary of attack methods with their configurations
  - **enabled**: Whether this method is active
  - **maxtime**: Maximum allowed attack duration in seconds
  - **targets**: List of backend APIs to forward requests to
    - **method**: Method name to use when forwarding to this target
    - **target**: URL of the backend API
    - **pathEncoding**: Whether to URL-encode parameters
    - **verbosity**: Enable detailed logging for this target

## Usage

### Starting the Server

```bash
./funnel
```

### Making Requests

To launch an attack, send a GET request to the `/attack` endpoint with the following parameters:

```
http://localhost:8090/attack?key=YOUR_API_KEY&method=METHOD_NAME&target=TARGET_IP&duration=DURATION&port=PORT
```

Parameters:
- **key**: Your API key for authentication
- **method**: The attack method to use
- **target**: The target IP address
- **duration**: Attack duration in seconds
- **port**: Target port number

### Example Request

```bash
curl "http://localhost:8090/attack?key=PwBlGAJbuOnRK86xE7ut&method=tcpbypass&target=192.168.1.1&duration=60&port=80"
```

## Target URL Templating

The target URLs support variable substitution using the following placeholders:

- `<<$target>>`: Will be replaced with the target IP
- `<<$duration>>`: Will be replaced with the attack duration
- `<<$port>>`: Will be replaced with the target port
- `<<$method>>`: Will be replaced with the method name

## Building from Source

### Prerequisites

- Go 1.16 or higher

### Build Instructions

```bash
git clone https://github.com/yourusername/funnel.git
cd funnel
go build -o funnel
```

## Security Considerations

- Always use a strong, randomly generated API key
- Run Funnel behind a firewall, exposing only the necessary port
- Consider using HTTPS for both the listener and backend services
- Regularly rotate API keys and review logs for unauthorized access attempts

## License

This project is licensed under the MIT License - see the LICENSE file for details.