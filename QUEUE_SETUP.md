# Message Queue & Scheduling - Feature #4

## Overview
This feature allows you to schedule WhatsApp messages for specific times, with automatic retry mechanisms, rate limiting, and batch sending capabilities.

## Features
✅ Schedule messages for future delivery
✅ Automatic retry mechanism (configurable retries 0-5)
✅ Rate limiting (1 message per second)
✅ Bulk operations (cancel, retry, delete)
✅ Status tracking (pending, processing, sent, failed, cancelled)
✅ Edit pending/failed messages
✅ Queue-based processing for reliability

## Database Schema
The `scheduled_messages` table includes:
- Basic info: account, recipient, message content
- Template support: template_name, template_params
- Scheduling: scheduled_at, status
- Retry logic: retry_count, max_retries
- Error tracking: error_message
- Delivery info: sent_at, meta_message_id

## Queue Setup

### 1. Configure Queue Driver
Edit `.env`:
```
QUEUE_CONNECTION=database
```

### 2. Create Jobs Table (if not exists)
```bash
php artisan queue:table
php artisan migrate
```

### 3. Run Queue Worker
Start the queue worker to process scheduled messages:
```bash
php artisan queue:work --tries=3
```

For production with auto-restart on code changes:
```bash
php artisan queue:work --tries=3 --timeout=60 --sleep=3 --max-jobs=1000
```

### 4. Run Scheduler
The scheduler checks for ready messages every minute:

**Option A: Manual Processing**
```bash
php artisan whatsapp:process-scheduled
```

**Option B: Automatic (Production)**
Add to your crontab:
```bash
* * * * * cd /Applications/MAMP/htdocs/whatsappit && php artisan schedule:run >> /dev/null 2>&1
```

Then run the scheduler:
```bash
php artisan schedule:work
```

## Usage Guide

### Schedule a New Message
1. Go to **Message Queue** in sidebar
2. Click **Schedule New Message**
3. Fill in:
   - WhatsApp Account
   - Recipient Number (format: 628123456789)
   - Message Content
   - Schedule Date & Time (at least 5 minutes in future)
   - Max Retries (0-5, default: 3)
4. Use quick schedule buttons for common delays (+5min, +30min, +1hr, +1day)

### Message Statuses
- **Pending**: Waiting to be sent at scheduled time
- **Processing**: Currently being sent
- **Sent**: Successfully delivered
- **Failed**: Failed after all retries
- **Cancelled**: Manually cancelled before sending

### Bulk Operations
1. Select multiple messages using checkboxes
2. Choose action:
   - **Cancel Selected**: Cancel pending messages
   - **Retry Selected**: Retry failed messages immediately
   - **Delete Selected**: Delete messages (except processing)

### Edit/Retry Failed Messages
- **Edit**: Modify content, recipient, or schedule (resets status to pending)
- **Retry Now**: Immediately dispatch failed message to queue

## Rate Limiting
Messages are dispatched with 1-second delays between each to avoid Meta API rate limits:
- 1 message per second per account
- Configurable in `ProcessScheduledMessages` command

## Retry Logic
When a message fails:
1. Job automatically retries with exponential backoff:
   - 1st retry: after 1 minute
   - 2nd retry: after 5 minutes
   - 3rd retry: after 15 minutes
2. After max retries, message status = "failed"
3. You can manually retry from UI or edit and reschedule

## Production Deployment

### Using Supervisor (Recommended)
Create `/etc/supervisor/conf.d/whatsappit-worker.conf`:
```ini
[program:whatsappit-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /Applications/MAMP/htdocs/whatsappit/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/Applications/MAMP/htdocs/whatsappit/storage/logs/worker.log
stopwaitsecs=3600
```

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start whatsappit-worker:*
```

### Using Laravel Horizon (Alternative)
For Redis-based queues with UI monitoring:
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Testing

### Test Manual Processing
```bash
# Schedule a message via UI for 5 minutes from now
# Then manually trigger processing:
php artisan whatsapp:process-scheduled
```

### Test Queue Worker
```bash
# In one terminal, start queue worker:
php artisan queue:work

# In another terminal, schedule messages or trigger processing:
php artisan whatsapp:process-scheduled
```

### Monitor Queue
```bash
# Check failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## API Integration
The `SendScheduledWhatsappMessage` job uses `MetaWhatsappService`:
- Supports text messages: `sendMessage()`
- Supports template messages: `sendTemplateMessage()`
- Stores Meta message ID for tracking
- Logs all send attempts and errors

## Troubleshooting

### Messages Stuck in Processing
```bash
# Check queue worker is running
ps aux | grep "queue:work"

# Restart queue worker
php artisan queue:restart
```

### Messages Not Being Processed
1. Verify scheduler is running: `php artisan schedule:work`
2. Check scheduled_at is in the past
3. Verify status is "pending"
4. Check logs: `storage/logs/laravel.log`

### Failed Messages
1. Check error_message in database or UI
2. Common issues:
   - Invalid phone number format
   - Expired/invalid access token
   - Meta API rate limits
   - Network timeout
3. Edit message to fix issue, then retry

## Performance Tips
- Use database queue for small-medium loads
- Use Redis queue for high-volume sending
- Adjust `--sleep` and `--max-jobs` for queue worker
- Monitor queue depth: `SELECT COUNT(*) FROM jobs;`
- Archive old sent/cancelled messages periodically

## Security Notes
- Access tokens stored in whatsapp_accounts table
- Only authenticated admin users can schedule messages
- Rate limiting prevents API abuse
- Failed job logs may contain sensitive data - rotate regularly
