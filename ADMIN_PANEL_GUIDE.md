# Admin Panel - Setup Guide

Admin panel WhatsApp Sender menggunakan template **TailAdmin** dengan Tailwind CSS, Alpine.js, dan ApexCharts.

## 🎨 Features

- ✅ Modern dan responsive dashboard dengan TailAdmin
- ✅ Dark mode support
- ✅ Manage WhatsApp accounts
- ✅ View & filter messages history
- ✅ Generate dan regenerate API keys
- ✅ Real-time stats & analytics
- ✅ QR code initialization
- ✅ Mobile-friendly sidebar

## 📦 Installation

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Setup Database

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### 3. Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 4. Create Storage Link

```bash
php artisan storage:link
```

## 🚀 Running the Application

### Development
```bash
php artisan serve
```

Atau menggunakan MAMP:
- Start MAMP server
- Akses: `http://localhost/whatsappit/public`
- Atau setup virtual host: `http://whatsappit.test`

### Production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔐 Default Login

Setelah menjalankan `php artisan db:seed`:

**Admin Account:**
- Email: `admin@whatsapp.com`
- Password: `password`

**Demo Account:**
- Email: `demo@whatsapp.com`
- Password: `password`

## 📱 Admin Panel Pages

### Dashboard (`/admin`)
- Total accounts statistics
- Connected accounts count
- Total messages sent
- Messages sent today
- Recent messages table
- Quick status overview

### WhatsApp Accounts (`/admin/accounts`)
- **List** - View all WhatsApp accounts dengan status
- **Create** - Add new WhatsApp account
- **Edit** - Update account details dan status
- **Show** - View details, API credentials, dan actions
- **Initialize** - Generate QR code untuk koneksi
- **Regenerate Keys** - Generate new sender_key dan sender_secret

### Messages (`/admin/messages`)
- View all sent messages
- Filter by status (pending, sent, delivered, read, failed)
- Filter by account
- Search by phone number atau message content
- Pagination support

## 🎨 TailAdmin Theme Features

### Dark Mode
- Toggle di header (top right)
- Automatically saves preference ke localStorage
- Smooth transitions

### Responsive Sidebar
- Collapsible pada mobile
- Sticky pada desktop
- Auto-hide sidebar toggle on desktop

### Components Used
- Cards dengan shadow & borders
- Tables dengan hover states
- Forms dengan validation styling
- Badges untuk status indicators
- Alerts untuk success/error messages
- Pagination components

## 🔧 Customization

### Colors (tailwind.config.js)
```javascript
colors: {
    primary: '#3C50E0',
    success: '#219653',
    danger: '#D34053',
    warning: '#FFA70B',
    // ... more colors
}
```

### Layout (resources/views/admin/layout.blade.php)
- Edit main layout structure
- Add meta tags
- Include additional scripts

### Sidebar (resources/views/admin/partials/sidebar.blade.php)
- Add/remove menu items
- Change logo
- Modify navigation structure

### Header (resources/views/admin/partials/header.blade.php)
- Add notifications
- Add user menu
- Modify dark mode toggle

## 📊 Stats & Analytics

Dashboard menampilkan:
1. **Total Accounts** - Jumlah semua WhatsApp accounts
2. **Connected Accounts** - Accounts yang status = 'connected'
3. **Total Messages** - Semua messages yang pernah dikirim
4. **Messages Today** - Messages yang dikirim hari ini

## 🔑 API Credentials Management

### View Credentials
- Sender Key: visible, copyable
- Sender Secret: password field dengan toggle visibility, copyable

### Regenerate Keys
1. Go to account detail page
2. Click "Regenerate API Keys" button
3. Confirm action
4. New keys akan di-generate
5. **Warning**: Old keys akan invalid!

### Copy to Clipboard
- Click copy icon pada setiap field
- JavaScript `navigator.clipboard.writeText()`
- Alert notification saat berhasil copy

## 🎯 Workflow

### Adding New WhatsApp Account
1. Login ke admin panel
2. Navigate to "WhatsApp Accounts"
3. Click "Add Account"
4. Fill form:
   - Select user
   - Enter phone number (format: 628xxx)
   - Enter account name (optional)
5. Click "Create Account"
6. System auto-generate sender_key dan sender_secret
7. Go to detail page
8. Click "Initialize / Get QR Code"
9. Scan QR code dengan WhatsApp mobile
10. Status akan berubah menjadi "connected"

### Sending Message via API
1. Get sender_key dan sender_secret dari account detail
2. Use API endpoint: `POST /api/send`
3. Include credentials dalam request body
4. Messages akan muncul di Messages history

### Monitoring Messages
1. Go to "Messages" page
2. Use filters:
   - Status filter
   - Account filter
   - Search by phone/message
3. View message details
4. Check status (pending/sent/delivered/read/failed)

## 🛠️ Troubleshooting

### Assets not loading
```bash
npm run build
php artisan view:clear
php artisan cache:clear
```

### Dark mode not working
- Check browser localStorage
- Clear storage and reload
- Verify Alpine.js is loaded

### Sidebar not responsive
```bash
# Rebuild tailwind
npm run build
```

### Database errors
```bash
php artisan migrate:fresh --seed
```

## 📁 File Structure

```
resources/
├── css/
│   └── app.css                          # Tailwind CSS
├── js/
│   └── app.js                           # Alpine.js & ApexCharts
└── views/
    └── admin/
        ├── layout.blade.php             # Main layout
        ├── dashboard.blade.php          # Dashboard page
        ├── partials/
        │   ├── header.blade.php         # Header dengan dark mode
        │   └── sidebar.blade.php        # Sidebar navigation
        ├── accounts/
        │   ├── index.blade.php          # List accounts
        │   ├── create.blade.php         # Create form
        │   ├── edit.blade.php           # Edit form
        │   └── show.blade.php           # Account details
        └── messages/
            └── index.blade.php          # Messages list

app/Http/Controllers/Admin/
├── DashboardController.php              # Dashboard logic
├── WhatsappAccountController.php        # Account CRUD
└── MessageController.php                # Messages view & filter

routes/
└── web.php                              # Admin routes
```

## 🎨 UI Components

### Cards
```blade
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <!-- Card content -->
</div>
```

### Status Badges
```blade
<!-- Success -->
<span class="inline-flex rounded-full bg-success bg-opacity-10 px-3 py-1 text-sm font-medium text-success">
    Connected
</span>

<!-- Warning -->
<span class="inline-flex rounded-full bg-warning bg-opacity-10 px-3 py-1 text-sm font-medium text-warning">
    Pending
</span>

<!-- Danger -->
<span class="inline-flex rounded-full bg-danger bg-opacity-10 px-3 py-1 text-sm font-medium text-danger">
    Failed
</span>
```

### Buttons
```blade
<!-- Primary Button -->
<button class="flex justify-center rounded bg-primary px-6 py-2 font-medium text-white hover:bg-opacity-90">
    Submit
</button>

<!-- Secondary Button -->
<button class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1">
    Cancel
</button>
```

## 🔐 Security Notes

1. **Never commit** `.env` file
2. **Change default passwords** setelah deploy
3. **Regenerate API keys** secara berkala
4. **Use HTTPS** di production
5. **Enable CSRF protection** (already enabled di Laravel)
6. **Validate all inputs** (already implemented)

## 📈 Next Steps

- [ ] Add user authentication (Laravel Breeze/Jetstream)
- [ ] Add role-based access control (Spatie Permission)
- [ ] Add real-time notifications (Laravel Echo)
- [ ] Add export messages to CSV/Excel
- [ ] Add charts untuk analytics (ApexCharts)
- [ ] Add webhook configuration UI
- [ ] Add bulk message sending
- [ ] Add message templates management

---

**Made with ❤️ using Laravel + TailAdmin**
