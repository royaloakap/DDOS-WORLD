from telegram import Update, InlineKeyboardMarkup, InlineKeyboardButton, ChatPermissions
from telegram.ext import ContextTypes, CallbackContext
import json
from datetime import datetime, timedelta
from telegram.constants import ChatMemberStatus
import psutil
import cpuinfo 
import time
import html
import requests


with open('config.json', 'r') as f:
    config = json.load(f)
licensekey = config['licensekey'] 
product = "stresser"
api_key = "G1gje4OBpsAr5gpkqvEgAiRHdumxIGUo"
url = "https://89.213.158.173:3000/api/client"

headers = {'Authorization': api_key}
data = {'licensekey': licensekey, 'product': product }
response = requests.post(url, headers=headers, json=data)
status = response.json()

if status['status_overview'] == "success":
    print("Etape 1", licensekey)
    print("Creer par Royaloakap discord.gg/BOTFR Discord ID: " + status['discord_id'])
else:
    print("Etape1", licensekey)
    print("Create a ticket in our discord server to get one. discord.gg/BOTFR or @royaloakap")
    exit()
ADMIN_IDS = config['ADMIN_IDS']

def load_config():
    with open('config_store.json', 'r') as file:
        return json.load(file)

CONFIG = load_config()

DEFAULT_STORE_DATA = {
    "time": 60,
    "concurrent": 1,
    "vip": True,
    "bypass_blacklist": False,
    "expire": 30,
    "price": 20
}

bot_start_time = time.time()

async def handle_ping_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    start_time = time.time()  
    system_info = get_system_info()
    response_time_ms = (time.time() - start_time) * 1000

    uptime_str = format_duration(time.time() - bot_start_time)

    message_text = (
        f"🏓 ᴩᴏɴɢ : {response_time_ms:.3f}ᴍs\n\n"
        f"StresserFR Stat\n\n"
        f"↬ ᴜᴩᴛɪᴍᴇ : {uptime_str}\n"
        f"↬ ʀᴀᴍ : {system_info['ram_percent']}%\n"
        f"↬ ᴄᴘᴜ ᴍᴏᴅᴇʟ : {system_info['cpu']['brand_raw']}\n"
        f"↬ ɴᴜᴍʙᴇʀ ᴏꜰ ᴄᴏʀᴇꜱ : {system_info['cpu']['num_cores']}\n"
    )

    for core_num, usage in system_info['cpu']['usage_per_core'].items():
        message_text += f"    • ᴄᴏʀᴇ {core_num} : {usage}%\n"

    await update.message.reply_text(message_text, parse_mode='HTML')


def get_system_info():
    cpu_info = cpuinfo.get_cpu_info()
    cpu_info_dict = {
        'brand_raw': cpu_info['brand_raw'],
        'num_cores': psutil.cpu_count(logical=False),
        'usage_per_core': {}
    }
    for i, percent in enumerate(psutil.cpu_percent(percpu=True)):
        cpu_info_dict['usage_per_core'][i + 1] = percent

    ram_percent = psutil.virtual_memory().percent

    return {'cpu': cpu_info_dict, 'ram_percent': ram_percent}

def format_duration(seconds):
    m, s = divmod(seconds, 60)
    h, m = divmod(m, 60)
    return f"{int(h)}ʜ:{int(m)}ᴍ:{int(s)}s"

from telegram import InlineKeyboardButton, InlineKeyboardMarkup

async def stresser(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    intro_message = (
        "👋 <b>Welcome to Royal Bot Stresser!</b>\n\n"
        "I'm here to assist you with various tasks. Here are some things you can do:\n\n"
        "1. Use the <code>/attack</code> command to launch an attack.\n"
        "2. Reach out to the admin for assistance or if you have any questions.\n\n"
        "Feel free to explore! If you need helpme, just type <code>/helpme</code>.\n"
    )
    keyboard = InlineKeyboardMarkup([
        [InlineKeyboardButton("🌟 Stresser Group !", url="https://t.me/stresserfr")]
    ])
    await update.message.reply_text(intro_message, parse_mode='HTML', reply_markup=keyboard)

async def helpme_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    helpme_message = (
        "🚀 <b>Welcome to StresserFR Help!</b>\n\n"
        "Here are the available commands:\n\n"
        "🎯 /attack - Start an attack\n"
        "🛑 /bl - Blacklist management\n"
        "➕ /add - Add a user\n"
        "🚫 /ban - Ban a user\n"
        "✅ /unban - Unban a user\n"
        "⚙️ /running - View running attacks\n"
        "🔧 /method - Manage attack methods\n"
        "📋 /listban - List banned users\n"
        "⏳ /plan - View your current plan\n"
        "🏁 /Stresser - Start the bot\n"
        "💳 /buy - Buy a plan\n"
        "🛒 /store - View the store\n"
        "🔝 /set - Promote VIP users\n"
        "🏓 /ping - Handle ping command\n"
        "🎟️ /redeem - Redeem a plan / license.\n\n"
        "Click the button below for more details and usage instructions."
    )

    keyboard = InlineKeyboardMarkup([[InlineKeyboardButton("🚀 My Reputation !", url="https://t.me/vouchroyal")]])

    await update.message.reply_text(helpme_message, parse_mode='HTML', reply_markup=keyboard)



async def modify_ban_status(update: Update, context: ContextTypes.DEFAULT_TYPE, ban_status: bool) -> None:
    user_id = update.effective_user.id
    if user_id not in ADMIN_IDS:
        return

    if update.message.reply_to_message:
        target_id = update.message.reply_to_message.from_user.id
    else:
        args = context.args
        if len(args) != 1:
            await update.message.reply_text("Usage:<code> /ban [user_id] or /unban [user_id]</code>", parse_mode='HTML')
            return
        target_id = args[0]

    try:
        with open('users.json', 'r+') as file:
            users = json.load(file)
            if str(target_id) in users:
                users[str(target_id)]['banned'] = ban_status
                file.seek(0)
                json.dump(users, file, indent=4)
                file.truncate()
                action = "banned" if ban_status else "unbanned"
                await update.message.reply_text(f"User <code>{target_id}</code> has been <code>{action}</code>.", parse_mode='HTML')
            else:
                await update.message.reply_text("User ID not found.")
    except Exception as e:
        await update.message.reply_text(f"Error modifying ban status: {e}")

async def ban_user(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
  await modify_ban_status(update, context, True)

async def unban_user(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
  await modify_ban_status(update, context, False)

async def blacklist_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    user_id = update.effective_user.id
    if user_id not in ADMIN_IDS:
        return

    args = context.args
    if not args:
        await update.message.reply_text('Usage: /bl [list|rm target|target]\nExample:<code> /bl fbi.gov</code>\nExample:<code> /bl rm fbi.gov</code>', parse_mode="HTML")
        return

    command = args[0].lower()

    if command == 'list':
        await list_blacklist(update)
    elif command == 'rm' and len(args) > 1:
        await remove_from_blacklist(update, args[1])
    elif '.' in command or command.isnumeric():
        await add_to_blacklist(update, command)
    else:
        await update.message.reply_text('Invalid command or missing argument.')

async def list_blacklist(update: Update) -> None:
    with open('blacklist.json', 'r') as file:
        blacklist = json.load(file)
    if blacklist:
        escaped_blacklist = [html.escape(item) for item in blacklist]
        message = "<code>" + "\n".join(escaped_blacklist) + "</code>"
    else:
        message = "\n".join(blacklist)
    await update.message.reply_text(message, parse_mode="HTML")


async def add_to_blacklist(update: Update, target: str) -> None:
    with open('blacklist.json', 'r+') as file:
        blacklist = json.load(file)
        if target not in blacklist:
            blacklist.append(target)
            file.seek(0)
            json.dump(blacklist, file)
            file.truncate()
            await update.message.reply_text(f"Added <code>{target}</code> to blacklist.", parse_mode="HTML")
        else:
            await update.message.reply_text(f"<code>{target}</code> is already in the blacklist.", parse_mode="HTML")

async def remove_from_blacklist(update: Update, target: str) -> None:
    with open('blacklist.json', 'r+') as file:
        blacklist = json.load(file)
        if target in blacklist:
            blacklist.remove(target)
            file.seek(0)
            json.dump(blacklist, file)
            file.truncate()
            await update.message.reply_text(f"Removed <code>{target}</code> from blacklist.", parse_mode="HTML")
        else:
            await update.message.reply_text(f"<code>{target}</code> is not in the blacklist.", parse_mode="HTML")

async def running_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
  user_id = update.effective_user.id
  now = datetime.utcnow()

  with open('running.json', 'r') as file:
      running_attacks = json.load(file)

  user_attacks = [details for key, details in running_attacks.items() if details["user_id"] == user_id and datetime.fromisoformat(details["end_time"]) > now]


  if not user_attacks:
      await update.message.reply_text("You have no running attacks.")
      return

  message = ""
  for attack in user_attacks:
      time_left = (datetime.fromisoformat(attack["end_time"]) - now).total_seconds()
      message += f"URL: {attack['url']}\nTime left: {int(time_left)}s\nPort: {attack['port']}\nMethod: {attack['method_name']}\n\n+---+----—————-----+---+\n"

  await update.message.reply_text(message)

async def method_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
  user_id = update.effective_user.id
  

  args = context.args
  if not args:
      await update.message.reply_text("Usage:<code> /method [add|list|rm] [parameters]</code>",parse_mode='HTML')
      return

  command = args[0].lower()

  if command == "add":
      if user_id not in ADMIN_IDS:
          return
      await add_method(args[1:], update)
  elif command == "list":
      await list_methods(update)
  elif command == "rm":
      if user_id not in ADMIN_IDS:
          return
      await remove_method(args[1:], update)
  else:
      await update.message.reply_text("Invalid command. Use <code>/method <add|list|rm> [parameters]</code>",parse_mode='HTML')

async def add_method(args, update: Update) -> None:
  if len(args) < 4:
      await update.message.reply_text("Usage:<code> /method [name] [description] [vip] [layer]</code>\nExample: <code> /method HTTPS HTTPS Attack true 7</code>",parse_mode='HTML')
      return

  method_name, description, vip_status, layer_str = args[0], " ".join(args[1:-2]), args[-2].lower(), args[-1]

  if vip_status not in ['true', 'false']:
      await update.message.reply_text(" <code>VIP status must be either 'true' or 'false' </code>", parse_mode='HTML')
      return
  if layer_str not in ['4', '7']:
      await update.message.reply_text("<code>Layer must be either '4' or '7'</code>", parse_mode='HTML')
      return
  vip = vip_status == 'true'
  layer = int(layer_str)

  try:
      with open('methods.json', 'r') as file:
          methods = json.load(file)
  except FileNotFoundError:
      methods = []

  if any(method['name'] == method_name for method in methods):
      await update.message.reply_text(f"Method <code>'{method_name}'</code> already exists.", parse_mode='HTML')
      return

  new_method = {
      "name": method_name,
      "description": description,
      "vip": vip,
      "layer": layer
  }

  methods.append(new_method)
  with open('methods.json', 'w') as file:
      json.dump(methods, file, indent=4)

  await update.message.reply_text(f"Method '{method_name}' added successfully.")

async def list_methods(update: Update) -> None:
  try:
      with open('methods.json', 'r') as file:
          methods = json.load(file)
  except FileNotFoundError:
      await update.message.reply_text("No methods available.")
      return

  if not methods:
      await update.message.reply_text("No methods available.")
      return

  layer4_methods = [method for method in methods if method['layer'] == 4]
  layer7_methods = [method for method in methods if method['layer'] == 7]


  method_list_layer4 = "\n\n".join([f"<code>{method['name']}</code>: {method['description']} (VIP: {method['vip']})" for method in layer4_methods])
  method_list_layer7 = "\n\n".join([f"<code>{method['name']}</code>: {method['description']} (VIP: {method['vip']})" for method in layer7_methods])


  message = f"<b>------Layer 4 Methods------</b>:\n\n{method_list_layer4}\n\n<b>------Layer 7 Methods------</b>:\n\n{method_list_layer7}"

  await update.message.reply_text(message, parse_mode='HTML')

async def remove_method(args, update: Update) -> None:
  if len(args) < 1:
      await update.message.reply_text("Usage:<code> /method rm <name></code>",parse_mode='HTML')
      return

  method_name = args[0]

  try:
      with open('methods.json', 'r') as file:
          methods = json.load(file)
  except FileNotFoundError:
      await update.message.reply_text("No methods file found.")
      return


  method_found = False
  updated_methods = []
  for method in methods:
      if method['name'] == method_name:
          method_found = True
      else:
          updated_methods.append(method)

  if not method_found:
      await update.message.reply_text(f"Method <code>'{method_name}'</code> not found.", parse_mode='HTML')
      return


  with open('methods.json', 'w') as file:
      json.dump(updated_methods, file, indent=4)

  await update.message.reply_text(f"Method <code>'{method_name}'</code> has been removed.", parse_mode='HTML')

async def list_banned(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
  user_id = update.effective_user.id
  if user_id not in ADMIN_IDS:
      return

  try:
      with open('users.json', 'r') as file:
          users = json.load(file)

      banned_users = [f"ID: <code>{uid} </code>" for uid, user in users.items() if user.get('banned', False)]

      if not banned_users:
          await update.message.reply_text("No banned users.")
      else:
          banned_users_text = "\n".join(banned_users)
          await update.message.reply_text(f"{banned_users_text}",parse_mode='HTML')

  except FileNotFoundError:
      await update.message.reply_text("User data file not found.")
  except json.JSONDecodeError:
      await update.message.reply_text("Error reading the user data.")


async def plan(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
  user_id = str(update.effective_user.id)  

  try:
      with open('users.json', 'r') as file:
          users = json.load(file)

      user_plan = users.get(user_id)  
      

      if not user_plan:
          await update.message.reply_text("You do not have an active plan") 
      else:
          expire_datetime = datetime.fromisoformat(user_plan['expire'].replace('Z', '+00:00'))
          formatted_expire = expire_datetime.strftime('%H:%M:%S %d-%m-%Y')
          plan_details = (
              f"<b>Time: </b> <code>{user_plan['time']}s </code>\n"
              f"<b>Concurrent: </b> <code>{user_plan['concurrent']} </code>\n"
              f"<b>VIP: </b> <code>{'Yes' if user_plan['vip'] else 'No'} </code>\n"
              f"<b>Expires: </b> <code>{formatted_expire} </code>\n"
              f"<b>Banned: </b> <code>{'Yes' if user_plan['banned'] else 'No'} </code>\n"
              f"<b>Bypass Blacklist: </b> <code>{'Yes' if user_plan.get('bypass_blacklist', False) else 'No'} </code>\n"
              f"<b>Cooldown: </b> <code>{user_plan['cooldown']}s </code>"
          )
          await update.message.reply_text(f"{plan_details}",parse_mode='HTML')  

  except FileNotFoundError:
      await update.message.reply_text("User data file not found.")  
  except json.JSONDecodeError:
      await update.message.reply_text("Error reading the user data.")  


async def add_user(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    user_id = update.effective_user.id
    if user_id not in ADMIN_IDS:
        return

    if update.message.reply_to_message:
        target_id = str(update.message.reply_to_message.from_user.id)
        args = [target_id] + context.args
    else:
        args = context.args

    if len(args) != 7:
        await update.message.reply_text("Usage: /add [id] [time] [concurrent] [expire_in_days] [vip] [bypass blacklist] [cooldown_in_seconds]\nExample:<code> /add 5145402317 300 5 60 true false 29</code>", parse_mode='HTML')
        return

    user_id, time, concurrent, expire_in_days, vip, bypass_blacklist, cooldown = args
    try:
        time = int(time)
        concurrent = int(concurrent)
        expire_in_days = int(expire_in_days)
        cooldown = int(cooldown)
        vip = True if vip.lower() == 'true' else False
        bypass_blacklist = True if bypass_blacklist.lower() == 'true' else False

        expire_datetime = datetime.utcnow() + timedelta(days=expire_in_days)
        expire_iso = expire_datetime.isoformat()

        with open('users.json', 'r+') as file:
            users = json.load(file)
            users[user_id] = {
                "time": time,
                "concurrent": concurrent,
                "vip": vip,
                "expire": expire_iso,
                "banned": False,
                "bypass_blacklist": bypass_blacklist,
                "cooldown": cooldown,
                "last_attack": None  
            }
            file.seek(0)
            json.dump(users, file, indent=4)
            file.truncate()

        await update.message.reply_text(f"User <code>{user_id}</code> added/updated with | Time={time} | Concurrent={concurrent} | VIP={vip} | Bypass blacklist={bypass_blacklist} | Cooldown={cooldown}s | Expires on {expire_iso}", parse_mode="HTML")

    except ValueError:
        await update.message.reply_text("Error: Ensure that all parameters are correctly formatted.")



def load_plans():
    with open('plan.json', 'r') as file:
        return json.load(file)['plans']


async def buy(update: Update, context: CallbackContext) -> None:
    plans = load_plans()
    current_plan = plans[0]  


    message = (
        "🛒 <b>Shop</b>\n\n"
        "Welcome to our shop\n\n"
        "Please choose an option below 👇"
    )

    keyboard = [
        [
            InlineKeyboardButton("<<", callback_data=str(plans[-1]['id'])),
            InlineKeyboardButton(f"0/{len(plans)}", callback_data="current"),
            InlineKeyboardButton(">>", callback_data=str(plans[0]['id']))
        ]
    ]

    reply_markup = InlineKeyboardMarkup(keyboard)
    await update.message.reply_text(message, reply_markup=reply_markup, parse_mode="HTML")

async def plan_callback(update: Update, context: CallbackContext) -> None:
    query = update.callback_query
    if not query:
        return
    await query.answer()
    plans = load_plans()
    plan_id = int(query.data)

    current_index = next((index for index, plan in enumerate(plans) if plan['id'] == plan_id), None)
    next_index = (current_index + 1) % len(plans)
    prev_index = (current_index - 1) % len(plans)


    keyboard = [
        [
            InlineKeyboardButton("<<", callback_data=str(plans[prev_index]['id'])),
            InlineKeyboardButton(f"{current_index + 1}/{len(plans)}", callback_data=str(current_index)),
            InlineKeyboardButton(">>", callback_data=str(plans[next_index]['id']))
        ],
        [
            InlineKeyboardButton("💬 Buy this Plan", url="https://t.me/Royaloakap_bot")
        ]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    details = plans[current_index]['details']

    expire_date = datetime.utcnow() + timedelta(days=int(details['Expires']))
    formatted_expire_date = expire_date.strftime("%d/%m/%Y")

    message = (
        f"🛒 <b>Shop</b>\n\n"
        f"🎖 <b>Plan:</b> <code>{plans[current_index]['name']}</code>\n\n"
        f"⌛ <b>Time:</b> <code>{details['Time']}</code>\n\n"
        f"💥 <b>Concurrent:</b> <code>{details['Concurrent']}</code>\n\n"
        f"⚜️ <b>VIP:</b> {details['VIP']}\n\n"
        f"📅 <b>Expiration:</b> <code>{details['Expires']} days</code><b> ({formatted_expire_date})</b>\n\n"
        f"🔓 <b>Bypass Blacklist:</b> {details['Bypass Blacklist']}\n\n"
        f"⏰ <b>Cooldown:</b> <code>{details['Cooldown']}</code>\n\n"
        f"💵 <b>Price:</b> <code>${details['Price']}</code>"
    )

    await query.edit_message_text(text=message, reply_markup=reply_markup,parse_mode="HTML")


async def handle_callback(update: Update, context: CallbackContext) -> None:
    query = update.callback_query
    await query.answer()

    data = query.data

    if data.startswith("store_"):
        await handle_store_callback(update, context)
    elif data.isdigit():
        await plan_callback(update, context)

async def handle_store_callback(update: Update, context: CallbackContext) -> None:
    query = update.callback_query
    store_data = context.user_data.get('store_data', DEFAULT_STORE_DATA.copy())

    if query.data == "store_increase_time":
        store_data['time'] += CONFIG['time']['step']
        store_data['price'] += CONFIG['time']['price_per_unit']
    elif query.data == "store_decrease_time":
        if store_data['time'] > CONFIG['time']['step']:
            store_data['time'] -= CONFIG['time']['step']
            store_data['price'] -= CONFIG['time']['price_per_unit'] 
    elif query.data == "store_increase_concurrent":
        store_data['concurrent'] += CONFIG['concurrent']['step']
        store_data['price'] += CONFIG['concurrent']['price_per_unit'] 
    elif query.data == "store_decrease_concurrent":
        if store_data['concurrent'] > CONFIG['concurrent']['step']:
            store_data['concurrent'] -= CONFIG['concurrent']['step']
            store_data['price'] -= CONFIG['concurrent']['price_per_unit']
    elif query.data == "store_increase_expire":
        store_data['expire'] += CONFIG['expire']['step']
        store_data['price'] += CONFIG['expire']['price_per_unit'] 
    elif query.data == "store_decrease_expire":
        if store_data['expire'] > CONFIG['expire']['step']:
            store_data['expire'] -= CONFIG['expire']['step']
            store_data['price'] -= CONFIG['expire']['price_per_unit'] 
    elif query.data == "store_toggle_vip":
        store_data['vip'] = not store_data['vip']
        store_data['price'] += CONFIG['vip']['price'] if store_data['vip'] else -CONFIG['vip']['price']
    elif query.data == "store_toggle_bypass_blacklist":
        store_data['bypass_blacklist'] = not store_data['bypass_blacklist']
        store_data['price'] += CONFIG['bypass_blacklist']['price'] if store_data['bypass_blacklist'] else -CONFIG['bypass_blacklist']['price']

    context.user_data['store_data'] = store_data

    await store(update, context)


def generate_store_message(data):
    expire_date = datetime.utcnow() + timedelta(days=data['expire'])
    formatted_expire_date = expire_date.strftime("%d/%m/%Y")
    message = (
        f"🛒 <b>Store</b>\n\n"
        f"⌛ <b>Time:</b> <code>{data['time']}s</code>\n"
        f"💥 <b>Concurrent:</b> <code>{data['concurrent']}</code>\n"
        f"⚜️ <b>VIP:</b> {data['vip']}\n"
        f"🔓 <b>Bypass Blacklist:</b> {data['bypass_blacklist']}\n"
        f"📅 <b>Expire:</b> <code>{data['expire']} days</code> ({formatted_expire_date})\n\n"
        f"💵 <b>Price:</b> <code>${data['price']}</code>\n\n"
    )
    return message


async def store(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    query = update.callback_query
    if query:
        await query.answer()

    store_data = context.user_data.get('store_data', DEFAULT_STORE_DATA.copy())
    message = generate_store_message(store_data)

    keyboard = [
        [
            InlineKeyboardButton("-", callback_data="store_decrease_time"),
            InlineKeyboardButton("Time", callback_data="store_time"),
            InlineKeyboardButton("+", callback_data="store_increase_time")
        ],
        [
            InlineKeyboardButton("-", callback_data="store_decrease_concurrent"),
            InlineKeyboardButton("Concurrent", callback_data="store_concurrent"),
            InlineKeyboardButton("+", callback_data="store_increase_concurrent")
        ],
        [
            InlineKeyboardButton("-", callback_data="store_decrease_expire"),
            InlineKeyboardButton("Expire", callback_data="store_expire"),
            InlineKeyboardButton("+", callback_data="store_increase_expire")
        ],
        [
            InlineKeyboardButton("Toggle VIP", callback_data="store_toggle_vip")
        ],
        [
            InlineKeyboardButton("Toggle Bypass Blacklist", callback_data="store_toggle_bypass_blacklist")
        ]
    ]

    reply_markup = InlineKeyboardMarkup(keyboard)

    if query:
        await query.edit_message_text(text=message, reply_markup=reply_markup, parse_mode="HTML")
    else:
        await update.message.reply_text(message, reply_markup=reply_markup, parse_mode="HTML")

async def promote_vip_users(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    user_id = update.effective_user.id
    if user_id not in ADMIN_IDS:
        await update.message.reply_text("You do not have permission to use this command.")
        return

    try:
        with open('users.json', 'r') as file:
            users = json.load(file)

        chat_id = update.effective_chat.id
        current_time = datetime.utcnow()

        for user_id, user_info in users.items():
            target_id = int(user_id)
            is_vip = user_info.get('vip', False)
            expire_date = datetime.fromisoformat(user_info['expire'])

            if is_vip and expire_date > current_time:
                await context.bot.promote_chat_member(
                    chat_id=chat_id,
                    user_id=target_id,
                    is_anonymous=False,
                    can_manage_chat=False,
                    can_post_messages=False,
                    can_edit_messages=False,
                    can_delete_messages=False,
                    can_restrict_members=False,
                    can_promote_members=False,
                    can_change_info=False,
                    can_invite_users=True,
                    can_pin_messages=False,
                    can_manage_topics=False
                )

                await context.bot.set_chat_administrator_custom_title(
                    chat_id=chat_id,
                    user_id=target_id,
                    custom_title="ᴠɪᴘ ᴜsᴇʀ ✓"
                )
                await update.message.reply_text(f"UPDATE TITLE => <code>{target_id}</code>", parse_mode='HTML')

            elif not is_vip or expire_date <= current_time:
                member = await context.bot.get_chat_member(chat_id, target_id)
                if member.status == ChatMemberStatus.ADMINISTRATOR:
                    await context.bot.promote_chat_member(
                        chat_id=chat_id,
                        user_id=target_id,
                        can_manage_chat=False,
                        can_post_messages=False,
                        can_edit_messages=False,
                        can_delete_messages=False,
                        can_restrict_members=False,
                        can_promote_members=False,
                        can_change_info=False,
                        can_invite_users=False,
                        can_pin_messages=False,
                        can_manage_topics=False
                    )
                    await update.message.reply_text(f"REMOVE TITLE => <code>{target_id}</code>", parse_mode='HTML')

    except Exception as e:
        await update.message.reply_text(f"Error UPDATE/REMOVE VIP TITLE: {e}")

