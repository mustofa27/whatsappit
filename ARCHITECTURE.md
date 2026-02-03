# Incoming Message Handler - Architecture & Flow

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        WHATSAPP USER                            │
│                    Sends message to number                      │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                   META WHATSAPP SERVER                          │
│              Processes and forwards message                     │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
                  ┌──────────────┐
                  │  HTTPS POST  │
                  │   Request    │
                  └──────┬───────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              YOUR SERVER (wait.icminovasi.my.id)                │
│                                                                 │
│  POST /api/webhooks/meta                                       │
│  ├─ Verify request                                             │
│  └─ Call MetaWhatsappService::handleWebhook()                  │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│           MetaWhatsappService::handleWebhook()                  │
│                                                                 │
│  ├─ Parse webhook data                                         │
│  └─ Call handleMessageWebhook()                                │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │
                    ┌────┴────┐
                    ▼         ▼
        ┌──────────────────┐ ┌──────────────────┐
        │  Status Updates  │ │ Incoming Messages│
        │  (sent/read/etc) │ │  (new messages)  │
        └────────┬─────────┘ └────────┬─────────┘
                 │                    │
                 ▼                    ▼
        Update message status │   processIncomingMessages()
        in DB                 │   ├─ Parse message content
                              │   ├─ Handle media (image, etc)
                              │   ├─ Create WhatsappMessage
                              │   ├─ Dispatch event
                              │   └─ Log activity
                              │
                              ▼
                    ┌──────────────────────┐
                    │  Fire Event          │
                    │ IncomingWhatsapp     │
                    │ Message              │
                    └────────┬─────────────┘
                             │
                             ▼
                    ┌──────────────────────┐
                    │ Listener triggers:   │
                    │ Update Conversation  │
                    │ ├─ Create/Update     │
                    │ ├─ Increment unread  │
                    │ └─ Set last_msg_at   │
                    └────────┬─────────────┘
                             │
                             ▼
                    ┌──────────────────────┐
                    │  Database Updated:   │
                    │ ├─ whatsapp_messages │
                    │ └─ whatsapp_          │
                    │   conversations      │
                    └──────────────────────┘
```

## Data Flow for Incoming Message

```
Meta Webhook Payload (JSON)
│
├─ entry[0]
│  └─ changes[0]
│     ├─ field: "messages"
│     └─ value
│        ├─ messages[]  ← INCOMING MESSAGE DATA
│        │  ├─ id (WAMID)
│        │  ├─ from (sender phone)
│        │  ├─ timestamp
│        │  └─ type (text, image, etc)
│        │
│        └─ contacts[]
│           └─ wa_id (sender number)
│
└─ metadata
   ├─ phone_number_id (receiver/your number)
   └─ account_id

        ↓ PARSED BY SYSTEM ↓

WhatsappMessage Record Created:
{
  whatsapp_account_id: 1,
  direction: "incoming",
  contact_number: "62812345678",
  sender_number: "62812345678",
  receiver_number: "62897654321",
  message: "Hello!",
  message_type: "text",
  status: "delivered",
  external_id: "wamid.xxxxx",
  received_at: "2026-02-03 10:30:45",
  metadata: {}
}

        ↓ EVENT DISPATCHED ↓

IncomingWhatsappMessage Event
└─ Listener: UpdateConversationOnIncomingMessage
   └─ Creates/Updates WhatsappConversation:
      {
        whatsapp_account_id: 1,
        contact_number: "62812345678",
        contact_name: null,
        last_message_at: "2026-02-03 10:30:45",
        unread_count: 1,
        is_archived: false
      }
```

## API Response Examples

### List Conversations
```bash
GET /api/conversations/?account_id=1
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "contact_number": "62812345678",
      "contact_name": null,
      "last_message_at": "2026-02-03T10:30:45Z",
      "unread_count": 3,
      "is_archived": false
    }
  ],
  "pagination": {
    "total": 1,
    "limit": 20,
    "offset": 0
  }
}
```

### Get Conversation with Messages
```bash
GET /api/conversations/show?account_id=1&contact_number=62812345678
```

Response:
```json
{
  "success": true,
  "data": {
    "conversation": {
      "id": 1,
      "contact_number": "62812345678",
      "contact_name": null,
      "last_message_at": "2026-02-03T10:30:45Z",
      "unread_count": 3,
      "is_archived": false
    },
    "messages": [
      {
        "id": 1,
        "direction": "outgoing",
        "message": "Hello! How can I help?",
        "message_type": "text",
        "status": "delivered",
        "created_at": "2026-02-03T10:25:00Z"
      },
      {
        "id": 2,
        "direction": "incoming",
        "message": "I need help with my order",
        "message_type": "text",
        "status": "delivered",
        "created_at": "2026-02-03T10:30:45Z"
      }
    ],
    "pagination": {
      "total": 2,
      "limit": 20,
      "offset": 0
    }
  }
}
```

## Message Type Handling

```
Incoming Message from Meta
│
├─ type: "text"
│  └─ Store: body text
│
├─ type: "image"
│  ├─ Store: caption + media_url
│  └─ Metadata: { media_id, mime_type }
│
├─ type: "document"
│  ├─ Store: filename + media_url
│  └─ Metadata: { media_id, mime_type }
│
├─ type: "audio"
│  ├─ Store: media_url (voice message)
│  └─ Metadata: { media_id, mime_type }
│
├─ type: "video"
│  ├─ Store: caption + media_url
│  └─ Metadata: { media_id, mime_type }
│
├─ type: "location"
│  ├─ Store: "Location: lat, long"
│  └─ Metadata: { latitude, longitude, ... }
│
└─ type: "button/list/interactive"
   ├─ Store: selected option title
   └─ Metadata: { full interactive object }
```

## Conversation Lifecycle

```
┌─────────────────┐
│  Message Sent   │  (via API)
│   to Contact    │
└────────┬────────┘
         │
         ▼
    (No conversation
     created yet)

         │
         ▼
┌─────────────────────────────┐
│  Contact Replies            │  (Webhook received)
│  with Message               │
└────────┬────────────────────┘
         │
         ▼
    Conversation AUTO-CREATED
    ├─ unread_count: 1
    ├─ is_archived: false
    └─ last_message_at: now

         │
         ▼
    ┌─────────────────────────┐
    │  More messages from     │
    │  contact arrive         │
    └────────┬────────────────┘
             │
             ▼
    unread_count increments

         │
         ▼
    ┌─────────────────────────┐
    │  User marks as read     │
    │  via API                │
    └────────┬────────────────┘
             │
             ▼
    unread_count: 0
    (messages status stays "delivered")

         │
         ▼
    ┌─────────────────────────┐
    │  User archives          │
    │  conversation           │
    └────────┬────────────────┘
             │
             ▼
    is_archived: true
    (hidden from default list)

         │
         ▼
    ┌─────────────────────────┐
    │  New message from       │
    │  contact arrives        │
    └────────┬────────────────┘
             │
             ▼
    is_archived: false (AUTO-UNARCHIVED)
    unread_count: 1 (incremented again)
```

## Database Schema Relationships

```
┌──────────────────────────┐
│  whatsapp_accounts       │
├──────────────────────────┤
│ id (PK)                  │
│ phone_number             │
│ phone_number_id          │
│ is_verified              │
│ ...                      │
└────────┬─────────────────┘
         │
         ├─ ONE-TO-MANY ─────┐
         │                   │
         ▼                   ▼
┌──────────────────────────┐ ┌──────────────────────────┐
│  whatsapp_messages       │ │ whatsapp_conversations   │
├──────────────────────────┤ ├──────────────────────────┤
│ id (PK)                  │ │ id (PK)                  │
│ whatsapp_account_id (FK) │ │ whatsapp_account_id (FK) │
│ direction                │ │ contact_number           │
│ contact_number           │ │ contact_name             │
│ sender_number            │ │ last_message_at          │
│ receiver_number          │ │ unread_count             │
│ message                  │ │ is_archived              │
│ message_type             │ │ created_at, updated_at   │
│ status                   │ └──────────────────────────┘
│ media_url                │
│ media_type               │  Note: Conversation is grouped
│ external_id              │  by account + contact_number
│ received_at              │
│ metadata                 │
│ created_at, updated_at   │
└──────────────────────────┘
```

## Event Flow

```
IncomingWhatsappMessage EVENT
        │
        ├─ Listener #1: UpdateConversationOnIncomingMessage
        │  ├─ Find/Create Conversation
        │  ├─ Update unread_count
        │  └─ Update last_message_at
        │
        ├─ Listener #2: (Your custom listener here)
        │  ├─ Send notification
        │  ├─ Update CRM
        │  └─ Trigger auto-response
        │
        └─ Listener #3: (Future enhancement)
           ├─ Save to search index
           └─ Trigger analytics
```

---

This architecture provides:
- ✅ Clean separation of concerns
- ✅ Scalable event-driven design
- ✅ Efficient database queries
- ✅ Flexible message type handling
- ✅ RESTful API interface
