# Authentication Guide

Sistem authentication WhatsApp Sender menggunakan sistem login/register dengan styling TailAdmin.

## Akses Halaman

### Login
- **URL**: `http://localhost:8888/whatsappit/public/login`
- Digunakan untuk masuk ke admin panel
- Fitur "Remember Me" tersedia

### Register  
- **URL**: `http://localhost:8888/whatsappit/public/register`
- Untuk membuat akun baru
- Validasi: email unique, password minimal 8 karakter

### Logout
- Klik dropdown user di header (kanan atas)
- Pilih "Log Out"

## Default User (dari Database Seeder)

```
Email: admin@whatsapp.com
Password: password

Email: demo@whatsapp.com  
Password: password
```

## Proteksi Routes

Semua routes `/admin/*` sudah diproteksi dengan middleware `auth`:
- Dashboard
- WhatsApp Accounts (CRUD)
- Messages History
- API Credentials Management

User yang belum login akan otomatis redirect ke halaman login.

## Testing Authentication

1. **Start MAMP Server**
   - Pastikan Apache dan MySQL running

2. **Akses Login Page**
   ```
   http://localhost:8888/whatsappit/public/login
   ```

3. **Login dengan Default Credentials**
   - Email: `admin@whatsapp.com`
   - Password: `password`

4. **Test Register (Optional)**
   - Klik link "Sign Up" di halaman login
   - Isi form dengan data valid
   - Setelah register otomatis login dan redirect ke dashboard

5. **Test Logout**
   - Di admin panel, klik avatar/nama user di kanan atas
   - Klik "Log Out"
   - Akan redirect ke halaman login

## Features

### Login Page
- Email validation
- Password field
- Remember me checkbox (dengan Alpine.js animation)
- Link ke register page
- Error handling untuk credentials salah
- Demo credentials info

### Register Page  
- Name field (required)
- Email field (unique validation)
- Password field (min 8 chars)
- Password confirmation
- Link ke login page
- Error handling untuk validation

### Protected Admin Panel
- Auto-redirect ke login jika belum auth
- User dropdown di header dengan nama & email
- Logout functionality
- Session management

## Catatan

- Session disimpan di `storage/framework/sessions/`
- Password di-hash menggunakan `Hash::make()` dari Laravel
- CSRF protection aktif di semua forms
- Styling konsisten dengan TailAdmin theme
- Dark mode support di auth pages
