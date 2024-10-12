import discord
from discord.ext import commands

intents = discord.Intents.default()  # Создаем объект intents
intents.typing = True  # Отключаем набор текста
intents.presences = True  # Отключаем информацию о присутствии участников

bot = commands.Bot(command_prefix='/c ', intents=intents)

owners = ['1120830207411097710']  # Замените YOUR_OWNER_ID на реальный ID владельца

@bot.event
async def on_ready():
    print('Bot is ready.')

@bot.command()
async def help(ctx):
    await ctx.send("/c info - информация о клане\n/c list - отправляет список участников клана\n/c addlist (текст, список участников клана, в строчку, типо в низ под каждым ником)\n/c red - редактирует список участников клана, она должна работать так /c red (новый список участников клана) при использовании команды /c red список клана обновляется, при команде /c list список должен обновиться\n/c war - объявление войны другому клану, она должна работать так (/c war (название клана), после этой команды бот отправляет сообщение 'Война успешно объявлена клану (название клана который указали в аргументе).'")

@bot.command()
async def info(ctx):
    # Логика для команды /c info
    await ctx.send("Информация о клане")

@bot.command()
async def list(ctx):
    # Логика для команды /c list
    await ctx.send("Список участников клана")

@bot.command()
async def addlist(ctx, *, text):
    # Логика для команды /c addlist
    await ctx.send(f"Добавление списка участников клана: {text}")

@bot.command()
@commands.check_any(commands.is_owner(), commands.has_permissions(administrator=True))
async def red(ctx, *, new_list):
    # Логика для команды /c red
    await ctx.send(f"Обновление списка участников клана: {new_list}")

@bot.command()
@commands.check_any(commands.is_owner(), commands.has_permissions(administrator=True))
async def war(ctx, clan_name):
    # Логика для команды /c war
    await ctx.send(f"Война успешно объявлена клану {clan_name}.")

bot.run('MTEyMDgyOTAxMjUyNTE5MTE5MA.GdbGRi.TQqypbACt7QxfOApnHid2tCqbk3G_KzBk0mkk0')  # Замените YOUR_BOT_TOKEN на токен вашего бота


