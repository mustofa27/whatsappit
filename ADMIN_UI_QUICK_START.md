# Admin UI Quick Reference

## 🚀 Access the Admin Panel

```
URL: http://localhost/admin/conversations
Requires: User authentication (login first)
```

## 📊 Admin Panel Layout

```
┌─────────────────────────────────────────────────────┐
│  Navigation Bar                                       │
│  [Home] [Dashboard] [Conversations] [Accounts] [Logout]
└─────────────────────────────────────────────────────┘

┌──────────────────────┬──────────────────────────────┐
│                      │                              │
│  Conversations       │  Message History             │
│  ───────────────     │  ──────────────────          │
│  □ John (62812...)   │  Incoming msg              │
│    1 unread          │  Outgoing msg              │
│  □ Alice (62823...)  │  [Type message...] [Send]  │
│  □ Bob (62834...)    │                              │
│                      │  [Mark as Read]             │
│                      │  [Archive]                  │
│                      │                              │
└──────────────────────┴──────────────────────────────┘
```

## ✨ Key Features

### Conversations List
- **Name/Number**: Contact information
- **Unread Badge**: Red badge showing unread count
- **Last Message Time**: "2 hours ago", "just now", etc
- **Click to Select**: Opens that conversation

### Message Display
- **Incoming**: Gray background on left
- **Outgoing**: Blue background on right
- **Status**: Shows "pending", "sent", "delivered", "read"
- **Time**: Hour and minutes in corner
- **Media**: Images, documents, audio, video display

### Action Buttons
- **Mark as Read**: Only shows if unread count > 0
- **Archive**: Hide conversation
- **Unarchive**: Restore archived conversation
- **Send**: Submit message form

## 🎯 Common Tasks

### View All Conversations
1. Click: `/admin/conversations`
2. See list on left side
3. Conversations sorted by latest message

### Read a Conversation
1. Click on conversation name
2. Messages load on right
3. See full history

### Send a Reply
1. Select conversation
2. Type in message box
3. Click Send button
4. Message appears in history
5. Gets sent via WhatsApp API

### Mark Conversation as Read
1. Select conversation
2. Click "Mark as Read" button
3. Red unread badge disappears
4. Unread count resets to 0

### Archive a Conversation
1. Select conversation
2. Click "Archive" button
3. Conversation disappears from list
4. Can be unarchived later

### Unarchive a Conversation
1. Find archived conversation
2. Click "Unarchive" button
3. Conversation reappears in list

## 📱 What You Can See

### Incoming Messages
- Text messages
- Images with preview
- Documents with download link
- Audio messages with player
- Video messages with player
- Location data (shared locations)

### Outgoing Messages
- Text messages
- Timestamps
- Status (pending, sent, delivered, read)

## 🔐 Security

- Login required to access
- CSRF protection on all forms
- Input validation on messages
- Authenticated API calls only

## 🎨 User Interface

### Dark Mode
- Automatically detects system preference
- Applies dark colors to all elements
- Gray backgrounds in dark mode
- Light text in dark mode

### Responsive Design
- Works on desktop
- Optimized for tablet
- Can be extended for mobile

## 🐛 Troubleshooting

### Conversations Not Loading
- Check you're logged in
- Check account is configured
- Check database has conversations

### Messages Not Showing
- Select a conversation first
- Check messages exist in database
- Check page loads properly

### Send Button Not Working
- Check message is not empty
- Check you're logged in
- Check form submission works

## 💡 Tips

1. **Unread Badge**: Red badge shows unread message count
2. **Blue Messages**: Your sent messages appear in blue
3. **Gray Messages**: Incoming messages appear in gray
4. **Timestamps**: Shows relative time (2 hours ago)
5. **Status**: Outgoing messages show delivery status

## 📞 Example Conversation

```
User visits: http://localhost/admin/conversations

Left Panel:
- Lists all conversations
- Shows: John (+62812345678)
         Alice (+62823456789)
         Bob (+62834567890)

User clicks on John's conversation:

Right Panel Shows:
- Contact: John
- Number: +62812345678
- 2 unread messages

Messages:
[Gray] "Hi there!" (just now)
[Gray] "How are you?" (1 minute ago)
[Blue] "Hello John!" (sent, 2 minutes ago)

Actions:
[Mark as Read] [Archive]
[Type message...] [Send]
```

## 🔗 Related Files

- **View**: `resources/views/admin/conversations/index.blade.php`
- **Controller**: `app/Http/Controllers/Admin/ConversationController.php`
- **Routes**: `routes/web.php`
- **Layout**: `resources/views/layouts/app.blade.php`

## 🚀 Next Steps

1. Access `/admin/conversations`
2. Send a test message via WhatsApp API
3. Refresh page
4. See message in admin panel
5. Send reply from admin panel
6. Verify message received on WhatsApp

---

**Ready to use! 🎉**
