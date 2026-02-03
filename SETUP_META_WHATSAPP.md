# Panduan Setup Meta WhatsApp Cloud API

Panduan lengkap untuk mengatur Meta WhatsApp Business Platform untuk aplikasi WAIt.

## 📋 Persyaratan

- Akun Facebook/Meta pribadi
- Nomor telepon yang belum terdaftar di WhatsApp Business API
- Dokumen bisnis (NPWP, KTP, Surat Izin Usaha) untuk verifikasi
- Akses ke server/hosting untuk webhook

---

## 🚀 Langkah 1: Membuat Meta Developer Account

### 1.1 Daftar/Login ke Meta for Developers

1. Buka [https://developers.facebook.com](https://developers.facebook.com)
2. Klik **"Get Started"** atau **"My Apps"** di pojok kanan atas
3. Login dengan akun Facebook Anda
4. Jika pertama kali, Anda akan diminta verifikasi:
   - Verifikasi email
   - Verifikasi nomor telepon (via SMS)
   - Setujui Terms of Service

### 1.2 Lengkapi Profil Developer

1. Masuk ke **Settings** > **Basic**
2. Isi informasi developer:
   - Display Name
   - Contact Email
   - Privacy Policy URL (opsional untuk development)

---

## 📱 Langkah 2: Membuat Aplikasi WhatsApp

### 2.1 Create New App

1. Di [Meta Apps Dashboard](https://developers.facebook.com/apps)
2. Klik **"Create App"** (tombol hijau)
3. Pilih **"Business"** sebagai app type
4. Klik **"Next"**

### 2.2 Konfigurasi App

Isi detail aplikasi:

| Field | Contoh Value |
|-------|--------------|
| **App Name** | WAIt - Sender Service |
| **App Contact Email** | your-email@domain.com |
| **Business Account** | Create new atau pilih existing |

5. Klik **"Create App"**
6. Tunggu proses pembuatan (beberapa detik)

---

## 🔐 Langkah 3: Mendapatkan Credentials

### 3.1 App ID & App Secret

1. Setelah app dibuat, Anda akan di-redirect ke Dashboard
2. Di sidebar kiri, klik **"Settings"** > **"Basic"**
3. Salin credentials berikut:

```env
META_WHATSAPP_APP_ID=123456789012345
META_WHATSAPP_APP_SECRET=abc123def456ghi789jkl012mno345pq
```

⚠️ **PENTING**: Jangan share App Secret ke publik!

### 3.2 Tambahkan WhatsApp Product

1. Di sidebar kiri, cari **"Add Product"**
2. Temukan **"WhatsApp"** 
3. Klik **"Set Up"**
4. Pilih **WhatsApp Business Account**:
   - Create new atau
   - Pilih existing (jika sudah punya)

---

## 📞 Langkah 4: Setup Phone Number

### 4.1 Gunakan Test Number (Development)

Meta menyediakan test number untuk development:

1. Di WhatsApp Dashboard, lihat section **"API Setup"**
2. Anda akan melihat:
   - **Test Phone Number**: +1 555-0100 (contoh)
   - **Phone Number ID**: `102918xxxxx`
   - **WhatsApp Business Account ID**: `104567xxxxx`

3. Tambahkan recipient phone untuk testing:
   - Klik **"Add phone number"**
   - Masukkan nomor Anda (format: +62812...)
   - Verifikasi via SMS/WhatsApp
   - Max 5 recipients untuk test number

4. Salin credentials:

```env
META_WHATSAPP_PHONE_ID=102918xxxxx
META_WHATSAPP_BUSINESS_ID=104567xxxxx
```

### 4.2 Gunakan Production Number (Opsional)

⚠️ Untuk production, Anda perlu:

1. **Business Verification** (wajib)
2. **Phone Number Registration** 
3. Klik **"Add Phone Number"** di dashboard
4. Masukkan nomor bisnis Anda
5. Verifikasi kepemilikan (SMS atau Call)
6. Tunggu approval (1-3 hari kerja)

**Persyaratan Nomor:**
- Belum terdaftar di WhatsApp (personal/business)
- Dapat menerima SMS/call
- Tidak VOIP (harus nomor GSM asli)

---

## 🔑 Langkah 5: Generate Access Token

### 5.1 Temporary Token (24 jam)

1. Di WhatsApp Dashboard, section **"API Setup"**
2. Klik **"Generate Token"** atau lihat **"Temporary access token"**
3. Salin token (expires in 24 hours):

```env
META_WHATSAPP_ACCESS_TOKEN=EAAxxxxxxxxxxxxxxxxxxxxx
```

### 5.2 Permanent Token (Recommended)

#### Option A: System User Token (Untuk Production)

1. Buka [Business Settings](https://business.facebook.com/settings)
2. Pilih Business Account Anda
3. Klik **"Users"** > **"System Users"**
4. Klik **"Add"**:
   - Name: `WAIt System User`
   - Role: `Admin`
5. Generate token:
   - Klik **"Generate New Token"**
   - Pilih App Anda
   - Permissions: `whatsapp_business_management`, `whatsapp_business_messaging`
   - Duration: **Never Expire**
6. Salin dan simpan token dengan aman

#### Option B: User Access Token

1. Gunakan [Graph API Explorer](https://developers.facebook.com/tools/explorer/)
2. Pilih aplikasi Anda
3. Permissions: tambahkan `whatsapp_business_management`, `whatsapp_business_messaging`
4. Generate token
5. Extend token via endpoint:
   ```
   GET https://graph.facebook.com/oauth/access_token?
     grant_type=fb_exchange_token&
     client_id={app-id}&
     client_secret={app-secret}&
     fb_exchange_token={short-lived-token}
   ```

---

## 🌐 Langkah 6: Setup Webhook

### 6.1 Konfigurasi Webhook URL

1. Di WhatsApp Dashboard, klik **"Configuration"**
2. Di section **"Webhook"**, klik **"Edit"**
3. Masukkan:

| Field | Value |
|-------|-------|
| **Callback URL** | `https://wait.icminovasi.my.id/api/webhooks/meta` |
| **Verify Token** | `wait_verify_2026` (sama dengan `.env`) |

4. Klik **"Verify and Save"**

⚠️ **Webhook harus sudah live** sebelum verify!

### 6.2 Subscribe to Events

Centang event yang diperlukan:
- ✅ **messages** (pesan masuk)
- ✅ **message_status** (status delivered/read)
- ✅ **message_echoes** (pesan yang kita kirim)
- ✅ **message_template_status_update** (status template)

Klik **"Subscribe"**

### 6.3 Test Webhook

Meta akan mengirim GET request ke URL Anda:

```
GET /api/webhooks/meta?
  hub.mode=subscribe&
  hub.challenge=1234567890&
  hub.verify_token=wait_verify_2026
```

Aplikasi harus return `hub.challenge` jika token match.

Lihat log untuk memastikan:
```bash
tail -f storage/logs/laravel.log
```

---

## ⚙️ Langkah 7: Update Environment Variables

### 7.1 Edit File `.env`

Buka file `.env` dan update:

```env
# Meta WhatsApp Cloud API Configuration
META_WHATSAPP_APP_ID=123456789012345
META_WHATSAPP_APP_SECRET=abc123def456ghi789jkl012mno345pq
META_WHATSAPP_API_VERSION=v21.0
META_WHATSAPP_VERIFY_TOKEN=wait_verify_2026
META_WHATSAPP_PHONE_ID=102918xxxxx
META_WHATSAPP_BUSINESS_ID=104567xxxxx
META_WHATSAPP_ACCESS_TOKEN=EAAxxxxxxxxxxxxxxxxxxxxx
```

### 7.2 Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🗄️ Langkah 8: Run Database Migration

Tambahkan field Meta ke tabel `whatsapp_accounts`:

```bash
php artisan migrate
```

Output yang diharapkan:
```
Running migrations.
2026_01_30_000001_add_meta_fields_to_whatsapp_accounts_table .... DONE
```

---

## ✅ Langkah 9: Test Phone Verification

### 9.1 Create Account

1. Login ke admin panel: `http://localhost:8888/admin/accounts`
2. Klik **"Create New Account"**
3. Isi form:
   - **Name**: Test Account
   - **Phone Number**: +6281234567890 (nomor yang sudah didaftarkan di test recipients)
4. Submit

### 9.2 Request Verification Code

1. Di halaman account detail, klik **"Verify Phone Number"**
2. Klik **"Request Verification Code"**
3. Lihat log untuk mendapatkan kode:
   ```bash
   tail -f storage/logs/laravel.log
   ```
4. Cari: `Verification code for account XXX: 123456`

⚠️ **Note**: Karena SMS real belum diimplementasi, kode akan muncul di log. Code expires dalam 5 menit.

### 9.3 Verify Code

1. Masukkan 6-digit code
2. Klik **"Verify"**
3. Jika berhasil, status berubah menjadi **"Verified"**

---

## 📤 Langkah 10: Test Sending Message

### 10.1 Via Postman/cURL

```bash
curl -X POST http://localhost:8888/api/send \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "sender_key=sk_your_sender_key" \
  -d "sender_secret=ss_your_sender_secret" \
  -d "to=6281234567890" \
  -d "message=Hello from WAIt!"
```

### 10.2 Expected Response

**Success:**
```json
{
  "status": "success",
  "message": "Message sent successfully",
  "message_id": "wamid.HBgNNjI4MTIzNDU2Nzg5MBUCABEYEjREOEQzRjYzMzQxMTQ2RDEA",
  "data": {
    "messaging_product": "whatsapp",
    "contacts": [{
      "input": "6281234567890",
      "wa_id": "6281234567890"
    }]
  }
}
```

**Error (Rate Limit):**
```json
{
  "status": "error",
  "message": "Rate limit exceeded. Maximum 20 requests per minute."
}
```

### 10.3 Check Delivery Status

Webhook akan mengirim update status:

```json
{
  "object": "whatsapp_business_account",
  "entry": [{
    "changes": [{
      "field": "messages",
      "value": {
        "messaging_product": "whatsapp",
        "statuses": [{
          "id": "wamid.xxx",
          "status": "delivered",
          "timestamp": "1738234567"
        }]
      }
    }]
  }]
}
```

Lihat di `storage/logs/laravel.log`

---

## 📝 Langkah 11: Message Templates (Production Only)

Untuk mengirim pesan ke nomor yang **belum pernah chat** dengan bisnis Anda dalam 24 jam terakhir, wajib pakai template.

### 11.1 Create Template

1. Buka [WhatsApp Manager](https://business.facebook.com/wa/manage/message-templates/)
2. Klik **"Create Template"**
3. Isi:
   - **Name**: `welcome_message` (lowercase, underscore only)
   - **Category**: Utility, Marketing, atau Authentication
   - **Language**: Indonesian
   - **Header**: (opsional) Text/Media
   - **Body**: 
     ```
     Halo {{1}}! Selamat datang di WAIt.
     Terima kasih telah mendaftar sebagai pelanggan kami.
     ```
   - **Footer**: (opsional) `WAIt - Automated Service`
   - **Buttons**: (opsional) Call to Action

4. Klik **"Submit"**
5. Tunggu approval (beberapa jam - 1 hari)

### 11.2 Send Template Message

```php
$response = Http::withToken(config('services.meta_whatsapp.access_token'))
    ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'template',
        'template' => [
            'name' => 'welcome_message',
            'language' => [
                'code' => 'id'
            ],
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => 'Ahmad'] // {{1}}
                    ]
                ]
            ]
        ]
    ]);
```

---

## 🔍 Troubleshooting

### Error: "Invalid Phone Number"

**Solusi:**
- Pastikan format: +62812... (dengan +)
- Nomor harus sudah ada di WhatsApp
- Untuk test number, tambahkan ke recipient list dulu

### Error: "Webhook verification failed"

**Solusi:**
- Pastikan webhook URL accessible dari internet (gunakan ngrok untuk local)
- Cek verify_token di .env sama dengan yang di Meta Dashboard
- Lihat response dari endpoint: `/api/webhooks/meta?hub.mode=subscribe&hub.challenge=123&hub.verify_token=xxx`

### Error: "Access Token Expired"

**Solusi:**
- Generate permanent token (System User)
- Atau extend token durasi
- Update META_WHATSAPP_ACCESS_TOKEN di .env

### Error: "(#131030) Recipient not available"

**Solusi:**
- Untuk test number, recipient harus didaftarkan dulu di Meta Dashboard
- Maksimal 5 recipients untuk development mode
- Atau upgrade ke production number

### Rate Limit: "Too Many Requests"

**Solusi:**
- Default limit: 1000 messages/day untuk conversation-based pricing
- 80 messages/second per phone number
- Aplikasi sudah ada rate limiting 20/menit
- Untuk increase limit, hubungi Meta Support

---

## 📊 Monitoring & Analytics

### WhatsApp Manager Dashboard

Akses [https://business.facebook.com/wa/manage/home/](https://business.facebook.com/wa/manage/home/)

Monitor:
- **Analytics**: Messages sent/delivered/read/failed
- **Conversations**: Jumlah conversation (billing basis)
- **Quality Rating**: Phone number quality (Green/Yellow/Red)
- **Message Templates**: Status approval templates

### Laravel Logs

```bash
# Real-time monitoring
tail -f storage/logs/laravel.log

# Filter Meta API logs
grep "Meta API" storage/logs/laravel.log

# Filter errors
grep "ERROR" storage/logs/laravel.log
```

---

## 💰 Pricing & Limits

### Free Tier (Development)

- ✅ 1,000 conversations gratis/bulan
- ✅ Test number included
- ✅ All features available
- ⏱️ Rate limit: 80 msg/second

### Production Pricing (2026)

| Kategori Conversation | Harga (IDR) |
|-----------------------|-------------|
| **Marketing** | ~Rp 900/conversation |
| **Utility** | ~Rp 500/conversation |
| **Authentication** | Gratis (limited) |
| **Service** | ~Rp 300/conversation |

**Conversation Window**: 24 jam setelah user reply

Lihat pricing terbaru: [Meta Pricing](https://developers.facebook.com/docs/whatsapp/pricing)

---

## 🔒 Security Best Practices

### 1. Protect Credentials

```bash
# Jangan commit .env ke git
echo ".env" >> .gitignore

# Set proper permissions
chmod 600 .env
```

### 2. Use HTTPS for Webhooks

- Produksi wajib HTTPS (SSL certificate)
- Local development: gunakan ngrok

### 3. Validate Webhook Signature (Recommended)

Update `MetaWhatsappService.php`:

```php
public function validateSignature($payload, $signature)
{
    $expectedSignature = hash_hmac(
        'sha256',
        $payload,
        config('services.meta_whatsapp.app_secret')
    );
    
    return hash_equals('sha256=' . $expectedSignature, $signature);
}
```

### 4. Rate Limiting

Sudah implemented di aplikasi:
- 20 requests/menit per account
- Human-like delay 2-4 detik

---

## 📚 Resources

### Official Documentation

- [WhatsApp Cloud API Docs](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [Getting Started Guide](https://developers.facebook.com/docs/whatsapp/cloud-api/get-started)
- [API Reference](https://developers.facebook.com/docs/whatsapp/cloud-api/reference)
- [Webhooks Guide](https://developers.facebook.com/docs/whatsapp/cloud-api/webhooks)

### Tools

- [Graph API Explorer](https://developers.facebook.com/tools/explorer/)
- [Webhook Testing Tool](https://webhook.site/)
- [ngrok](https://ngrok.com/) - Tunnel untuk local testing

### Support

- [Meta Business Help Center](https://www.facebook.com/business/help)
- [WhatsApp Business API Support](https://developers.facebook.com/support/)

---

## ✨ Next Steps

Setelah setup selesai:

1. ✅ **Test thoroughly** dengan berbagai scenario
2. 📋 **Create message templates** untuk marketing
3. 🔐 **Setup Business Verification** untuk production
4. 📱 **Register production phone number**
5. 💳 **Add payment method** di Meta Business Manager
6. 📊 **Monitor conversation usage** untuk billing
7. 🚀 **Deploy to production server**

---

**Selamat! Setup Meta WhatsApp Cloud API selesai.** 🎉

Jika ada pertanyaan atau error, cek:
1. Laravel logs: `storage/logs/laravel.log`
2. Meta App Dashboard: Error di Alerts section
3. Webhook logs di Meta Dashboard

**Update terakhir:** 30 Januari 2026
