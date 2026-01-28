# Evolution API Setup Guide

Panduan setup Evolution API untuk WhatsApp IT integration.

## Apa itu Evolution API?

Evolution API adalah REST API untuk WhatsApp yang menggunakan Baileys library. API ini memungkinkan:
- Koneksi WhatsApp Web multi-device
- Send/receive messages
- Media handling (image, video, audio, document)
- Webhook untuk real-time events
- Multi-instance (banyak nomor WhatsApp)

## Setup Local Development (MAMP)

### 1. Prerequisites

- Node.js 16+ (download dari https://nodejs.org)
- Git

### 2. Install Evolution API

```bash
# Clone repository
cd /Applications/MAMP/htdocs
git clone https://github.com/EvolutionAPI/evolution-api.git
cd evolution-api

# Install dependencies
npm install
```

### 3. Konfigurasi

Buat file `.env` di folder `evolution-api`:

```bash
cp .env.example .env
nano .env
```

Isi minimal configuration:

```env
# Server
SERVER_TYPE=http
SERVER_PORT=8080
SERVER_URL=http://localhost:8080

# Cors
CORS_ORIGIN=*
CORS_METHODS=GET,POST,PUT,DELETE
CORS_CREDENTIALS=true

# API Key (WAJIB GANTI!)
AUTHENTICATION_API_KEY=whatsappit-local-dev-key-12345678

# Database (SQLite untuk development)
DATABASE_ENABLED=true
DATABASE_PROVIDER=sqlite
DATABASE_CONNECTION_FILE=./evolution.db

# Websocket
WEBSOCKET_ENABLED=true

# Webhook
WEBHOOK_GLOBAL_ENABLED=true

# Log
LOG_LEVEL=ERROR
LOG_COLOR=true

# Storage
STORE_MESSAGES=true
STORE_CONTACTS=true
STORE_CHATS=true

# QR Code
QRCODE_LIMIT=30
```

### 4. Build & Start

```bash
# Build (jika TypeScript)
npm run build

# Start server
npm start

# Atau untuk development dengan auto-reload:
npm run dev
```

Server akan running di `http://localhost:8080`

### 5. Test Connection

```bash
curl -X GET http://localhost:8080 \
  -H "apikey: whatsappit-local-dev-key-12345678"
```

Response sukses:
```json
{
  "status": 200,
  "message": "Welcome to Evolution API"
}
```

## Update WhatsApp IT Configuration

Edit file `/Applications/MAMP/htdocs/whatsappit/.env`:

```env
EVOLUTION_API_URL=http://localhost:8080
EVOLUTION_API_KEY=whatsappit-local-dev-key-12345678
EVOLUTION_WEBHOOK_URL=http://localhost/api/webhooks/evolution
```

**PENTING:** `EVOLUTION_API_KEY` harus sama dengan `AUTHENTICATION_API_KEY` di Evolution API!

## Testing Integration

### 1. Start Evolution API

```bash
cd /Applications/MAMP/htdocs/evolution-api
npm start
```

### 2. Start Laravel (MAMP)

Pastikan MAMP sudah running di port 80.

### 3. Test Create WhatsApp Account

1. Buka browser: `http://localhost/login`
2. Login dengan credentials default
3. Klik "WhatsApp Accounts" → "Create New"
4. Isi form:
   - Phone Number: `628123456789` (nomor WhatsApp yang akan digunakan)
   - Name: `Testing Account`
5. Klik "Create Account"

### 4. Scan QR Code

Setelah create account, akan redirect ke halaman connect dengan QR code:
1. QR code akan muncul dari Evolution API
2. Buka WhatsApp di HP
3. Tap menu (⋮) → Linked Devices → Link a Device
4. Scan QR code yang muncul
5. Status akan otomatis berubah menjadi "Connected"

### 5. Test Send Message

```bash
curl -X POST http://localhost/api/send \
  -H "Content-Type: application/json" \
  -d '{
    "sender_key": "sk_xxxxx",
    "sender_secret": "ss_xxxxx",
    "to": "628123456789",
    "message": "Test message from local development!"
  }'
```

Ganti `sender_key` dan `sender_secret` dengan yang ada di account details.

## Troubleshooting

### Port 8080 sudah digunakan

Edit `.env` Evolution API, ganti port:

```env
SERVER_PORT=8081
```

Dan update di Laravel `.env`:

```env
EVOLUTION_API_URL=http://localhost:8081
```

### QR Code tidak muncul

1. Check Evolution API running: `curl http://localhost:8080`
2. Check logs Evolution API di terminal
3. Check API key sama di kedua aplikasi
4. Clear browser cache

### Connection failed

1. Check Evolution API logs di terminal
2. Pastikan Node.js versi 16+: `node --version`
3. Delete `evolution.db` dan restart Evolution API
4. Check firewall tidak block port 8080

### Webhook tidak terima data

1. Check webhook URL di `.env`: `EVOLUTION_WEBHOOK_URL`
2. Untuk local dev, webhook mungkin tidak work (perlu ngrok atau expose)
3. Check Laravel logs: `tail -f storage/logs/laravel.log`

### Instance tidak terbuat

1. Check Evolution API logs
2. Check database `evolution.db` exists dan writable
3. Restart Evolution API: Ctrl+C dan `npm start` lagi

## Production Tips

Untuk production di VPS:

### 1. Gunakan PM2

```bash
npm install -g pm2
pm2 start dist/src/main.js --name evolution-api
pm2 save
pm2 startup
```

### 2. Gunakan PostgreSQL/MySQL

Edit `.env`:

```env
DATABASE_PROVIDER=postgresql
DATABASE_CONNECTION_URI=postgresql://user:password@localhost:5432/evolution
```

### 3. Setup Nginx Reverse Proxy

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    
    location / {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

### 4. SSL dengan Let's Encrypt

```bash
sudo certbot --nginx -d api.yourdomain.com
```

Update WhatsApp IT `.env`:

```env
EVOLUTION_API_URL=https://api.yourdomain.com
```

## Evolution API Endpoints

Dokumentasi lengkap: https://doc.evolution-api.com

### Common Endpoints

**Create Instance:**
```bash
POST /instance/create
Headers: { "apikey": "your-key" }
Body: { "instanceName": "wa_1", "qrcode": true }
```

**Get QR Code:**
```bash
GET /instance/connect/{instanceName}
Headers: { "apikey": "your-key" }
```

**Send Text:**
```bash
POST /message/sendText/{instanceName}
Headers: { "apikey": "your-key" }
Body: { "number": "628xxx@s.whatsapp.net", "text": "Hello" }
```

**Send Media:**
```bash
POST /message/sendMedia/{instanceName}
Headers: { "apikey": "your-key" }
Body: {
  "number": "628xxx@s.whatsapp.net",
  "mediatype": "image",
  "media": "https://url-to-image.jpg",
  "caption": "Image caption"
}
```

**Check Status:**
```bash
GET /instance/connectionState/{instanceName}
Headers: { "apikey": "your-key" }
```

**Logout:**
```bash
DELETE /instance/logout/{instanceName}
Headers: { "apikey": "your-key" }
```

**Delete Instance:**
```bash
DELETE /instance/delete/{instanceName}
Headers: { "apikey": "your-key" }
```

## Webhook Events

Evolution API akan mengirim webhook ke `EVOLUTION_WEBHOOK_URL` untuk events:

### connection.update
```json
{
  "event": "connection.update",
  "instance": "wa_1",
  "data": {
    "state": "open"
  }
}
```

### qrcode.updated
```json
{
  "event": "qrcode.updated",
  "instance": "wa_1",
  "data": {
    "qrcode": "base64-qr-code-data"
  }
}
```

### messages.upsert
```json
{
  "event": "messages.upsert",
  "instance": "wa_1",
  "data": {
    "key": { ... },
    "message": { ... }
  }
}
```

## Resources

- **Official Documentation:** https://doc.evolution-api.com
- **GitHub Repository:** https://github.com/EvolutionAPI/evolution-api
- **Postman Collection:** https://doc.evolution-api.com/v2/en/get-started/postman

## Support

Jika ada masalah:
1. Check logs Evolution API di terminal
2. Check Laravel logs: `storage/logs/laravel.log`
3. Check Evolution API GitHub issues
4. Check Evolution API documentation

---

**Happy Coding!** 🚀
