# Xendit Subscription Payment Setup Guide

## ✅ Implementation Complete

I've successfully implemented Xendit subscription payment integration for your WhatsApp management platform. Here's what has been created:

## 📦 Database Tables

### 1. `user_subscriptions`
- Tracks user subscription status
- Links users to subscription plans
- Stores expiration dates and cancellation info

### 2. `subscription_payments`
- Records all payment transactions
- Stores Xendit transaction IDs
- Tracks payment status (pending, paid, failed, expired)

### 3. `subscription_invoices`
- Generates invoice numbers
- Tracks billing history
- Links payments to subscriptions

## 🔧 Components Created

### Models
- `UserSubscription` - Subscription management
- `SubscriptionPayment` - Payment tracking
- `SubscriptionInvoice` - Invoice generation

### Services
- `XenditService` - Payment gateway integration
  - Create invoices
  - Process webhooks
  - Verify payments

### Controllers
- `SubscriptionController` - User subscription management
- `XenditWebhookController` - Payment callback handler

### Views
- `/subscription` - Choose plan page
- `/subscription/my-subscription` - Manage subscription
- `/subscription/success` - Payment success
- `/subscription/failed` - Payment failed

## 🚀 Setup Instructions

### 1. Add Environment Variables

Add these to your `.env` file:

```env
# Xendit Configuration
XENDIT_SECRET_KEY=your_xendit_secret_key_here
XENDIT_PUBLIC_KEY=your_xendit_public_key_here
XENDIT_WEBHOOK_TOKEN=your_random_webhook_token_here
XENDIT_BASE_URL=https://api.xendit.co
```

### 2. Get Xendit API Keys

1. Sign up at https://dashboard.xendit.co/register
2. Go to **Settings** → **Developers** → **API Keys**
3. Copy your **Secret Key** (starts with `xnd_development_` for test mode)
4. Copy your **Public Key** (if needed)
5. Generate a random webhook token for security

### 3. Run Migrations

```bash
php artisan migrate
```

This will create the 3 subscription tables.

### 4. Configure Webhook in Xendit Dashboard

1. Go to **Settings** → **Webhooks**
2. Add a new webhook URL:
   ```
   https://yourdomain.com/webhook/xendit
   ```
3. Select events to subscribe:
   - ✅ Invoice paid
   - ✅ Invoice expired
4. Set callback token (use the same as `XENDIT_WEBHOOK_TOKEN` in .env)

### 5. Create Subscription Plans

Use the admin panel to create plans:
- Go to **Subscription Plans** menu (admin only)
- Click **Create New Plan**
- Fill in:
  - Name (e.g., "Starter", "Business", "Enterprise")
  - Price in Rupiah (e.g., 99000 for Rp 99,000)
  - Description
  - Features (one per line)
  - Max accounts and users
  - Popular flag (for highlighting)
  - Active status

## 💡 How It Works

### User Flow:
1. User goes to **My Subscription** menu
2. Selects a plan and clicks **Subscribe Now**
3. Gets redirected to Xendit payment page
4. Completes payment via:
   - Credit/Debit Card
   - Bank Transfer
   - E-wallet (GoPay, OVO, DANA, etc.)
   - QRIS
   - Retail (Alfamart, Indomaret)
5. Xendit sends webhook to your server
6. Subscription automatically activated
7. User redirected to success page

### Admin Flow:
1. Create and manage subscription plans
2. View all user subscriptions (coming soon)
3. Adjust pricing and features
4. Activate/deactivate plans

## 🔐 Security Features

- ✅ Webhook token verification
- ✅ HTTPS required for production
- ✅ Unique external IDs per transaction
- ✅ Payment status validation
- ✅ Transaction logging

## 📊 Available Routes

**Public:**
- `GET /pricing` - View pricing plans
- `POST /webhook/xendit` - Xendit callback (no auth)

**User (Auth Required):**
- `GET /subscription` - Choose plan
- `POST /subscription/subscribe/{plan}` - Subscribe to plan
- `GET /subscription/success` - Payment success page
- `GET /subscription/failed` - Payment failed page
- `GET /subscription/my-subscription` - View current subscription
- `POST /subscription/cancel` - Cancel subscription

**Admin (Admin Only):**
- Subscription Plans CRUD in admin panel

## 🧪 Testing

### Test Mode (Development)
1. Use Xendit test credentials (starts with `xnd_development_`)
2. Use test payment methods:
   - Test cards: 4000 0000 0000 0002 (success)
   - Test cards: 4000 0000 0000 0127 (failed)
3. Webhook will be sent to your local URL (use ngrok for local testing)

### Production Mode
1. Switch to live credentials (starts with `xnd_production_`)
2. Update webhook URL to production domain
3. Real payments will be processed

## 🎯 Next Steps

1. **Run migrations**: `php artisan migrate`
2. **Add .env variables** with your Xendit credentials
3. **Create subscription plans** in admin panel
4. **Configure webhooks** in Xendit dashboard
5. **Test with test credentials** before going live

## 📝 Additional Features You Can Add

- Email notifications (payment received, subscription expiring)
- Grace period after expiration (3-7 days)
- Subscription upgrade/downgrade
- Proration for plan changes
- Auto-renewal reminders
- Invoice PDF generation
- Payment receipts

## 🆘 Troubleshooting

**Webhook not working?**
- Check if webhook URL is accessible from internet
- Verify webhook token matches in .env and Xendit dashboard
- Check logs in `storage/logs/laravel.log`

**Payment not activating subscription?**
- Check webhook is configured in Xendit
- Verify callback token
- Check payment status in database

**Need help?**
- Xendit docs: https://developers.xendit.co/
- Support: https://help.xendit.co/

---

Your subscription payment system is ready to go! 🚀
