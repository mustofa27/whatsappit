# Incoming Message Handler Implementation

## Overview
Feature #1 - Incoming Message Handler has been implemented. The system can now:
- ✅ Receive & store incoming messages from Meta WhatsApp webhooks
- ✅ Parse different message types (text, image, document, audio, video, location, interactive)
- ✅ Manage conversations grouped by contact number
- ✅ Track unread messages
- ✅ Archive/unarchive conversations
- ✅ Trigger events when messages arrive

## Changes Made

### 1. Database Migrations

#### `2026_02_03_000001_add_incoming_message_fields_to_whatsapp_messages_table.php`
Extended `whatsapp_messages` table with:
- `direction` (incoming/outgoing) - distinguish message direction
- `contact_number` - primary contact (renamed from recipient_number)
- `sender_number` - who sent the message
- `receiver_number` - who received the message
- `external_id` - Meta's message ID (WAMID)
- `message_type` - type of message (text, image, document, etc)
- `received_at` - timestamp from Meta
- `metadata` - JSON field for extra data (media info, interactive responses)

#### `2026_02_03_000002_create_whatsapp_conversations_table.php`
New `whatsapp_conversations` table with:
- `whatsapp_account_id` - account the conversation belongs to
- `contact_number` - contact's WhatsApp number
- `contact_name` - optional contact name
- `last_message_at` - last activity timestamp
- `unread_count` - count of unread incoming messages
- `is_archived` - archive status

### 2. Models

#### `WhatsappMessage` (Updated)
New methods:
- `isIncoming()` - check if message is incoming
- `isOutgoing()` - check if message is outgoing

Updated fillable fields to include all new columns.

#### `WhatsappConversation` (New)
New model for conversation management with methods:
- `messages()` - get all messages in conversation
- `latestMessage()` - get latest message
- `unreadMessages()` - get unread incoming messages
- `markAsRead()` - mark conversation as read
- `archive()` - archive conversation
- `unarchive()` - unarchive conversation

### 3. Service Layer

#### `MetaWhatsappService` (Enhanced)
New/Updated methods:
- `handleMessageWebhook()` - enhanced to handle both incoming messages and status updates
- `processIncomingMessages()` - new method that:
  - Parses different message types
  - Handles media (image, document, audio, video)
  - Parses interactive messages (button/list replies)
  - Stores location data
  - Creates WhatsappMessage records
  - Triggers `IncomingWhatsappMessage` event

### 4. Events

#### `IncomingWhatsappMessage` (New)
Event dispatched when a message is received. Can be listened to by other parts of the application.

### 5. Listeners

#### `UpdateConversationOnIncomingMessage` (New)
Listener that automatically:
- Creates or updates conversation when message arrives
- Increments unread count
- Updates last_message_at timestamp
- Unarchives conversation if archived

### 6. Controllers

#### `WhatsappConversationController` (New)
API endpoints for conversation management:

```
GET  /api/conversations/
POST /api/conversations/show
POST /api/conversations/mark-as-read
POST /api/conversations/archive
POST /api/conversations/unarchive
```

#### `WhatsappController` (Updated)
Updated to use new column names when creating outgoing messages.

### 7. Routes

#### `routes/api.php` (Updated)
Added new conversation management routes under `/api/conversations/`.

## Message Types Supported

The system can parse and store:
- **text** - plain text messages
- **image** - images with optional captions
- **document** - PDFs and other documents
- **audio** - voice messages
- **video** - video messages with optional captions
- **location** - location data (latitude, longitude)
- **button** - button click responses
- **list** - list selection responses
- **interactive** - other interactive message types

## Usage Examples

### Get All Conversations
```bash
curl -X GET "http://localhost/api/conversations/?account_id=1"
```

### Get Conversation with Messages
```bash
curl -X GET "http://localhost/api/conversations/show?account_id=1&contact_number=62812345678"
```

### Mark Conversation as Read
```bash
curl -X POST "http://localhost/api/conversations/mark-as-read" \
  -H "Content-Type: application/json" \
  -d '{"account_id": 1, "contact_number": "62812345678"}'
```

### Archive Conversation
```bash
curl -X POST "http://localhost/api/conversations/archive" \
  -H "Content-Type: application/json" \
  -d '{"account_id": 1, "contact_number": "62812345678"}'
```

## Next Steps

To use this feature:

1. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

2. **Test Incoming Messages:**
   - Send a message from WhatsApp to your test number
   - Check if it appears in `whatsapp_messages` table with direction='incoming'
   - Check if conversation is created/updated in `whatsapp_conversations` table

3. **Extend Further:**
   - Add authentication middleware to conversation endpoints
   - Create UI to display conversations
   - Add real-time updates using WebSockets/broadcasting
   - Add search and filtering to conversations

## Database Queries Reference

```sql
-- Get unread conversations for account
SELECT * FROM whatsapp_conversations 
WHERE whatsapp_account_id = 1 
AND unread_count > 0 
AND is_archived = false
ORDER BY last_message_at DESC;

-- Get message history with specific contact
SELECT * FROM whatsapp_messages 
WHERE whatsapp_account_id = 1 
AND contact_number = '62812345678'
ORDER BY created_at DESC;

-- Get unread incoming messages
SELECT * FROM whatsapp_messages 
WHERE whatsapp_account_id = 1 
AND contact_number = '62812345678'
AND direction = 'incoming'
AND status = 'delivered'
ORDER BY created_at;
```

## Technical Notes

- Messages are stored with `status='delivered'` when received (they've already been delivered to the server)
- Outgoing messages start with `status='pending'` and are updated via webhook
- Conversations automatically unarchive when a new incoming message arrives
- The `metadata` JSON field is flexible and stores different data based on message type
- External_id (WAMID) is used to correlate messages between your system and Meta
