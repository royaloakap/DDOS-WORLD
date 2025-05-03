# Stresser-Telegram

## 📌 Project Presentation

Stresser-Telegram is a Telegram bot designed to manage stress tests on web servers. This bot allows users to perform controlled DDoS attacks through a Telegram interface, with different access levels and features based on subscription plans.

## 🛠️ Main Features

- **Attack System**: Launch DDoS attacks with different methods (Layer 4 and Layer 7)
- **User Management**: Complete system with plans, subscriptions and privileges
- **VIP System**: Access to exclusive attack methods
- **Blacklist**: Protection of certain domains against attacks
- **Promo Codes**: Promotional code system for users
- **Monitoring**: Tracking of ongoing attacks and history
- **Admin Interface**: Complete bot management by administrators

## 📋 Project Structure
BOT/Stresser-Telegram/
├── blacklist.json # Protected domains list
├── config.json # Bot configuration (tokens, API, etc.)
├── config_store.json # Store configuration
├── functions.py # Bot utility functions
├── main.py # Main bot entry point
├── methods.json # Available attack methods
├── plan.json # Available subscription plans
├── redeem_code.json # Promotional codes
├── running.json # Ongoing attacks
└── users.json # User database


## 🚀 Installation and Configuration

### Prerequisites

- Python 3.7+
- pip (Python package manager)
- A Telegram bot token (obtained via [@BotFather](https://t.me/BotFather))
- An API key for the stress testing service

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/Stresser-Telegram.git
   cd Stresser-Telegram
   ```

2. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```

3. Configure the `config.json` file:
   ```json
   {
       "BOT_TOKEN": "your_telegram_token",
       "licensekey": "your_license_key",
       "Api_Link": "https://api-service.com/api/",
       "Api_Username": "your_username",
       "Api_Passwd": "your_password",
       "ADMIN_IDS": [your_telegram_id],
       "bot_token": "your_telegram_token",
       "chat_id": "chat_id_for_logs"
   }
   ```

## 🔧 Usage

### Starting the Bot
python main.py


### Main Commands

- `/stresser` - Start the bot
- `/attack [target] [port] [time] [method]` - Launch an attack
- `/running` - View ongoing attacks
- `/method list` - Show available methods
- `/plan` - View your current plan
- `/buy` - Buy a plan
- `/store` - Access the store
- `/redeem use [code]` - Use a promotional code
- `/helpme` - Show help

### Admin Commands

- `/add [id] [time] [concurrent] [expire] [vip] [bypass] [cooldown]` - Add a user
- `/ban [id]` - Ban a user
- `/unban [id]` - Unban a user
- `/bl [list|rm target|target]` - Manage blacklist
- `/method [add|list|rm]` - Manage attack methods
- `/listban` - List banned users
- `/set` - Promote users to VIP
- `/redeem [add|list|rm|use]` - Manage promotional codes

## ⚠️ Warning

This bot is designed solely for educational purposes and to test the resilience of your own servers. Using this bot to attack servers without explicit authorization is illegal and unethical. The authors are not responsible for the misuse of this software.

## 📜 License

This project is protected by a proprietary license. Any use, modification or distribution without authorization is strictly prohibited.

## 👨‍💻 Developer

Developed by Royaloakap - Discord: discord.gg/BOTFR