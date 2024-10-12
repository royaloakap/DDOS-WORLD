Proxy Server with Rate Limiting and IP Blacklisting

This Go application sets up a flexible proxy server with built-in rate limiting, IP blacklisting, and SSH client blocking features. It also includes Telegram integration for logging and notifications.

Features

    Proxy Management: Forward connections from proxy servers to target servers.
    IP Blacklisting: Dynamically block IPs based on rate limits and connection attempts.
    Rate Limiting: Control the number of connections from each IP to prevent abuse.
    SSH Client Blocking: Prevent connections from specific SSH clients.
    Telegram Integration: Send real-time logs and notifications to a Telegram chat.
    Configurable: Easily adjust settings via a JSON configuration file.

Configuration

    Proxies: Define multiple proxies with their respective IPs and ports.
    Rate Limiting: Set limits on connection attempts per IP.
    SSH Client Blocking: List SSH clients to block.
    IP Blacklisting: Configure the duration for blocking IPs.
    Telegram: Enable or disable Telegram notifications and set up credentials.

Usage

    Clone the Repository:

    bash

    git clone https://github.com/royaloakap/ddosworld

    go build -o proxy-server

Run the Application:

    ./proxy-server -config config.json

    Edit Configuration: Modify config.json to suit your needs.

Requirements

    Go 1.18+
    Network access to target servers
    (Optional) Telegram bot token and chat ID for logging
