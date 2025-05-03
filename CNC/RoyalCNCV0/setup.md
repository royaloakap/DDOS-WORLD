# Royal CNC – Installation and Troubleshooting Guide

Welcome to the installation and troubleshooting guide for Royal CNC, a secure SSH-based command and control (C2) tool. This guide will help you set up the necessary environment and troubleshoot common issues that might arise.

## System Requirements

- Ubuntu/Debian-based Linux system
- Minimum 1GB RAM
- 10GB free disk space
- Root or sudo access
- Open port for SSH access (default: 2137)

## Installation Process

### 1. System Update and Dependencies Installation

Update your system and install required dependencies:

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install mysql-server git golang-go screen -y
```

### 2. MySQL Server Configuration

Secure your MySQL installation:

```bash
sudo mysql_secure_installation
```

Follow the prompts to:
- Set a strong root password
- Remove anonymous users
- Disallow root login remotely
- Remove test database
- Reload privilege tables

### 3. Database Setup

Connect to MySQL:

```bash
sudo mysql -u root -p
```

Create the database and user:

```sql
CREATE DATABASE cnc;
USE cnc;

# Replace 'username' and 'strong_password' with your secure credentials
CREATE USER 'username'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON cnc.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Clone and Setup Royal CNC

Clone the repository:

```bash
git clone https://github.com/royaloakap/RoyalCNC.git
cd RoyalCNC
```

Import the database schema:

```bash
mysql -u root -p cnc < database.sql
```

### 5. SSH Key Generation

Generate SSH keys for secure authentication:

```bash
mkdir -p ssh
ssh-keygen -t rsa -b 4096 -f ssh/ssh.cat
```

Do not set a passphrase when prompted (press Enter twice).

### 6. Configuration

Edit the configuration file:

```bash
nano config.royal
```

Update the following parameters:

```
name=royal
attacksenabled=true
slots=5
generatehelp=true
generatemethods=true
license=YOUR_LICENSE_KEY
port=2137
mysqlhost=localhost
mysqluser=username
mysqlpassword=strong_password
mysqldb=cnc
```

Save and exit (Ctrl+X, Y, Enter).

### 7. Build and Run

Compile the application:

```bash
go build -o RoyalCNC
```

Run the application:

```bash
# For testing
./RoyalCNC

# For production (using screen to keep it running)
screen -S royalcnc
./RoyalCNC
```

To detach from the screen session, press Ctrl+A followed by D.

## Accessing Royal CNC

Connect to your server using an SSH client:

```bash
ssh -p 2137 username@your_server_ip
```

## Troubleshooting Common Issues

### Error 1: SSH Connection Issues

**Symptoms**: Unable to connect to server via SSH.

**Solutions**:
- Verify SSH service is running: `systemctl status ssh`
- Check if the port is open: `netstat -tuln | grep 2137`
- Ensure correct permissions on SSH keys:
  ```bash
  chmod 700 ssh
  chmod 600 ssh/ssh.cat
  chmod 644 ssh/ssh.cat.pub
  ```
- Verify firewall settings: `ufw status` and `ufw allow 2137/tcp` if needed

### Error 2: Database Connection Problems

**Symptoms**: Application fails to start with database errors.

**Solutions**:
- Verify MySQL is running: `systemctl status mysql`
- Check database credentials in config.royal
- Test database connection:
  ```bash
  mysql -u username -p cnc
  ```
- Ensure the database schema is properly imported:
  ```bash
  mysql -u username -p cnc -e "SHOW TABLES;"
  ```

### Error 3: Build or Runtime Errors

**Symptoms**: Application fails to build or crashes at runtime.

**Solutions**:
- Ensure Go is properly installed: `go version`
- Check for missing dependencies: `go mod tidy`
- Verify all required files exist
- Check logs for specific errors: `tail -f logs/royal.log`
- Ensure proper permissions on application directory

### Error 4: License Issues

**Symptoms**: License validation errors.

**Solutions**:
- Verify the license key in config.royal
- Ensure your server has internet access for license validation
- Check if the license is expired or invalid
- Contact support for license verification

## Additional Configuration

### Setting Up Methods

Edit the methods.txt file to configure attack methods:

```bash
nano methods.txt
```

Format for each method:
```
name:METHOD_NAME
command:COMMAND_TO_EXECUTE
description:DESCRIPTION
```

### User Management

To create the first admin user, use the MySQL console:

```sql
INSERT INTO users (username, password, admin, expiry) VALUES ('admin', 'hashed_password', 1, '2099-12-31');
```

## About Royal CNC

Royal CNC is a secure, SSH-based command and control (C2) system designed to provide a secure and flexible interface for managing and controlling systems remotely. Developed by @royaloakap, this system is optimized for a small footprint and high level of security, using SSH keys for authentication and a robust protocol for communications.

## Support

For any questions or additional support, please contact @royaloakap on Telegram or visit https://t.me/Royal_FAQ.