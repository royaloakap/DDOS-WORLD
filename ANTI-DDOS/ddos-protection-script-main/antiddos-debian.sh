apt update
echo Packages Updated.
sleep 1
apt install iptables-persistent
iptables -N SAFEZONE
iptables -N IN_DPI_RULES 
iptables -N IN_CUSTOMRULES

echo "Dropping bogus TCP pkts before conntrack occurs to save CPU and replicating in mangle table to ensure no rule interferes with our DROPs"
iptables -t raw -A PREROUTING -p tcp --tcp-flags FIN,SYN,RST,PSH,ACK,URG NONE -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags FIN,SYN FIN,SYN -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags SYN,RST SYN,RST -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags FIN,RST FIN,RST -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags FIN,ACK FIN -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ACK,URG URG -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ACK,FIN FIN -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ACK,PSH PSH -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ALL ALL -m comment --comment "xmas pkts (xmas portscanners)" -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ALL NONE -m comment --comment "null pkts (null portscanners)" -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ALL FIN,PSH,URG -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ALL SYN,FIN,PSH,URG -j DROP
iptables -t raw -A PREROUTING -p tcp --tcp-flags ALL SYN,RST,ACK,FIN,URG -j DROP
iptables -t raw -A PREROUTING -s 224.0.0.0/3 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 169.254.0.0/16 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 172.16.0.0/12 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 192.0.2.0/24 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 10.0.0.0/8 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 0.0.0.0/8 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 240.0.0.0/5 -m comment --comment "BOGONS" -j DROP
iptables -t raw -A PREROUTING -s 127.0.0.0/8 ! -i lo -m comment --comment "Only lo iface can have an addr-range of 127.0.0.x/8" -j DROP
echo "Using the mangle table to drop pkts before a routing decision is made also saves CPU resources"
iptables -t mangle -A PREROUTING -m conntrack --ctstate INVALID -j DROP
iptables -t mangle -A PREROUTING -p tcp ! --syn -m conntrack --ctstate NEW -m comment --comment "DROP new packets that don't present the SYN flag" -j DROP
iptables -t mangle -A PREROUTING -p tcp -m conntrack --ctstate NEW -m tcpmss ! --mss 536:65535 -m comment --comment "DROP new pkts that have malformed mss values" -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags FIN,SYN,RST,PSH,ACK,URG NONE -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags FIN,SYN FIN,SYN -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags SYN,RST SYN,RST -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags FIN,RST FIN,RST -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags FIN,ACK FIN -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ACK,URG URG -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ACK,FIN FIN -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ACK,PSH PSH -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ALL ALL -m comment --comment "xmas pkts (xmas portscanners)" -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ALL NONE -m comment --comment "null pkts (null portscanners)" -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ALL FIN,PSH,URG -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ALL SYN,FIN,PSH,URG -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags ALL SYN,RST,ACK,FIN,URG -j DROP
iptables -t mangle -A PREROUTING -p tcp --tcp-flags SYN,ACK,FIN,RST RST -m limit --limit 1/s --limit-burst 2 -m comment --comment "limit RST pkts (half-handshakes)" -j DROP
iptables -t mangle -A PREROUTING -s 224.0.0.0/3 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 169.254.0.0/16 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 172.16.0.0/12 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 192.0.2.0/24 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 10.0.0.0/8 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 0.0.0.0/8 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 240.0.0.0/5 -m comment --comment "BOGONS" -j DROP
iptables -t mangle -A PREROUTING -s 127.0.0.0/8 ! -i lo -m comment --comment "Only lo iface can have an addr-range of 127.0.0.x/8" -j DROP
#iptables -t mangle -A PREROUTING -s 192.168.0.0/16 -m comment --comment "BOGONS" -j DROP # check if you require this CLASS C addr-range before enabling
iptables -A INPUT -i lo -j ACCEPT
iptables -A INPUT -m conntrack --ctstate ESTABLISHED,RELATED -m comment --comment "ESTABLISHED,RELATED conns quick accept" -j ACCEPT
iptables -A INPUT -m comment --comment "Security Rules" -j IN_DPI_RULES
iptables -A INPUT -m comment --comment "Allowed Ports and Services" -j IN_CUSTOMRULES
#iptables -A INPUT -m comment --comment "Log All Dropped packets" -j LOG --log-prefix "[IPTABLES-BLOCKED]: " --log-level 7 ## This rule is mostly for debug incase you missed a rule or service to allow in
iptables -A INPUT -m comment --comment "Explicitly DROP other connections" -j DROP
iptables -A IN_DPI_RULES -m conntrack --ctstate INVALID -m comment --comment "Drop INVALID state connections" -j DROP
iptables -A IN_DPI_RULES -m conntrack --ctstate UNTRACKED -m comment --comment "Drop UNTRACKED state connections" -j DROP
iptables -A IN_DPI_RULES -m comment --comment "Jump back to main filter rules" -j RETURN
/sbin/iptables -A IN_CUSTOMRULES -p tcp --dport ssh -m conntrack --ctstate NEW -m recent --set
/sbin/iptables -A IN_CUSTOMRULES -p tcp --dport ssh -m conntrack --ctstate NEW -m recent --update --seconds 60 --hitcount 10 -j DROP
iptables -A IN_CUSTOMRULES -p tcp -m tcp --dport 22 -m conntrack --ctstate NEW -s 0.0.0.0/0 -m comment --comment "Allow SSH" -j SAFEZONE
iptables -A IN_CUSTOMRULES -m comment --comment "Jump back to main filter rules" -j RETURN
iptables -A IN_CUSTOMRULES -m comment --comment "Explicit drop rule */paranoid*/" -j DROP
iptables -A SAFEZONE -s x.x.x.x/32 -m comment --comment "allow-ingress-from-xxx-secure-IP" -j ACCEPT
iptables -A SAFEZONE -s x.x.x.x/32 -m comment --comment "allow-ingress-from-xxx-secure-IP" -j ACCEPT
iptables -A SAFEZONE -s x.x.x.x/32 -m comment --comment "allow-ingress-from-xxx-hq" -j ACCEPT
iptables -A SAFEZONE -m comment --comment "JUMP back to IN_CUSTOMRULES chain" -j RETURN

echo "Firewall configuration successfully applied. If you would like to undo this config, edit the script and replace -A with -D."
