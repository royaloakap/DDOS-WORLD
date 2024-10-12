import os
import sys
import time

def rescreen(pid):
	while(True):
		if(not os.path.exists("/proc/" + pid)):
			os.system("screen -S myra -d -m ./cnc")
			return
		time.sleep(1)

rescreen(sys.argv[1])
