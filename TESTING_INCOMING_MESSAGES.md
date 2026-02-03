# Testing Incoming Message Handler

## Prerequisites
- Ensure migrations are run: `php artisan migrate`
- Have a verified WhatsApp account configured in the system
- Meta webhooks configured to point to your server

## Step 1: Send Test Message
Send a WhatsApp message from your phone to the registered WhatsApp number.

## Step 2: Verify Message Storage
Check the database for the incoming message:

```sql
SELECT * FROM whatsapp_messages 
WHERE direction = 'incoming' 
ORDER BY created_at DESC LIMIT 1;
```

Expected fields:
- `direction`: 'incoming'
- `message`: your message content
- `message_type`: 'text' (or other type)
- `status`: 'delivered'
- `received_at`: timestamp
- `external_id`: Meta's WAMID

## Step 3: Verify Conversation Created
Check if conversation was created:

```sql
SELECT * FROM whatsapp_conversations 
WHERE contact_number = '<sender_number>' 
ORDER BY created_at DESC LIMIT 1;
```

Expected:
- `unread_count`: 1
- `last_message_at`: recent timestamp
- `is_archived`: false

## Step 4: Test API Endpoints

### List Conversations
```bash
curl "http://localhost/api/conversations/?account_id=1"
```

### Get Conversation Details
```bash
curl "http://localhost/api/conversations/show?account_id=1&contact_number=62812345678"
```

### Mark as Read
```bash
curl -X POST "http://localhost/api/conversations/mark-as-read" \
  -H "Content-Type: application/json" \
  -d '{"account_id": 1, "contact_number": "62812345678"}'
```

Verify unread_count becomes 0:
```sql
SELECT unread_count FROM whatsapp_conversations 
WHERE account_id = 1 AND contact_number = '62812345678';
```

## Step 5: Test Different Message Types

### Send Image
Send an image from WhatsApp and check:
```sql
SELECT message_type, media_url, metadata FROM whatsapp_messages 
WHERE direction = 'incoming' AND message_type = 'image'
ORDER BY created_at DESC LIMIT 1;
```

### Send Document
```sql
SELECT message_type, media_url, metadata FROM whatsapp_messages 
WHERE direction = 'incoming' AND message_type = 'document'
ORDER BY created_at DESC LIMIT 1;
```

### Send Location
```sql
SELECT message_type, metadata FROM whatsapp_messages 
WHERE direction = 'incoming' AND message_type = 'location'
ORDER BY created_at DESC LIMIT 1;
```

## Troubleshooting

### Messages not appearing
1. Check webhook logs: `tail -f storage/logs/laravel.log`
2. Look for "Meta webhook received" entries
3. Verify webhook token in `.env` matches Meta dashboard

### Conversation not created
1. Check if listener is registered in `AppServiceProvider`
2. Verify webhook includes sender information

### Event not triggered
1. Check logs for "Incoming message stored" entries
2. Verify `IncomingWhatsappMessage` event is being dispatched

## Performance Considerations

- Conversations table has indexes on:
  - `whatsapp_account_id` + `contact_number` (for lookups)
  - `is_archived` (for filtering)
  - `last_message_at` (for sorting)

- Messages table should be indexed similarly for large volumes

## Future Enhancements
- Add message search
- Add pagination for message history
- Add message reactions/replies tracking
- Add conversation labels/tags
- Add bulk operations (delete multiple conversations)
