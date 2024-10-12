import requests
import time

ip = input(" entrer une IP pour la localiser : ")
print("")

response = requests.get("http://ip-api.com/json/" + ip).json()
print(response)

print(response['lat'])
print(response['lon'])
print("")
input("Best IP logger BY ROY ! ")
input("discord.gg/RoyalC2")         