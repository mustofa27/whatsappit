# WhatsApp Sender Service - API Documentation

Layanan sender WhatsApp yang memungkinkan user untuk mengirim pesan WhatsApp melalui HTTP POST API menggunakan autentikasi `sender_key` dan `sender_secret`.

## Fitur Utama

- ✅ Kirim pesan text WhatsApp
- ✅ Kirim pesan dengan gambar
- ✅ Autentikasi dengan sender_key & sender_secret
- ✅ Tracking status pesan
- ✅ Support multiple sender accounts

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Database

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 3. Setup Storage untuk Media

```bash
php artisan storage:link
```

## API Endpoint

Base URL: `http://your-domain.com/api`

### Send WhatsApp Message

**Endpoint:** `POST /send`

**Authentication:** Menggunakan `sender_key` dan `sender_secret` dalam request body

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| sender_key | string | Yes | Unique key untuk identifikasi sender |
| sender_secret | string | Yes | Secret key untuk autentikasi |
| to | string | Yes | Nomor WhatsApp penerima (format: 628xxx) |
| message | string | Conditional | Isi pesan text (required jika tidak ada image) |
| image | file | No | File gambar (JPG, PNG, GIF, max 5MB) |

#### Response Success (200)

```json
{
  "success": true,
  "message": "Message sent successfully",
  "data": {
    "message_id": 1,
    "to": "628987654321",
    "status": "sent",
    "sent_at": "2026-01-27T11:00:00.000000Z"
  }
}
```

#### Response Error - Invalid Credentials (401)

```json
{
  "success": false,
  "message": "Invalid sender credentials"
}
```

#### Response Error - Account Not Connected (400)

```json
{
  "success": false,
  "message": "WhatsApp account is not connected. Please initialize your account first."
}
```

#### Response Error - Validation Failed (422)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "to": ["The to field is required."],
    "message": ["The message field is required when image is not present."]
  }
}
```

#### Response Error - Send Failed (500)

```json
{
  "success": false,
  "message": "Failed to send message",
  "error": "Connection timeout"
}
```

## Contoh Penggunaan

### 1. Kirim Pesan Text

#### cURL
```bash
curl -X POST http://your-domain.com/api/send \
  -H "Content-Type: application/json" \
  -d '{
    "sender_key": "your-sender-key-here",
    "sender_secret": "your-sender-secret-here",
    "to": "628987654321",
    "message": "Hello from WhatsApp Sender!"
  }'
```

#### PHP
```php
<?php

$data = [
    'sender_key' => 'your-sender-key-here',
    'sender_secret' => 'your-sender-secret-here',
    'to' => '628987654321',
    'message' => 'Hello from WhatsApp Sender!'
];

$ch = curl_init('http://your-domain.com/api/send');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
```

#### JavaScript (Fetch)
```javascript
fetch('http://your-domain.com/api/send', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    sender_key: 'your-sender-key-here',
    sender_secret: 'your-sender-secret-here',
    to: '628987654321',
    message: 'Hello from WhatsApp Sender!'
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

#### Python (Requests)
```python
import requests

data = {
    'sender_key': 'your-sender-key-here',
    'sender_secret': 'your-sender-secret-here',
    'to': '628987654321',
    'message': 'Hello from WhatsApp Sender!'
}

response = requests.post('http://your-domain.com/api/send', json=data)
print(response.json())
```

### 2. Kirim Pesan dengan Gambar

#### cURL
```bash
curl -X POST http://your-domain.com/api/send \
  -F "sender_key=your-sender-key-here" \
  -F "sender_secret=your-sender-secret-here" \
  -F "to=628987654321" \
  -F "message=Check out this image!" \
  -F "image=@/path/to/image.jpg"
```

#### PHP (Multipart Form Data)
```php
<?php

$ch = curl_init('http://your-domain.com/api/send');

$data = [
    'sender_key' => 'your-sender-key-here',
    'sender_secret' => 'your-sender-secret-here',
    'to' => '628987654321',
    'message' => 'Check out this image!',
    'image' => new CURLFile('/path/to/image.jpg', 'image/jpeg', 'image.jpg')
];

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
```

#### JavaScript (FormData)
```javascript
const formData = new FormData();
formData.append('sender_key', 'your-sender-key-here');
formData.append('sender_secret', 'your-sender-secret-here');
formData.append('to', '628987654321');
formData.append('message', 'Check out this image!');
formData.append('image', fileInput.files[0]); // from <input type="file">

fetch('http://your-domain.com/api/send', {
  method: 'POST',
  body: formData
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

## Format Nomor Telepon

Nomor WhatsApp penerima harus dalam format internasional tanpa tanda `+`:

- ✅ Benar: `628123456789` (Indonesia)
- ✅ Benar: `6281234567890`
- ❌ Salah: `+628123456789`
- ❌ Salah: `08123456789`

## Mendapatkan sender_key dan sender_secret

`sender_key` dan `sender_secret` harus ditambahkan ke database pada tabel `whatsapp_accounts`:

```sql
-- Contoh insert manual
INSERT INTO whatsapp_accounts 
(user_id, phone_number, name, sender_key, sender_secret, status, created_at, updated_at) 
VALUES 
(1, '628123456789', 'My Account', 'unique-sender-key-123', 'secure-secret-456', 'connected', NOW(), NOW());
```

Atau bisa dibuat interface admin untuk generate otomatis.

## Database Schema

### whatsapp_accounts
- `id` - Primary key
- `user_id` - Foreign key ke users table
- `phone_number` - Nomor WhatsApp sender (unique)
- `name` - Nama akun (optional)
- **`sender_key`** - Unique key untuk autentikasi API (unique)
- **`sender_secret`** - Secret key untuk autentikasi API
- `status` - Status koneksi (pending/connected/disconnected/failed)
- `qr_code` - QR code data untuk inisialisasi
- `session_data` - Data sesi WhatsApp (encrypted)
- `last_connected_at` - Waktu terakhir terhubung

### whatsapp_messages
- `id` - Primary key
- `whatsapp_account_id` - Foreign key ke whatsapp_accounts
- `recipient_number` - Nomor penerima
- `message` - Isi pesan
- `status` - Status pesan (pending/sent/delivered/read/failed)
- `media_url` - URL media (optional)
- `media_type` - Tipe media (image)
- `error_message` - Pesan error jika gagal
- `sent_at` - Waktu terkirim

## Status Pesan

- `pending` - Pesan dalam antrian, belum dikirim
- `sent` - Pesan berhasil dikirim ke WhatsApp
- `delivered` - Pesan terkirim ke penerima
- `read` - Pesan sudah dibaca penerima
- `failed` - Pesan gagal terkirim

## Rate Limiting

Untuk production, sangat disarankan untuk menambahkan rate limiting:

```php
// routes/api.php
Route::post('/send', [WhatsappController::class, 'send'])
    ->middleware('throttle:60,1'); // 60 requests per menit
```

## Security Best Practices

1. **HTTPS**: Selalu gunakan HTTPS di production
2. **Environment**: Jangan expose `sender_secret` secara publik
3. **Rate Limiting**: Batasi jumlah request per IP/sender
4. **Input Validation**: Selalu validasi input untuk mencegah injection
5. **File Upload**: Batasi ukuran dan tipe file yang diupload

## Testing

Untuk testing API, Anda bisa menggunakan:

- **Postman**: Import collection untuk testing
- **Thunder Client** (VS Code extension)
- **cURL**: Command line testing
- **Browser**: Untuk upload form

## Troubleshooting

### Error: "Invalid sender credentials"
- Pastikan `sender_key` dan `sender_secret` benar
- Cek database apakah credentials tersebut ada

### Error: "WhatsApp account is not connected"
- Account WhatsApp belum diinisialisasi
- Status account bukan "connected"
- Perlu scan QR code terlebih dahulu

### Error: "Validation failed"
- Periksa parameter yang dikirim
- Pastikan format nomor telepon benar
- Jika kirim gambar, pastikan file valid (JPG/PNG/GIF, max 5MB)

## Integrasi WhatsApp Provider

Untuk implementasi lengkap, Anda perlu mengintegrasikan dengan WhatsApp provider:

- **Evolution API** (Recommended untuk Laravel)
- **Baileys** (Node.js library)
- **WAHA** (WhatsApp HTTP API)
- **WhatsApp Business API** (Official, berbayar)

Edit file `app/Services/WhatsappService.php` untuk implementasi provider pilihan Anda.

## Notes

1. Pastikan storage sudah di-link: `php artisan storage:link`
2. Folder `storage/app/public/whatsapp-media` akan menyimpan gambar yang diupload
3. Untuk production, gunakan queue untuk mengirim pesan agar tidak blocking request
4. Implementasikan webhook untuk update status pesan secara real-time

---

Last updated: January 27, 2026

## Fitur Utama

- ✅ Multi-account WhatsApp per user
- ✅ Initialize account dengan QR Code
- ✅ Kirim pesan text dan media
- ✅ Tracking status pesan (pending, sent, delivered, read, failed)
- ✅ Riwayat pesan
- ✅ Management akun WhatsApp

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Database

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 3. Setup Authentication (Laravel Sanctum)

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## API Endpoints

Base URL: `http://your-domain.com/api`

Semua endpoint memerlukan authentication menggunakan Laravel Sanctum token.

### Authentication

```
Header: Authorization: Bearer {your-token}
```

### 1. Get All WhatsApp Accounts

**Endpoint:** `GET /whatsapp/accounts`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "phone_number": "628123456789",
      "name": "My Business Account",
      "status": "connected",
      "last_connected_at": "2026-01-27T10:30:00.000000Z",
      "created_at": "2026-01-27T08:00:00.000000Z",
      "updated_at": "2026-01-27T10:30:00.000000Z"
    }
  ]
}
```

### 2. Add New WhatsApp Account

**Endpoint:** `POST /whatsapp/accounts`

**Request Body:**
```json
{
  "phone_number": "628123456789",
  "name": "My Business Account"
}
```

**Response:**
```json
{
  "success": true,
  "message": "WhatsApp account added successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "phone_number": "628123456789",
    "name": "My Business Account",
    "status": "pending",
    "created_at": "2026-01-27T08:00:00.000000Z",
    "updated_at": "2026-01-27T08:00:00.000000Z"
  }
}
```

### 3. Initialize WhatsApp Account (Get QR Code)

**Endpoint:** `POST /whatsapp/accounts/{id}/initialize`

**Response:**
```json
{
  "success": true,
  "message": "Please scan the QR code to connect",
  "data": {
    "qr_code": "data:image/png;base64,...",
    "account": {
      "id": 1,
      "phone_number": "628123456789",
      "status": "pending"
    }
  }
}
```

**Usage:**
1. Call endpoint ini untuk mendapatkan QR code
2. Scan QR code menggunakan WhatsApp mobile app
3. Setelah berhasil scan, status akan berubah menjadi "connected"

### 4. Get Account Status

**Endpoint:** `GET /whatsapp/accounts/{id}/status`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "phone_number": "628123456789",
    "status": "connected",
    "last_connected_at": "2026-01-27T10:30:00.000000Z"
  }
}
```

**Status Values:**
- `pending` - Menunggu scan QR code
- `connected` - Terhubung dan siap mengirim pesan
- `disconnected` - Terputus dari WhatsApp
- `failed` - Gagal terhubung

### 5. Delete WhatsApp Account

**Endpoint:** `DELETE /whatsapp/accounts/{id}`

**Response:**
```json
{
  "success": true,
  "message": "WhatsApp account deleted successfully"
}
```

### 6. Send WhatsApp Message

**Endpoint:** `POST /whatsapp/messages/send`

**Request Body (Text Message):**
```json
{
  "whatsapp_account_id": 1,
  "recipient_number": "628987654321",
  "message": "Hello, this is a test message!"
}
```

**Request Body (Media Message):**
```json
{
  "whatsapp_account_id": 1,
  "recipient_number": "628987654321",
  "message": "Check out this image!",
  "media_url": "https://example.com/image.jpg",
  "media_type": "image"
}
```

**Media Types:**
- `image` - Gambar (JPG, PNG, dll)
- `video` - Video (MP4, dll)
- `document` - Dokumen (PDF, DOC, dll)
- `audio` - Audio (MP3, dll)

**Response:**
```json
{
  "success": true,
  "message": "Message sent successfully",
  "data": {
    "id": 1,
    "whatsapp_account_id": 1,
    "recipient_number": "628987654321",
    "message": "Hello, this is a test message!",
    "status": "sent",
    "sent_at": "2026-01-27T11:00:00.000000Z",
    "created_at": "2026-01-27T11:00:00.000000Z"
  }
}
```

**Message Status:**
- `pending` - Menunggu untuk dikirim
- `sent` - Berhasil dikirim
- `delivered` - Terkirim ke penerima
- `read` - Sudah dibaca penerima
- `failed` - Gagal terkirim

### 7. Get Message History

**Endpoint:** `GET /whatsapp/accounts/{accountId}/messages`

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "whatsapp_account_id": 1,
        "recipient_number": "628987654321",
        "message": "Hello!",
        "status": "delivered",
        "sent_at": "2026-01-27T11:00:00.000000Z"
      }
    ],
    "per_page": 50,
    "total": 100
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "phone_number": ["The phone number has already been taken."]
  }
}
```

### Account Not Connected (400)
```json
{
  "success": false,
  "message": "WhatsApp account is not connected"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to send message",
  "error": "Connection timeout"
}
```

## Contoh Penggunaan dengan cURL

### Add Account
```bash
curl -X POST http://your-domain.com/api/whatsapp/accounts \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "628123456789",
    "name": "My Account"
  }'
```

### Initialize Account
```bash
curl -X POST http://your-domain.com/api/whatsapp/accounts/1/initialize \
  -H "Authorization: Bearer your-token"
```

### Send Message
```bash
curl -X POST http://your-domain.com/api/whatsapp/messages/send \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "whatsapp_account_id": 1,
    "recipient_number": "628987654321",
    "message": "Hello from API!"
  }'
```

## Database Schema

### whatsapp_accounts
- `id` - Primary key
- `user_id` - Foreign key ke users table
- `phone_number` - Nomor WhatsApp (unique)
- `name` - Nama akun (optional)
- `status` - Status koneksi (pending/connected/disconnected/failed)
- `qr_code` - QR code data untuk inisialisasi
- `session_data` - Data sesi WhatsApp (encrypted)
- `last_connected_at` - Waktu terakhir terhubung

### whatsapp_messages
- `id` - Primary key
- `whatsapp_account_id` - Foreign key ke whatsapp_accounts
- `recipient_number` - Nomor penerima
- `message` - Isi pesan
- `status` - Status pesan
- `media_url` - URL media (optional)
- `media_type` - Tipe media (optional)
- `error_message` - Pesan error jika gagal
- `sent_at` - Waktu terkirim

## Integrasi WhatsApp

File `WhatsappService.php` sudah disiapkan dengan placeholder untuk integrasi dengan WhatsApp API provider pilihan Anda:

- **Baileys** (Node.js library untuk WhatsApp Web)
- **WhatsApp Business API** (Official API)
- **WAHA** (WhatsApp HTTP API)
- **Evolution API**
- Dan lainnya

Anda perlu mengimplementasikan method-method berikut sesuai provider yang dipilih:
- `initialize()` - Inisialisasi koneksi dan generate QR
- `sendMessage()` - Kirim pesan
- `disconnect()` - Disconnect akun
- `handleWebhook()` - Handle webhook untuk update status

## Notes

1. **Security**: Pastikan menggunakan HTTPS untuk production
2. **Rate Limiting**: Implementasikan rate limiting untuk mencegah spam
3. **Queue**: Untuk volume tinggi, gunakan Laravel Queue untuk mengirim pesan
4. **Webhook**: Setup webhook untuk menerima update status pesan secara real-time
5. **Session Storage**: Simpan session WhatsApp dengan aman (encrypt di database)

## Roadmap

- [ ] Implementasi Queue untuk pengiriman pesan
- [ ] Webhook handler untuk status update
- [ ] Bulk message sending
- [ ] Template message
- [ ] Group message support
- [ ] Scheduled messages
- [ ] Analytics & reporting
