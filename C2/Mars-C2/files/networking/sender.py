import socket as socket
import time

def send(socket: socket.socket, data:str, escape=True, reset=True):
    if reset:
        data += "[0m"
    if escape:
        data += '\r\n'
    socket.send(data.encode())