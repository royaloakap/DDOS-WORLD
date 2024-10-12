# With poor grammar, I refer to the people that say 'your' instead of 'you're', 'i' instead of 'I', 'dont' instead of 'don't', and so on.
# These little children should pay attention in school. Such grammar mistakes make you look more silly, and therefore you will archive less.
#
# This was made by XxBiancaXx#4356.
#
# LINKS:
# https://www.github.com/XxB1a/ddos-discord-bot
# https://www.instagram.com/moron420

from discord.ext import commands     # Commands
from discord.ext.commands import Bot # BOt
from os import system                # This will be used to clear the screen in on_ready()
from os import name                  # ^
from colorama import *               # This will be used to print our startup banner in color
import discord                       # D I S C O R D
import aiohttp                       # For our API Requests
import random                        # Random.randint(1,6) will be used in the random_color() function!

buyers  = [1, 2, 3]              # Replace digits with Discord USER-IDs!
admins  = [1, 2, 3]              # Replace digits with Discord USER-IDs! (admins!!)
owners  = [1046461397808197762]              # Replace digits with Discord USER-IDs! (owners, they cannot be removed!!)
token   = 'your_token_lol'                  # Discord Bot token
bot = commands.Bot(command_prefix = '!', intents= discord.Intents.all())

# This is our function to give embeds a random color!
# You can call it using 'await random_color()'
async def random_color():
    number_lol = random.randint(1, 999999)

    while len(str(number_lol)) != 6:
        number_lol = int(str(f'{random.randint(1, 9)}{number_lol}'))

    return number_lol

@bot.command()
async def топгп(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 геймпадеров на версии 1.1')
        await ctx.send('`1.Flower_GP \n2.Sumiko \n3.Stormos \n4.Fazze \n5.netcffel \n6.Noizyypvp \n7.WiqeenEz \n8.SanyaGP \n9.Nikmain \n10.XpaHuTeJl`')

@bot.command()
async def гпбе(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 геймпадеров СНГ на версии Bedrock Edition:')
        await ctx.send('`1.[🇷🇺] ImKmeta \n2.[🇷🇺] BSdonZ \n3.[🇺🇦] Robos \n4.[🇷🇺] LesbianPvP \n5.[🇰🇿] xTeqWeaze \n6.[🇰🇿] ImStillAlive \n7.[🇷🇺] SmertTopPvP \n8.[🇷🇺] ImDqsty \n9.[🇷🇺] Striffyx \n10.[🇧🇾] qCounteL`')

@bot.command()
async def топгпмира(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 геймпадеров мира (Bedrock Edition):')
        await ctx.send('`1.[🇷🇺] ImKmeta \n2.[🇮🇪] Cl0setCheats \n3.[🇺🇸] EJUKAD \n4.[🇺🇸] OpCranker \n5.[🇺🇸] FreshThePig \n6.[🇫🇷] yChroma \n7.[🇬🇧] owhyumad \n8.[🇺🇸] ZxtimeYT \n9.[🇬🇧] xKeremy \n10.[🇺🇸] RuggedTurtle10`')

@bot.command()
async def топпкмх(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 пк на сервере Minenex')
        await ctx.send('`1.Listen \n2.Yank \n3.Ka1do \n4.Mingo \n5.Shosty \n6.Lmao \n7.Parker \n8.Flay \n9.homak \n10.L4`')

@bot.command()
async def топгпдм(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 геймпадеров на сервере DoshikMine')
        await ctx.send('`1.Sexlenoring \n2.exploler \n3.lolik_comeback \n4.invali_comeback \n5.SqSamirkaLega \n6.Rolex \n7.yaiiimashiro \n8.SexLenorZ \n9.Arts \n10.kroli4`')

@bot.command()
async def топ(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('`Используйте !хелп`')

@bot.command()
async def топы(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('`Используйте !хелп`')

@bot.command()
async def топебланов(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 ебланов на всех версиях')
        await ctx.send('`1.Runi \n2.Under \n3.Bobik \n4.Lexan \n5.koteyka_78 \n6.Night \n7.DysterBoy \n8.Wiso \n9.Wheek \n10.MrBread`')

participants = []

@bot.command()
async def war(ctx, clan_name=None):
    if ctx.author.id not in owners:
        await ctx.send(f"❌ Сорян бротиш, {ctx.author}, но ты не создатель клана.")
        return

    if clan_name is None:
        await ctx.send('❗Укажите название клана')
    else:
        await ctx.send(f'🚀 Война клану {clan_name} успешно объявлена.')


@bot.command()
async def add(ctx, *members):
    if ctx.author.id not in owners:
        await ctx.send(f"❌ Сорян бротиш, {ctx.author}, но ты не создатель клана.")
        return

    participants.extend(members)
    await ctx.send('✅ Участники клана успешно добавлены.')


@bot.command()
async def list(ctx):
    if ctx.author.id not in owners:
        await ctx.send(f"❌ Сорян бротиш, {ctx.author}, но ты не создатель клана.")
        return

    if participants:
        participants_list = '\n'.join(participants)
        await ctx.send(f'Список участников:\n{participants_list}')
    else:
        await ctx.send('Список участников пуст.')


@bot.command()
async def red(ctx, *members):
    if ctx.author.id not in owners:
        await ctx.send(f"Sorry, {ctx.author}, but you aren't an owner!")
        return

    participants.clear()
    participants.extend(members)
    await ctx.send('Список участников успешно обновлен.')

@bot.command()
async def топпкдм(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 пк на сервере DoshikMine')
        await ctx.send('`1.Lmao \n2.Homeless \n3.Listen \n4.Destiny \n5.Kamru3u5 \n6.Flasty \n7.Heekirama \n8.xjiopka \n9.M1chi \n10.Runi`')

@bot.command()
async def хелп(ctx):
  embed = discord.Embed(
    title = 'MCPE Bot',
    colour = 4374015,
    description = '\n```\n! - префикс 🤖\n```\n```\n!хелп - помощь 🤗\n```\n```\n!топебланов - топ ебланов 🧐\n```\n```\n!топгпмира - топ гпшеров мира 🎮\n```\n```\n!топпк - топ пкашеров 1.1 🖥\n```\n```\n!топпе - топ пеешеров 📱\n```\n```\n!топпкмх - топ пкашеров майнекса 💻\n```\n```\n!топгпдм - топ гпшеров дошика 🕹\n```\n```\n!топпкдм - топ пкашеров дошика 🎧\n```\n```\n!топпкбе - топ пкашеров BE 👻\n```\n```\n!топгп - топ гпшеров 1.1 🔥\n```\n```\n!гпбе - топ гп бедрок 🔰\n```\n```\n!ban - Баны 🚫\n```\n```\n!kick - Кики 🖕\n```\n',
    url = 'https://discord.com/api/oauth2/authorize?client_id=1061356054962770051&permissions=8&scope=bot')
  await ctx.send(embed=embed)

@bot.command()
async def топпе(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 игроков с ПЕ на 1.1:')
        await ctx.send('`1.Rizent \n2.Fxlest \n3.Rainbow \n4.Sacred \n5.Zertix \n6.Resistance \n7.Lerny \n8.Bonn \n9.Mxrest \n10.Recwyll`')

@bot.command()
async def топдаунов(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 даунов во всем майнкрафте')
        await ctx.send('`1.Runi \n2.Runi \n3.Runi \n4.Runi \n5.Runi \n6.Runi \n7.Runi \n8.Runi \n9.Runi \n10.Runi`')
        
@bot.command()
async def топпк(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 пк на версии 1.1')
        await ctx.send('`1.Peepeldu \n2.uwugirl \n3.Lmao \n4.Listen \n5.Kezumi \n6.shielacetti \n7.rxvend \n8.DimaBilan \n9.mamulenok \n10.modifaer`')

@bot.command()
async def топпкбе(ctx, method : str = None, victim : str = None, port : str = None, time : str = None):
    if ctx.author.id not in buyers: # They didn't buy the bot!!
        await ctx.send('Топ 10 игроков России на бедрок версии:')
        await ctx.send('`1.vequter \n2.Khxqs \n3.XentryEU \n4.Listen \n5.alextanker6768 \n6.veqered \n7.Ghxxnas \n8.s1rfl3n \n9.IUnluckyFoxI \n10.zqcmx`')

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
        await bot.change_presence(activity=discord.Activity(type=discord.ActivityType.watching, name=f"{len(bot.guilds)} server!"))
        
    else:
        await bot.change_presence(activity=discord.Activity(type=discord.ActivityType.playing, name=f"!хелп"))

if __name__ == '__main__':
    init(convert=True)
    bot.run("MTEyMDgyOTAxMjUyNTE5MTE5MA.GdbGRi.TQqypbACt7QxfOApnHid2tCqbk3G_KzBk0mkk0")
