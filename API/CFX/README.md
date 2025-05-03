# CFX Lookup Tool

## 📌 Project Overview

CFX Lookup Tool is a command-line utility designed to obtain detailed information about FiveM servers using their CFX code. This simple and efficient tool offers a quick way to retrieve metadata, player statistics, and other useful information about any public FiveM server.

## 🛠️ Key Features

- **CFX Code Lookup**: Retrieve comprehensive information via a simple CFX code
- **API Mode**: Provides an HTTP endpoint for integration with other services
- **JSON Format**: Structured and easy-to-parse responses
- **Low Memory Footprint**: Optimized for minimal resource usage
- **Cross-Platform**: Works on Windows, Linux, and macOS

## 🔍 Available Information

- **General Information**: Server name, description, tags, version
- **Player Statistics**: Number of connected players, maximum capacity
- **Player List**: IDs and names of currently connected players
- **Resources**: List of active resources/scripts on the server
- **Configuration**: Public server parameters
- **Location**: Hosting country and region
- **Performance**: Response time and server status

## 🚀 Usage

### API Mode

To start the API server:

```bash
./ApiCFX
```

Then access the endpoint:

```
GET http://localhost/fivem?cfx={CFX_CODE}
```

### Example Output

```json
           {
                    "error": true,
                    "message": "API response will be here."
                }
                OR
                    "ip_info": {
                      "City": "Gravelines",
                      "Country": "FR",
                      "Hostname": "ip134.ip-137-74-33.eu",
                      "IP": "137.74.33.134",
                      "Loc": "50.9865,2.1281",
                      "Org": "AS16276 OVH SAS",
                      "Postal": "59820",
                      "Readme": "https://ipinfo.io/missingauth",
                      "Region": "Hauts-de-France",
                      "Timezone": "Europe/Paris"
                },
                    "server": {
                      "CurrentClients": 1,
                      "DiscordLink": "https://discord.gg/stellantia",
                      "EnhancedHosting": "true",
                      "Gametype": "Stellantia RP",
                      "IP": "137.74.33.134",
                      "Map": "San Andreas",
                      "MaxClients": 128,
                      "Name": "^5Stellantia RP^0",
                      "Owner": "DrekRS",
                      "Port": "30176",
                      "ProjectDesc": "Compatible manette ! Serveur français FreeAccess basé sur un RP USA, plongez dans un RP immersif !",
                      "ProjectName": "Stellantia RP",
                      "ResourcesCount": 249,
                      "ServerVersion": "FXServer-master v1.0.0.12180 linux"
                }
```

## 💻 Installation

### Manual Installation

1. Download the binary for your system
2. Make it executable (Linux/macOS):
   ```bash
   chmod +x ApiCFX
   ```
3. Move it to a directory included in your PATH:
   ```bash
   # Linux/macOS
   sudo mv ApiCFX /usr/local/bin/
   ```

## 🌐 Integration

### Example with curl

```bash
curl http://localhost/fivem?cfx=abcd123
```

### Example with Python

```python
import requests

def get_fivem_server_info(cfx_code):
    response = requests.get(f"http://localhost/fivem?cfx={cfx_code}")
    return response.json()
```

### Example with Node.js

```javascript
const axios = require('axios');

async function getFiveMServerInfo(cfxCode) {
  const response = await axios.get(`http://localhost/fivem?cfx=${cfxCode}`);
  return response.data;
}
```

## ⚠️ Limitations

- The tool can only access public and listed FiveM servers
- Some information may not be available if the server has restricted it
- In API mode, rate limits are applied to prevent abuse

## 📜 License

This project is distributed under the MIT License. See the `LICENSE` file for more information.

## 🔗 Useful Links

- [RoyalProjets](https://royalprojets.com/)