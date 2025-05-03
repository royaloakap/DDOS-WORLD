# RoyPing - TCP Port Connectivity Tester

## Overview

RoyPing is a Python-based TCP port connectivity testing tool that allows you to check if a specific port on a target server is open and accessible. This lightweight utility provides visual feedback with colorful terminal output, making it easy to monitor connection status in real-time.

## Features

- **TCP Port Testing**: Verify if specific ports are open on target servers
- **Colorful Interface**: Visually appealing terminal output with color-coded results
- **Continuous Monitoring**: Automatically retries connections at regular intervals
- **Cross-Platform**: Works on Windows, macOS, and Linux
- **Low Resource Usage**: Minimal system requirements

## Installation

### Prerequisites

- Python 3.6 or higher
- pip (Python package manager)

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/royaloakap/RoyPing.git
   cd RoyPing
   ```

2. Install the required dependencies:
   ```bash
   pip install -r requirements.txt
   ```

## Usage

Run the script with Python:

```bash
python RoyPing.py
```

When prompted:
1. Enter the target IP address or hostname
2. Enter the port number to test

The tool will continuously attempt to connect to the specified port and display the results in real-time.

## Example Output

```
        ___     _    ___     _    __    __    ___   ___ ╗ ╔═══════════════╗
       / o |  .' \  / o |  .' \  /  \  / /  ,' _/  / _/ ║ ║.gg/paradiseC2 ║
      / _,'  / o / /  ,'  / o / / o | / /  _\ `.  / _/  ║ ╠═══════════════╣
     /_/    /_n_/ /_/`_\ /_n_/ /__,' /_/  /___,' /___/  ║ ║    RoyalOak   ║
                                                        ║ ╚═══════════════╝
        ╔═════════════════════════════════════════════════════════════════╗
        ║             [!] Roy Pinger by .gg/paradiseC2 [!]               ║
        ║                                                                 ║
        ║                  [&] t.me/paradiseC2 [&]                        ║
        ╚═════════════════════════════════════════════════════════════════╝

[>>>] IP: 8.8.8.8

[>>>] Port: 53

                [OK] Connection to 8.8.8.8 in port 53 [By .gg/paradiseC2] [OK]
                [OK] Connection to 8.8.8.8 in port 53 [By .gg/paradiseC2] [OK]
                [OK] Connection to 8.8.8.8 in port 53 [By .gg/paradiseC2] [OK]
```

## Dependencies

- **fade**: For creating gradient color effects in terminal text
- **colorama**: For cross-platform colored terminal output
- **socket**: Python standard library for network connections

## Stopping the Tool

To stop the continuous testing, press `Ctrl+C` in your terminal.

## Customization

You can modify the following aspects of the tool:

- **Connection timeout**: Edit the `sock.settimeout(1)` value in the code
- **Retry interval**: Change the `time.sleep(0.1)` value to adjust how frequently connections are attempted
- **Colors and appearance**: Modify the color variables at the top of the script

## Troubleshooting

If you encounter issues:

1. **Connection errors**: Verify that the target IP and port are correct
2. **Display issues**: Ensure your terminal supports ANSI color codes
3. **Import errors**: Confirm all dependencies are installed correctly

## License

This project is distributed under the MIT License. See the `LICENSE` file for more information.

## Credits

Developed by RoyalOak for the Paradise C2 community.

## Links

- Discord: [Here](https://royalprojets.com)