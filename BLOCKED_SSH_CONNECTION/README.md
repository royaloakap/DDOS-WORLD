# Royal SSH Tarpit

## 📌 Project Presentation

Royal SSH Tarpit is a security tool designed to slow down and block brute force attacks on SSH services. By simulating a legitimate SSH server while deliberately slowing down connections, it helps waste automated attackers' time and protects your real SSH services.

## 🛠️ Main Features

- **SSH Tarpit**: Simulates an SSH server but intentionally slows down connections
- **Low resource consumption**: Designed to be lightweight and efficient
- **Flexible configuration**: Adjustable parameters for delay, maximum clients, etc.
- **Detailed logging**: Connection tracking and statistics generation
- **Multi-platform support**: Works on Linux, OpenBSD, and Solaris/illumos

## 📋 Project Structure
BLOCKED_SSH_CONNECTION/
├── royal # Main executable
├── README.md # This file
├── util/ # Support utilities and scripts
│ ├── openbsd/ # OpenBSD support
│ │ └── royal # rc.d script for OpenBSD
│ ├── pivot.py # Script to analyze logs and generate CSV statistics
│ ├── royal.service # systemd service file for Linux
│ └── smf/ # Solaris/illumos Service Management Facility support
│ ├── init.royal # SMF initialization script
│ ├── royal.conf # Example configuration file
│ └── royal.xml # SMF manifest
└── doc/ # Documentation (not visible in excerpt)

## 🚀 Installation and Configuration

### Prerequisites

- Compatible operating system (Linux, OpenBSD, Solaris/illumos)
- Administrator privileges for installation and configuration
- Python 3 with pyrfc3339 for analysis scripts (optional)

### Installation

#### Linux (systemd)

1. Copy the `royal` executable to `/usr/local/bin/`:
   ```bash
   cp royal /usr/local/bin/
   chmod +x /usr/local/bin/royal
   ```

2. Copy the systemd service file:
   ```bash
   cp util/royal.service /etc/systemd/system/
   ```

3. To allow royal to bind to port 22 (standard SSH):
   ```bash
   setcap 'cap_net_bind_service=+ep' /usr/local/bin/royal
   ```

4. Enable and start the service:
   ```bash
   systemctl daemon-reload
   systemctl enable royal
   systemctl start royal
   ```

#### OpenBSD

1. Copy the `royal` executable to `/usr/local/bin/`:
   ```bash
   cp royal /usr/local/bin/
   chmod +x /usr/local/bin/royal
   ```

2. Copy the rc.d script:
   ```bash
   cp util/openbsd/royal /etc/rc.d/
   chmod +x /etc/rc.d/royal
   ```

3. Add the following line to `/etc/rc.conf.local`:
   ```
   royal=YES
   ```

4. Start the service:
   ```bash
   /etc/rc.d/royal start
   ```

#### Solaris/illumos (SMF)

1. Copy the `royal` executable to `/usr/local/bin/`:
   ```bash
   cp royal /usr/local/bin/
   chmod +x /usr/local/bin/royal
   ```

2. Create the configuration directory:
   ```bash
   mkdir -p /usr/local/etc
   ```

3. Copy the configuration file:
   ```bash
   cp util/smf/royal.conf /usr/local/etc/
   ```

4. Copy the initialization script:
   ```bash
   cp util/smf/init.royal /lib/svc/method/
   chmod +x /lib/svc/method/init.royal
   ```

5. Import and enable the SMF service:
   ```bash
   svccfg import util/smf/royal.xml
   svcadm enable network/royal
   ```

## ⚙️ Configuration

The `royal.conf` configuration file contains the following parameters:

- `Port`: Listening port (22 by default for SSH)
- `Delay`: Response delay in milliseconds (10000 by default)
- `MaxLineLength`: Maximum accepted line length (32 by default)
- `MaxClients`: Maximum number of simultaneous clients (4096 by default)
- `LogLevel`: Log detail level (0=Silent, 1=Standard, 2=Debug)
- `BindFamily`: IP address family (0=IPv4+IPv6, 4=IPv4 only, 6=IPv6 only)

## 🔧 Usage

### Manual Start

To start royal manually with a configuration file:

```bash
./royal -c /path/to/royal.conf
```

### Analysis Script

For analyzing connection statistics:

```bash
python3 pivot.py -f /path/to/royal.log
```

## 📝 Documentation

For detailed documentation and usage examples, refer to the [Documentation](doc/README.md) folder.


