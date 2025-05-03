# Stresser-Telegram AutoBuy

## 📌 Project Presentation

Telegram AutoBuy is a Telegram bot developed in Go for managing a stress testing service with an automated purchase system. This bot provides a complete interface for launching DDoS attacks, managing users and subscriptions through integration with the Sellix payment platform.

## 🛠️ Main Features

- **Payment System**: Integration with Sellix for automated purchases
- **User Management**: SQLite database for storing user information
- **Subscription Plans**: Different access levels with specific limitations
- **Token System**: Generation and validation of tokens to activate subscriptions
- **Blacklist**: Protection of certain domains against attacks
- **Admin Interface**: Special commands for administrators

## 📋 Project Structure

BOT/TELEGRAM/AUTOBUY/
├── bot/ # Telegram bot logic
│ ├── Bot.go # Bot entry point
│ └── Listen.go # Command and event handling
├── config/ # Configuration files
│ ├── config.json # Main configuration (token, API, etc.)
│ └── plans.json # Subscription plans configuration
├── database/ # Database management
│ ├── Connection.go # SQLite database connection
│ ├── Utils.go # Utility functions
│ └── Variables.go # Global variables
├── structs/ # Data structures
│ ├── Attack.go # Attack structure
│ ├── Config.go # Configuration structure
│ ├── Method.go # Attack method structure
│ ├── Plan.go # Subscription plan structure
│ ├── SellixPayload.go # Sellix API structures
│ ├── Token.go # Token structure
│ └── User.go # User structure
├── go.mod # Go dependencies
├── go.sum # Dependency checksums
└── main.go # Application entry point

## 🚀 Installation and Configuration

### Prerequisites

- Go 1.19+
- SQLite
- A Telegram bot token (obtained via [@BotFather](https://t.me/BotFather))
- A Sellix account for payments

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/Stresser-Telegram-AutoBuy.git
   cd Stresser-Telegram-AutoBuy
   ```

2. Install dependencies:
   ```bash
   go mod download
   ```

3. Configure the `config/config.json` file:
   ```json
   {
     "Token": "your_telegram_token",
     "SellixKey": "your_sellix_api_key",
     "URL": "https://your-webhook-url.com/",
     "Admins": ["your_telegram_id"],
     "Currency": "EUR",
     "Blacklist": ["gov"],
     "Methods": [
       {
         "Name": "TCP",
         "Description": "TCP Flood",
         "Plans": ["Basic", "Premium", "Enterprise"],
         "APIs": ["https://api.example.com/attack?host=[host]&port=[port]&time=[time]"]
       }
     ]
   }
   ```

4. Configure the `config/plans.json` file:
   ```json
   {
     "Plans": [
       {
         "Name": "Basic",
         "Description": "1 GB/s Power",
         "Price": 5.0,
         "MaxTime": 60,
         "MaxCons": 1,
         "Rank": 3
       },
       {
         "Name": "Advanced",
         "Description": "2 GB/s Power",
         "Price": 10.0,
         "MaxTime": 120,
         "MaxCons": 2,
         "Rank": 2
       },
       {
         "Name": "Pro",
         "Description": "8 GB/s Power",
         "Price": 20.0,
         "MaxTime": 600,
         "MaxCons": 4,
         "Rank": 1
       }
     ]
   }
   ```

### Compilation and Execution