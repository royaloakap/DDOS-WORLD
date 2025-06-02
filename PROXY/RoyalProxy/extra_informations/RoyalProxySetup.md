

Operating System: `Ubuntu 24.04`
Ram: `8 Gb`
CPU: `4 core` @ `1 Ghz`
Storage: `3 Gb`

# How To Setup Royal Proxy v1.1.1.2

# Before we begin, you will want to disable your IPV6 address on your server by running the following 3 commands:

sudo sysctl -w net.ipv6.conf.all.disable_ipv6=1
sudo sysctl -w net.ipv6.conf.default.disable_ipv6=1
sudo sysctl -w net.ipv6.conf.lo.disable_ipv6=1

# Royal Proxy uses JSON to store client data so make sure you read and edit the assets/config.json file.

*   Upload the "RoyalProxy" folder that you received in the "ROYALPROXY.zip" onto your server.
*   Replace line 5 in assets/config.json with your server IP

1. Install PM2 ( check in Royal SRC DOCS)

2. `cd RoyalProxy`

3. `chmod 777 *`

4. `./RoyalProxy`

5. Not Error ? CTRL + c

# Install NVM and Node.js:
```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.4/install.sh | bash
source ~/.bashrc
nvm install 22.9.0
nvm use 22.9.0
```

## What it should look like: Node.js version 22.9.0 installs

*Step #14*
# Install PM2:
```bash
npm install n -g
n latest
npm i pm2 -g
```

6. `pm2 start "./RoyalProxy" --name RoyalProxy`

7. `pm2 log RoyalProxy`

Thats it, you are all setup. Enjoy!

To check out my other services: https://RoyalProjets.com