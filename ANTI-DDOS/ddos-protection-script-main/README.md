# DDoS Protection for Linux Machines
WARNING! This script usually does not work properly with LXD/LXC Containers, please only use on KVM-Based VPS's or Bare Metal Servers.


## For CentOS/RHEL/AlmaLinux
```
wget https://raw.githubusercontent.com/Swivro/ddos-protection-script/main/script-rhel.sh && chmod +x antiddos-rhel.sh && ./antiddos-rhel.sh
```

## For Ubuntu/Debian
```
wget https://raw.githubusercontent.com/Swivro/ddos-protection-script/main/antiddos-debian.sh && chmod +x antiddos-debian.sh && ./antiddos-debian.sh
```


After running the script, IPTables custom configuration was applied. If IPTables is not installed on your OS, it will be installed when running the script. 

### What does this script do?
This script uses IPtables. It will do a good job at protecting your machine against DDoS attacks, but it is never a bad idea to have additional DDoS protection from providers like PATH.NET, OVH, Cloudflare (only if absolutely necessary), etc. It adds configuration to drop invalid packets, drop packest which are TCP packets which are new and aren't SYN, block packets with odd/suspicious TCP flags, block packets with suspicious MSS values, block all spoofed packets, disable ICMP protocol (your machine will not respond to pings, making it impossible for people to send an attack to your VPS using the ping of death or a Layer 4 attack), limit the # of connections per IP connected, drop fragments in all chains, limit all RST packets, add bruteforce protection for SSH, and finally, add protection against port scanning.
