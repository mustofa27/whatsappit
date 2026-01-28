# WhatsApp IT - Deployment Guide untuk VPS

Panduan lengkap deployment WhatsApp IT di VPS dengan Nginx, Evolution API, dan PM2.

## Arsitektur Sistem

```
Internet
    │
    ├─> Nginx (Port 80/443)
    │       ├─> Laravel (PHP-FPM) - WhatsApp IT App
    │       └─> Evolution API (Port 8080) - WhatsApp Integration
    │
    └─> Evolution API (PM2) - Node.js Service
```

## Prasyarat

- Ubuntu 20.04/22.04 atau Debian 11/12
- Root atau sudo access
- Domain (opsional, bisa gunakan IP)
- Node.js 16+ (untuk Evolution API)
- PHP 8.3+ dengan PHP-FPM
- MySQL/MariaDB 10.5+
- Nginx
- PM2 (Node.js process manager)
- Git

## 1. Persiapan Server

### Update sistem

```bash
sudo apt update && sudo apt upgrade -y
```

### Install dependencies dasar

```bash
sudo apt install -y curl git unzip software-properties-common
```

## 2. Install Node.js & PM2

### Install Node.js 18 LTS

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### Install PM2

```bash
sudo npm install -g pm2
```

### Setup PM2 startup

```bash
pm2 startup
# Jalankan command yang muncul (biasanya sudo env PATH=$PATH:...)
```

## 3. Install PHP 8.3

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl \
    php8.3-xml php8.3-bcmath php8.3-intl
```

### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

## 4. Install MySQL/MariaDB

```bash
sudo apt install -y mysql-server

# Secure installation
sudo mysql_secure_installation
```

### Buat database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE whatsappit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'whatsappit'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON whatsappit.* TO 'whatsappit'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Install Nginx

```bash
sudo apt install -y nginx
```

## 6. Setup Evolution API

### Clone Evolution API

```bash
cd /var/www
sudo git clone https://github.com/EvolutionAPI/evolution-api.git evolution-api
cd evolution-api
```

### Install dependencies

```bash
sudo npm install
```

### Buat file konfigurasi .env

```bash
sudo nano .env
```

Isi dengan:

```env
# Server Config
SERVER_TYPE=http
SERVER_PORT=8080
SERVER_URL=http://your-domain.com

# Cors
CORS_ORIGIN=*
CORS_METHODS=GET,POST,PUT,DELETE
CORS_CREDENTIALS=true

# API Key (WAJIB GANTI!)
AUTHENTICATION_API_KEY=ganti-dengan-api-key-yang-kuat-minimal-32-karakter

# Database (SQLite - simple)
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

# Clean store
CLEAN_STORE_CLEANING_INTERVAL=7200
CLEAN_STORE_MESSAGES=true
CLEAN_STORE_MESSAGE_UP_TO=false
CLEAN_STORE_CONTACTS=true
CLEAN_STORE_CHATS=true

# QR Code
QRCODE_LIMIT=30

# Instance
DEL_INSTANCE=false
```

### Build (jika ada TypeScript)

```bash
sudo npm run build
```

### Start dengan PM2

```bash
sudo pm2 start dist/src/main.js --name evolution-api
sudo pm2 save
```

### Check status

```bash
sudo pm2 status
sudo pm2 logs evolution-api
```

## 7. Deploy Laravel App

### Clone repository atau upload files

```bash
cd /var/www
sudo mkdir -p whatsappit
sudo chown -R $USER:$USER whatsappit
cd whatsappit

# Upload files via git atau scp
# git clone repository-anda .
```

### Install dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Setup environment

```bash
cp .env.example .env
nano .env
```

Update konfigurasi:

```env
APP_NAME="WhatsApp IT"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whatsappit
DB_USERNAME=whatsappit
DB_PASSWORD=password_yang_kuat

# Evolution API
EVOLUTION_API_URL=http://localhost:8080
EVOLUTION_API_KEY=ganti-dengan-api-key-yang-sama-di-evolution
EVOLUTION_WEBHOOK_URL="${APP_URL}/api/webhooks/evolution"
```

### Generate key & migrate

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### Set permissions

```bash
sudo chown -R www-data:www-data /var/www/whatsappit
sudo chmod -R 755 /var/www/whatsappit
sudo chmod -R 775 /var/www/whatsappit/storage
sudo chmod -R 775 /var/www/whatsappit/bootstrap/cache
```

### Build assets (jika perlu)

```bash
npm install
npm run build
```

## 8. Konfigurasi Nginx

### Buat config file

```bash
sudo nano /etc/nginx/sites-available/whatsappit
```

Isi dengan:

```nginx
# Laravel App
server {
    listen 80;
    listen [::]:80;
    
    server_name your-domain.com www.your-domain.com;
    root /var/www/whatsappit/public;
    
    index index.php index.html;
    
    # Logging
    access_log /var/log/nginx/whatsappit-access.log;
    error_log /var/log/nginx/whatsappit-error.log;
    
    # Client max body size (untuk upload gambar)
    client_max_body_size 10M;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Evolution API Reverse Proxy
server {
    listen 80;
    listen [::]:80;
    
    server_name api.your-domain.com;
    
    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Enable site

```bash
sudo ln -s /etc/nginx/sites-available/whatsappit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 9. SSL dengan Let's Encrypt (Opsional tapi Recommended)

### Install Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Dapatkan sertifikat

```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com -d api.your-domain.com
```

### Auto-renewal

Certbot sudah setup auto-renewal via systemd timer. Check dengan:

```bash
sudo systemctl status certbot.timer
```

## 10. Optimisasi & Keamanan

### Firewall (UFW)

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### PHP-FPM Optimization

Edit `/etc/php/8.3/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.3-fpm
```

### Laravel Optimization

```bash
cd /var/www/whatsappit
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 11. Monitoring & Maintenance

### PM2 Monitoring

```bash
pm2 monit
pm2 logs evolution-api --lines 100
```

### Laravel Logs

```bash
tail -f /var/www/whatsappit/storage/logs/laravel.log
```

### Nginx Logs

```bash
tail -f /var/log/nginx/whatsappit-error.log
```

### Update Aplikasi

```bash
cd /var/www/whatsappit
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Restart Services

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
pm2 restart evolution-api
```

## 12. Testing

### Test Evolution API

```bash
curl -X GET http://localhost:8080 \
  -H "apikey: your-api-key-here"
```

### Test Laravel App

```bash
curl http://your-domain.com
```

### Test WhatsApp Connection

1. Buka browser: `http://your-domain.com`
2. Login dengan default credentials dari seeder
3. Buat WhatsApp Account baru
4. Scan QR code dengan WhatsApp
5. Cek koneksi status

### Test API Send Message

```bash
curl -X POST http://your-domain.com/api/send \
  -H "Content-Type: application/json" \
  -d '{
    "sender_key": "sk_xxxxxxxxxxxxx",
    "sender_secret": "ss_xxxxxxxxxxxxx",
    "to": "6281234567890",
    "message": "Test message from API"
  }'
```

## Troubleshooting

### Evolution API tidak start

```bash
pm2 logs evolution-api --lines 50
# Check error di logs
```

### Permission errors Laravel

```bash
sudo chown -R www-data:www-data /var/www/whatsappit
sudo chmod -R 755 /var/www/whatsappit
sudo chmod -R 775 /var/www/whatsappit/storage
sudo chmod -R 775 /var/www/whatsappit/bootstrap/cache
```

### QR Code tidak muncul

1. Check Evolution API running: `pm2 status`
2. Check `.env` EVOLUTION_API_KEY sama di Laravel dan Evolution API
3. Check firewall tidak block port 8080
4. Check logs: `pm2 logs evolution-api`

### Nginx 502 Bad Gateway

```bash
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm
```

### Database connection error

```bash
# Check MySQL running
sudo systemctl status mysql

# Check credentials di .env
nano /var/www/whatsappit/.env
```

## Keamanan Tambahan

1. **Ganti semua password default**
   - Database user password
   - Evolution API key
   - Admin panel passwords

2. **Setup fail2ban**
   ```bash
   sudo apt install fail2ban
   ```

3. **Disable PHP info exposure**
   Edit `php.ini`: `expose_php = Off`

4. **Regular updates**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

5. **Backup regular**
   - Database: `mysqldump`
   - Files: `rsync` atau `tar`
   - Evolution API data

## Support

Untuk bantuan atau pertanyaan, silakan:
- Check logs di `/var/log/nginx/` dan `storage/logs/`
- PM2 logs: `pm2 logs evolution-api`
- Laravel logs: `tail -f storage/logs/laravel.log`

---

**Selamat! WhatsApp IT sudah running di VPS Anda** 🎉
