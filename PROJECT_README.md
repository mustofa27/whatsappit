# WhatsApp Sender Service 📱

Layanan sender WhatsApp berbasis Laravel yang memungkinkan user untuk menambahkan nomor WhatsApp mereka sebagai sender dan mengirim pesan melalui HTTP POST API.

## ✨ Fitur

- 🔐 Multi-user authentication dengan Laravel Sanctum
- 📱 Multi-account WhatsApp per user
- 🔄 Initialize account dengan QR Code scanning
- 💬 Kirim pesan text dan media (image, video, document, audio)
- 📊 Tracking status pesan real-time (pending, sent, delivered, read, failed)
- 📜 Riwayat pesan lengkap dengan pagination
- 🎯 RESTful API yang mudah digunakan
- 🔒 Secure session management

## 🛠️ Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum
- **Language**: PHP 8.2+

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (untuk assets)
- MAMP/XAMPP/Laragon atau web server lainnya

## 🚀 Installation

### 1. Clone atau setup project

```bash
cd /Applications/MAMP/htdocs/whatsappit
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whatsappit
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Install Laravel Sanctum (untuk API authentication)

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 7. Build assets

```bash
npm run build
```

### 8. Start development server

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## 📖 API Documentation

Dokumentasi lengkap API tersedia di [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

### Quick Start API

#### 1. Register/Login untuk mendapatkan token

Gunakan Laravel Sanctum untuk authentication. Buat endpoint login atau gunakan default Laravel auth.

#### 2. Add WhatsApp Account

```bash
POST /api/whatsapp/accounts
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "phone_number": "628123456789",
  "name": "My Business Account"
}
```

#### 3. Initialize Account (Get QR Code)

```bash
POST /api/whatsapp/accounts/{id}/initialize
Authorization: Bearer {your-token}
```

Response akan berisi QR code yang harus di-scan menggunakan WhatsApp mobile app.

#### 4. Send Message

```bash
POST /api/whatsapp/messages/send
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "whatsapp_account_id": 1,
  "recipient_number": "628987654321",
  "message": "Hello from WhatsApp Sender!"
}
```

## 🗂️ Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── WhatsappController.php    # API endpoints handler
├── Models/
│   ├── WhatsappAccount.php          # Model untuk akun WhatsApp
│   └── WhatsappMessage.php          # Model untuk pesan
└── Services/
    └── WhatsappService.php          # Business logic untuk WhatsApp

database/
└── migrations/
    ├── create_whatsapp_accounts_table.php
    └── create_whatsapp_messages_table.php

routes/
└── api.php                          # API routes definition
```

## 🔌 WhatsApp Integration

Project ini sudah menyediakan struktur untuk integrasi WhatsApp. Anda perlu memilih dan mengimplementasikan salah satu provider berikut:

### Recommended Providers:

1. **[Baileys](https://github.com/WhiskeySockets/Baileys)** (Node.js)
   - Free & Open Source
   - WhatsApp Web Multi-Device
   - Paling populer

2. **[Evolution API](https://github.com/EvolutionAPI/evolution-api)** (Node.js)
   - Wrapper untuk Baileys dengan HTTP API
   - Mudah diintegrasikan dengan Laravel

3. **[WAHA - WhatsApp HTTP API](https://github.com/devlikeapro/waha)**
   - Docker-ready
   - REST API

4. **WhatsApp Business API** (Official, berbayar)
   - Enterprise solution
   - Paling reliable

### Implementation Steps:

1. Setup WhatsApp provider pilihan Anda (misal: Evolution API)
2. Edit file `app/Services/WhatsappService.php`
3. Implement method-method yang sudah disediakan:
   - `initialize()` - Generate QR dan inisialisasi session
   - `sendMessage()` - Kirim pesan
   - `disconnect()` - Disconnect session
   - `handleWebhook()` - Handle status updates

## 🔒 Security Notes

1. **Environment Variables**: Jangan commit file `.env` ke repository
2. **HTTPS**: Gunakan HTTPS untuk production
3. **Rate Limiting**: Implementasikan rate limiting di routes
4. **Session Encryption**: Session WhatsApp di-encrypt di database
5. **API Token**: Simpan API token dengan aman

## 📊 Database Schema

### whatsapp_accounts
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke users |
| phone_number | string | Nomor WhatsApp (unique) |
| name | string | Nama akun |
| status | enum | pending/connected/disconnected/failed |
| qr_code | text | QR code data |
| session_data | text | WhatsApp session (encrypted) |
| last_connected_at | timestamp | Waktu terakhir connect |

### whatsapp_messages
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| whatsapp_account_id | bigint | Foreign key |
| recipient_number | string | Nomor penerima |
| message | text | Isi pesan |
| status | enum | pending/sent/delivered/read/failed |
| media_url | text | URL media (optional) |
| media_type | string | image/video/document/audio |
| error_message | text | Error message jika gagal |
| sent_at | timestamp | Waktu terkirim |

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter WhatsappTest
```

## 🚀 Deployment

### Production Checklist:

- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Configure proper database credentials
- [ ] Setup queue worker untuk pengiriman pesan
- [ ] Setup supervisor untuk queue worker
- [ ] Configure webhook URL untuk status updates
- [ ] Setup SSL certificate (HTTPS)
- [ ] Implement caching (Redis recommended)
- [ ] Setup backup database
- [ ] Configure logging & monitoring

### Queue Worker Setup (Recommended for Production)

```bash
# Edit WhatsappService untuk use Queue
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## 🛣️ Roadmap

- [x] Basic account management
- [x] Message sending (text & media)
- [x] Message history
- [ ] Implement queue for message sending
- [ ] Webhook handler untuk real-time updates
- [ ] Bulk message sending
- [ ] Template messages
- [ ] Group message support
- [ ] Scheduled messages
- [ ] Analytics dashboard
- [ ] Message templates management
- [ ] Contact list management
- [ ] Auto-reply functionality

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 💬 Support

Jika ada pertanyaan atau issue, silakan buat issue di repository ini.

---

Made with ❤️ using Laravel
