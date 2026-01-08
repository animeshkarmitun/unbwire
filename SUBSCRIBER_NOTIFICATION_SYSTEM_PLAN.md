# Subscriber Notification System Implementation Plan

## Overview
This document outlines a comprehensive plan for implementing a notification system that sends notifications to all subscribers when news is published. The system will support both in-app notifications and email notifications, with granular control for admins and individual subscriber preferences.

---

## 📋 Requirements Summary

1. **All subscribers receive notifications** when news is published
2. **Subscribers can mark notifications as "read"**
3. **Subscribers can view a list of notifications**
4. **Admins can control** whether subscribers receive news via email when publishing
5. **Each subscriber has individual email preferences** (can opt-in/out of email notifications)

---

## 🗄️ Database Schema

### 1. Update `subscribers` Table
Add new columns to support email preferences, notification tracking, and subscription linking:

```sql
ALTER TABLE subscribers ADD COLUMN:
- user_id BIGINT UNSIGNED NULL (links to users table if subscriber has account)
- email_notifications_enabled BOOLEAN DEFAULT TRUE
- language_preference VARCHAR(2) DEFAULT NULL (e.g., 'en', 'bn')
- created_at TIMESTAMP
- updated_at TIMESTAMP
- last_notified_at TIMESTAMP NULL
- unsubscribe_token VARCHAR(64) UNIQUE NULL (for unsubscribe links)

FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
INDEX idx_user_id (user_id)
INDEX idx_email (email)
```

**Note:** The `user_id` field links subscribers to user accounts (if they have one). This allows the system to check subscription tier for filtering notifications. Subscribers without user accounts are treated as "free tier" subscribers.

### 2. Create `subscriber_notifications` Table
Store in-app notifications for subscribers:

```sql
CREATE TABLE subscriber_notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    subscriber_id BIGINT UNSIGNED NOT NULL,
    news_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type VARCHAR(50) DEFAULT 'news_published', -- news_published, breaking_news, etc.
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    email_sent BOOLEAN DEFAULT FALSE,
    email_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    INDEX idx_subscriber_unread (subscriber_id, is_read),
    INDEX idx_created_at (created_at)
);
```

### 3. Create `news_publish_settings` Table (Optional - for admin control)
Store admin preferences for each news publication:

```sql
CREATE TABLE news_publish_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    news_id BIGINT UNSIGNED NOT NULL UNIQUE,
    send_email_notifications BOOLEAN DEFAULT TRUE,
    send_to_all_subscribers BOOLEAN DEFAULT TRUE,
    send_only_to_language_match BOOLEAN DEFAULT FALSE,
    send_only_to_premium_subscribers BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
);
```

**Alternative Approach (Simpler)**: Add columns directly to `news` table:
- `send_email_to_subscribers` BOOLEAN DEFAULT TRUE
- `notify_subscribers` BOOLEAN DEFAULT TRUE

---

## 🏗️ Architecture Components

### 1. Models

#### Update `Subscriber` Model
- Add relationships: 
  - `notifications()` - hasMany SubscriberNotification
  - `unreadNotifications()` - hasMany SubscriberNotification (unread)
  - `user()` - belongsTo User (nullable) - links to user account if exists
- Add methods: 
  - `shouldReceiveEmail()` - Check if email notifications enabled
  - `markNotificationAsRead()` - Mark notification as read
  - `getUnreadCount()` - Get count of unread notifications
  - `getSubscriptionTier()` - Get subscription tier slug (from linked user or 'free')
  - `getSubscriptionTierLevel()` - Get subscription tier level (0=free, 1=lite, 2=pro, 3=ultra)
  - `canAccessNews(News $news)` - Check if subscriber can access specific news
    - Matches `User::canAccessNews()` logic
    - Checks tier level: `packageTier >= requiredTier`
    - Explicitly checks `is_exclusive` requires ultra tier (level 3)
  - `hasActiveSubscription()` - Check if linked user has active subscription
  - `currentPackage()` - Get current subscription package (via linked user)
- Add email preference methods

#### Create `SubscriberNotification` Model
- Relationships: `subscriber()`, `news()`
- Scopes: `unread()`, `read()`, `recent()`
- Methods: `markAsRead()`, `markAsUnread()`

#### Update `News` Model
- Add relationship: `subscriberNotifications()`
- Add method: `shouldNotifySubscribers()`
- Add event observers or use model events

### 2. Events & Listeners

#### Create `NewsPublished` Event
- Fires when news is published (status=1 AND is_approved=1)
- Contains news data

#### Create `SendSubscriberNotifications` Listener
- Listens to `NewsPublished` event
- Creates notifications for all eligible subscribers
- Sends emails based on admin settings and subscriber preferences
- Uses queue for performance

### 3. Jobs (Queue)

#### Create `SendSubscriberNotificationJob`
- Handles individual subscriber notification
- Creates database notification record
- Sends email if enabled and subscriber preference allows
- Handles failures gracefully

#### Create `BulkNotifySubscribersJob`
- Processes notifications in batches
- Prevents memory issues with large subscriber lists
- Implements rate limiting for email sending

### 4. Mail Classes

#### Create `NewsPublishedMail` Mailable
- Email template for news publication
- **Subscription-aware content rendering:**
  - Full content for subscribers with appropriate tier
  - Filtered content based on subscription (images, videos, exclusive)
  - Upgrade prompts for premium content
- Includes news title, content (full or excerpt), images (if allowed), videos (if allowed)
- Includes unsubscribe link
- Supports both languages (en/bn)
- **Constructor parameters:**
  - `Subscriber $subscriber` - The subscriber receiving the email
  - `News $news` - The news article
  - Determines subscription tier and content access automatically

### 5. Services

#### Create `SubscriberNotificationService`
- Main service for handling notification logic
- Methods:
  - `notifySubscribers(News $news, array $options = [])`
  - `getEligibleSubscribers(News $news, array $filters = [])`
  - `createNotification(Subscriber $subscriber, News $news)`
  - `sendEmailNotification(Subscriber $subscriber, News $news)`
    - Creates `NewsPublishedMail` with subscriber and news
    - Email automatically includes full content based on subscription tier
    - Respects subscription access (images, videos, exclusive content)
  - `markAsRead(SubscriberNotification $notification)`
  - `getUnreadCount(Subscriber $subscriber)`
  - `getEmailContentForSubscriber(Subscriber $subscriber, News $news)`
    - Helper method to determine what content to include in email
    - Returns array with flags: `includeFullContent`, `includeImages`, `includeVideos`, `includeExclusive`

---

## 🔄 Workflow & Logic

### News Publishing Flow

1. **Admin publishes news** (via `store()`, `update()`, or `approveNews()`)
2. **Check if news is published**:
   - `status = 1` AND `is_approved = 1`
   - If news transitions from unpublished to published, trigger notification
3. **Fire `NewsPublished` event**
4. **Listener processes event**:
   - Check admin setting: `send_email_to_subscribers` (from news or settings table)
   - **Get eligible subscribers based on subscription access:**
     - **Primary Filter: Subscription Tier Access**
       - Only include subscribers who can access this news based on their subscription
       - Subscribers with user accounts: Check their active subscription tier
       - Subscribers without user accounts: Only get free tier news notifications
     - Language preference (if enabled)
     - Email notification preference
   - Create `SubscriberNotification` records for all eligible subscribers
   - Queue email jobs for subscribers who have email enabled
5. **Queue processes jobs**:
   - Send emails in batches
   - Update `email_sent` and `email_sent_at` in notification records

### Subscriber Notification Viewing Flow

1. **Subscriber visits notification page** (frontend)
2. **Fetch notifications**:
   - Get all notifications for subscriber
   - Filter by read/unread
   - Paginate results
   - Include related news data (slug, title, image, excerpt)
3. **Display notifications**:
   - Show unread count badge
   - List notifications with news title, excerpt, image
   - Each notification is clickable and links to the specific news article
4. **Click notification to view news**:
   - Navigate to news article page: `/news/{slug}`
   - Automatically mark notification as read when clicked
   - Update `is_read = TRUE` and `read_at = TIMESTAMP`
   - Update unread count
   - Handle cases where news is deleted/unpublished (show appropriate message)
5. **Mark as read**:
   - Automatic: When clicking notification link
   - Manual: "Mark as read" button on notification
   - Bulk: "Mark all as read" button

---

## 👤 User Experience Flow: Clicking a Notification

### Scenario: Subscriber Clicks a Notification

**Step 1: Subscriber sees notification**
- Notification appears in notification list or dropdown
- Shows news title, thumbnail, excerpt
- Unread indicator is visible

**Step 2: Subscriber clicks notification**
- Click can happen from:
  - Notification list page (`/notifications`)
  - Notification dropdown in header
  - Email notification (if email contains link)

**Step 3: System processes click**
- Route: `GET /notifications/{id}/view`
- Controller action:
  1. Verify subscriber owns the notification
  2. Load notification with related news
  3. Check if news exists and is published
  4. Mark notification as read (if not already read)
  5. Get news slug from notification

**Step 4: Handle different scenarios**

**Scenario A: News exists and is published**
- Redirect to: `/news/{slug}`
- Existing `ShowNews()` method handles:
  - Authentication check
  - Subscription requirement check
  - Subscription tier access check
- News article displays normally
- Notification is marked as read

**Scenario B: News is deleted or unpublished**
- Show error message: "This news article is no longer available"
- Redirect back to notifications page
- Notification can be marked as read or left unread (configurable)
- Optionally show "View other notifications" link

**Scenario C: Subscriber doesn't have subscription**
- Redirect to news article page
- Existing subscription check in `ShowNews()` redirects to subscription plans
- Notification remains unread until subscriber accesses news

**Step 5: User views news article**
- Normal news article page loads
- All existing functionality works (comments, related news, etc.)
- Notification is already marked as read

### Alternative Flow: Direct News Link with Notification Tracking

**Option:** Use query parameter approach
- Link format: `/news/{slug}?notification_id={id}`
- News page checks for `notification_id` parameter
- If present, marks notification as read
- No separate redirect route needed

**Pros:**
- Simpler routing
- Direct link to news
- Works with email links

**Cons:**
- Less control over error handling
- Notification ID visible in URL

---

## 🎨 Frontend Components

### 1. Subscriber Notification Page (Frontend)
- Route: `/notifications` or `/subscriber/notifications`
- View: List of notifications
- Features:
  - Unread count badge
  - Filter: All / Unread / Read
  - Pagination
  - **Click notification to navigate to specific news article**
    - Link format: `/news/{slug}` (uses news slug from notification)
    - Automatically marks notification as read on click
    - Opens news article in same window (or new tab based on preference)
  - Notification display includes:
    - News thumbnail image
    - News title (clickable link)
    - News excerpt/preview
    - Publication date
    - Category (optional)
    - Unread indicator badge
  - Mark as read button/action (manual)
  - Mark all as read button
  - Handle deleted/unpublished news gracefully (show "News no longer available" message)

### 2. Admin News Form Enhancement
- Add checkbox/toggle: "Send email notification to subscribers"
- Default: checked (TRUE)
- Show count of active subscribers
- Option to preview email

### 3. Subscriber Email Preferences Page (Frontend)
- Route: `/subscriber/preferences` or `/unsubscribe/{token}`
- Features:
  - Toggle email notifications on/off
  - Language preference selection
  - Unsubscribe link in emails
  - Manage notification preferences

### 4. Notification Badge/Indicator (Frontend Header)
- Show unread notification count
- Dropdown with recent notifications (last 5-10)
- Each notification in dropdown:
  - Shows news title (truncated)
  - Shows news thumbnail (small)
  - Clickable - navigates to news article
  - Marks as read when clicked
- Click "View All" to go to full notification page
- Auto-refresh unread count (optional: via polling or websockets)

---

## 🔐 Subscription-Based Notification Filtering

### Core Principle
**Subscribers should only receive notifications for news articles they can access based on their subscription tier.**

### Subscriber Types

#### 1. Subscribers Without User Accounts (Free Tier)
- **Who:** Subscribers who subscribed via newsletter but don't have a registered user account
- **Access Level:** Free tier only
- **Notifications:** Only receive notifications for:
  - News with `subscription_required = 'free'`
  - News with `is_exclusive = false`
- **Identification:** `user_id IS NULL` in subscribers table

#### 2. Subscribers With User Accounts (Paid Tiers)
- **Who:** Subscribers whose email matches a registered user account
- **Access Level:** Based on their active subscription package
- **Notifications:** Receive notifications based on subscription tier:
  - **UNB Lite:** Free + Lite news
  - **UNB Pro:** Free + Lite + Pro news
  - **UNB Ultra:** All news (including exclusive)
- **Identification:** `user_id IS NOT NULL` in subscribers table
- **Linking:** Match subscriber email to user email

### Subscription Tier Mapping (Based on Current Implementation)

**Tier Levels (from SubscriptionPackage::getTierLevel()):**
- Free (no subscription): Level 0
- UNB Lite: Level 1
- UNB Pro: Level 2
- UNB Ultra: Level 3

**Access Rules (from User::canAccessNews() and scopeForSubscriptionTier()):**
- User's package tier must be >= required tier
- Exclusive content requires Ultra tier (level 3), regardless of subscription_required value

| News Type | Free (0) | Lite (1) | Pro (2) | Ultra (3) |
|-----------|----------|---------|--------|-----------|
| `subscription_required = 'free'` + `is_exclusive = false` | ✅ | ✅ | ✅ | ✅ |
| `subscription_required = 'lite'` + `is_exclusive = false` | ❌ | ✅ | ✅ | ✅ |
| `subscription_required = 'pro'` + `is_exclusive = false` | ❌ | ❌ | ✅ | ✅ |
| `subscription_required = 'ultra'` + `is_exclusive = false` | ❌ | ❌ | ❌ | ✅ |
| `is_exclusive = true` (any subscription_required) | ❌ | ❌ | ❌ | ✅ |

**Important Note:** The current `User::canAccessNews()` method checks tier level (`packageTier >= requiredTier`) but doesn't explicitly check `is_exclusive` for users with packages. However, `scopeForSubscriptionTier()` does filter out exclusive content for tiers < 3. 

**For the notification system, we should explicitly check `is_exclusive`** to ensure consistency:
- If `is_exclusive = true`, require `packageTier >= 3` (Ultra tier)
- This matches the behavior of `scopeForSubscriptionTier()` and ensures subscribers only get notifications for content they can actually access

### Linking Subscribers to Users

**Automatic Linking:**
- When a subscriber subscribes, check if email matches existing user
- When a user registers, check if email matches existing subscriber
- Background job to sync links periodically

**Manual Linking:**
- Admin can manually link subscriber to user
- Useful when email addresses don't match exactly

### Handling Subscription Changes

**Subscription Expires:**
- Subscriber's tier reverts to "free"
- Stop sending premium notifications
- Continue sending free news notifications

**Subscription Upgrades:**
- Subscriber's tier increases
- Start sending higher tier notifications
- Historical notifications remain (already sent)

**Subscription Cancelled:**
- Treat as free tier subscriber
- Only send free news notifications

### Implementation Example

```php
// In SubscriberNotificationService

public function getEligibleSubscribers(News $news): Collection
{
    // Get all active subscribers with email notifications enabled
    $subscribers = Subscriber::where('email_notifications_enabled', true)
        ->get();
    
    // Filter by subscription access
    return $subscribers->filter(function($subscriber) use ($news) {
        return $subscriber->canAccessNews($news);
    });
}

// In Subscriber Model

public function canAccessNews(News $news): bool
{
    // If subscriber has linked user account
    if ($this->user_id && $this->user) {
        $user = $this->user;
        
        // Check if user has active subscription
        if (!$user->activeSubscription) {
            // No active subscription = free tier
            return $news->subscription_required === 'free' && !$news->is_exclusive;
        }
        
        // Use user's canAccessNews method
        return $user->canAccessNews($news);
    }
    
    // Subscriber without user account = free tier
    return $news->subscription_required === 'free' && !$news->is_exclusive;
}

public function getSubscriptionTier(): string
{
    if ($this->user_id && $this->user && $this->user->activeSubscription) {
        $package = $this->user->currentPackage();
        return $package ? $package->slug : 'free';
    }
    return 'free';
}
```

---

## 🔧 Implementation Details

### 1. News Publishing Detection

**Option A: Model Events (Recommended)**
- Use `saved` event on News model
- Check if status changed to 1 AND is_approved changed to 1
- Fire `NewsPublished` event

**Option B: Controller Logic**
- Add notification logic in `store()`, `update()`, and `approveNews()` methods
- Check conditions before firing event

**Option C: Observer Pattern**
- Create `NewsObserver`
- Listen to model events
- Handle notification logic

### 2. Subscriber Eligibility Logic

**Key Requirement:** Subscribers should only receive notifications for news they can access based on their subscription tier.

```php
function getEligibleSubscribers(News $news) {
    $query = Subscriber::query();
    
    // Email preference filter
    $query->where('email_notifications_enabled', true);
    
    // Language filter (if enabled in settings)
    if ($news->send_only_to_language_match) {
        $query->where(function($q) use ($news) {
            $q->where('language_preference', $news->language)
              ->orWhereNull('language_preference');
        });
    }
    
    // **SUBSCRIPTION-BASED FILTERING** - Most Important
    // Only send notifications to subscribers who can access this news
    $query->where(function($q) use ($news) {
        // Check if subscriber can access this news based on subscription
        $q->whereHas('user', function($userQuery) use ($news) {
            // Subscriber has linked user account
            $userQuery->whereHas('activeSubscription', function($subQuery) use ($news) {
                // User has active subscription
                $package = $subQuery->package;
                if ($package) {
                    $packageTier = $package->getTierLevel();
                    $requiredTier = $this->getRequiredTierLevel($news);
                    
                    // User's tier must be >= required tier
                    if ($requiredTier > 0) {
                        $subQuery->whereHas('package', function($pkgQuery) use ($requiredTier) {
                            $pkgQuery->whereRaw('CASE 
                                WHEN slug = "unb-ultra" THEN 3
                                WHEN slug = "unb-pro" THEN 2
                                WHEN slug = "unb-lite" THEN 1
                                ELSE 0
                            END >= ?', [$requiredTier]);
                        });
                    }
                    
                    // Exclusive content requires Ultra tier
                    if ($news->is_exclusive) {
                        $subQuery->whereHas('package', function($pkgQuery) {
                            $pkgQuery->where('slug', 'unb-ultra');
                        });
                    }
                }
            });
        })->orWhere(function($q) use ($news) {
            // Subscriber has NO linked user account (free tier)
            // Only send free news notifications
            $q->whereNull('user_id')
              ->where('subscription_required', 'free')
              ->where('is_exclusive', false);
        });
    });
    
    return $query->get();
}

// Helper method to get required tier level for news
function getRequiredTierLevel(News $news): int {
    if ($news->is_exclusive) {
        return 3; // Ultra tier required
    }
    
    return match($news->subscription_required) {
        'free' => 0,
        'lite' => 1,
        'pro' => 2,
        'ultra' => 3,
        default => 0,
    };
}
```

**Recommended Simplified Approach (Matches Current Implementation):**

```php
// In SubscriberNotificationService

public function getEligibleSubscribers(News $news): Collection
{
    $subscribers = Subscriber::where('email_notifications_enabled', true)
        ->get()
        ->filter(function($subscriber) use ($news) {
            // Check if subscriber can access this news
            return $subscriber->canAccessNews($news);
        });
    
    return $subscribers;
}

// In Subscriber Model (matches User::canAccessNews() logic):

public function canAccessNews(News $news): bool
{
    // If subscriber has linked user account
    if ($this->user_id && $this->user) {
        $user = $this->user;
        $package = $user->currentPackage();
        
        // No active subscription = free tier
        if (!$package) {
            return $news->subscription_required === 'free' && !$news->is_exclusive;
        }
        
        // Use same logic as User::canAccessNews()
        $packageTier = $package->getTierLevel(); // Returns: 0=free, 1=lite, 2=pro, 3=ultra
        $requiredTier = match($news->subscription_required) {
            'free' => 0,
            'lite' => 1,
            'pro' => 2,
            'ultra' => 3,
            default => 0,
        };
        
        // User's package tier must be >= required tier
        $tierAccess = $packageTier >= $requiredTier;
        
        // Exclusive content requires ultra tier (level 3)
        // This ensures consistency with scopeForSubscriptionTier()
        if ($news->is_exclusive && $packageTier < 3) {
            return false;
        }
        
        return $tierAccess;
    }
    
    // Subscriber without user account = free tier
    // Can only access free, non-exclusive news
    return $news->subscription_required === 'free' && !$news->is_exclusive;
}

public function getSubscriptionTier(): string
{
    if ($this->user_id && $this->user) {
        $package = $this->user->currentPackage();
        return $package ? $package->slug : 'free';
    }
    return 'free';
}

public function getSubscriptionTierLevel(): int
{
    if ($this->user_id && $this->user) {
        $package = $this->user->currentPackage();
        return $package ? $package->getTierLevel() : 0;
    }
    return 0; // Free tier
}

public function getSubscriptionTier(): string {
    if ($this->user_id && $this->user) {
        $package = $this->user->currentPackage();
        return $package ? $package->slug : 'free';
    }
    return 'free';
}
```

### 3. Email Sending Strategy

**Batch Processing:**
- Process subscribers in batches of 100-500
- Use Laravel queues
- Implement rate limiting (e.g., 100 emails/minute)
- Handle failures with retry logic

**Email Template:**
- Responsive design
- **Subscription-based content:**
  - Full news content for subscribers with appropriate tier
  - Images for Lite+ subscribers
  - Videos for Pro+ subscribers
  - Exclusive content for Ultra subscribers
  - Excerpt + upgrade prompts for free tier
- Include news image (if subscription allows), title, content (full or excerpt)
- **"Read More" button linking directly to news article** (`/news/{slug}`)
- **Alternative: Include notification tracking link** (`/notifications/{id}/view` or `/news/{slug}?notification_id={id}`)
- Unsubscribe link with token
- Support for both languages (en/bn)
- **Email links should mark notification as read** when clicked (if using notification tracking)
- **Upgrade prompts** for premium content (if subscriber doesn't have access)

### 4. Notification Marking as Read

**Automatic:**
- Mark as read when subscriber clicks notification link to view news
- Mark as read when subscriber views news article directly (optional - via middleware or event)
- Mark as read when notification is clicked in notification dropdown/badge

**Manual:**
- "Mark as read" button on notification
- "Mark all as read" button

**Implementation:**
- Create route: `POST /notifications/{id}/mark-read` or `GET /notifications/{id}/read`
- Create route: `GET /notifications/{id}/view` - marks as read and redirects to news
- Alternative: Use query parameter on news route: `/news/{slug}?notification_id={id}` to mark as read

### 5. News Article Access from Notification

**Important Considerations:**
- Subscribers may not have active subscriptions
- News may require specific subscription tiers
- News may be deleted or unpublished after notification is sent
- Need to handle access control gracefully

**Access Flow:**
1. Subscriber clicks notification
2. System checks if news exists and is published
3. If news is deleted/unpublished:
   - Show error message: "This news article is no longer available"
   - Redirect back to notifications page
   - Optionally mark notification as read anyway
4. If news exists:
   - Check subscriber's subscription status (if required)
   - Redirect to news article: `/news/{slug}`
   - Mark notification as read
   - News page loads normally with subscription checks

**Note:** The existing `ShowNews()` method in `HomeController` already handles:
- Authentication check
- Subscription requirement check
- Subscription tier access check
- News availability check

So the notification click handler just needs to:
- Mark notification as read
- Redirect to the news route
- Let the existing news controller handle access control

### 5. Notification Click Handler

**Route Options:**

**Option A: Direct Link with Read Tracking (Recommended)**
```
GET /notifications/{id}/view
- Marks notification as read
- Redirects to /news/{slug}
- Handles missing/deleted news gracefully
```

**Option B: Query Parameter on News Route**
```
GET /news/{slug}?notification_id={id}
- News page checks for notification_id parameter
- Marks notification as read if parameter exists
- Shows news article normally
```

**Option C: AJAX + Redirect**
```
POST /notifications/{id}/mark-read (AJAX)
- Marks notification as read
- Returns success response
- Frontend redirects to /news/{slug}
```

**Controller Logic:**
```php
public function viewNotification($id) {
    $notification = SubscriberNotification::findOrFail($id);
    
    // Verify subscriber owns this notification
    if ($notification->subscriber_id != auth()->user()->id) {
        abort(403);
    }
    
    // Mark as read
    if (!$notification->is_read) {
        $notification->markAsRead();
    }
    
    // Get news slug
    $news = $notification->news;
    
    // Handle deleted/unpublished news
    if (!$news || $news->status != 1 || $news->is_approved != 1) {
        return redirect()->route('notifications.index')
            ->with('error', 'This news article is no longer available.');
    }
    
    // Redirect to news article
    return redirect()->route('news-details', $news->slug);
}
```

---

## 📧 Email Notification Features

### Email Content (Subscription-Based)

**Key Feature:** Subscribers receive **full news content** in emails based on their subscription tier. The email content is filtered to match what they can access on the website.

#### Email Content Structure

**For All Subscribers:**
- **Subject**: "[Site Name] New Article: {News Title}"
- News title
- News publication date
- Category name
- Author name
- Unsubscribe link
- Site branding

**Content Based on Subscription Tier:**

**Free Tier Subscribers (No User Account or No Subscription):**
- News excerpt/summary (first 200-300 characters)
- **No images** (or placeholder image)
- **No videos**
- **No exclusive content**
- "Read More" button/link to full article
- "Subscribe to get full access" message

**UNB Lite Subscribers:**
- **Full news content** (complete article text)
- **News images** (thumbnails and full images)
- **No videos** (video placeholder with "Upgrade to Pro" message)
- **No exclusive content**
- "Read More" button/link
- "Upgrade to Pro for video access" message (if news has video)

**UNB Pro Subscribers:**
- **Full news content** (complete article text)
- **News images** (thumbnails and full images)
- **Video content** (embedded or linked)
- **No exclusive content**
- "Read More" button/link
- "Upgrade to Ultra for exclusive content" message (if news is exclusive)

**UNB Ultra Subscribers:**
- **Full news content** (complete article text)
- **News images** (thumbnails and full images)
- **Video content** (embedded or linked)
- **Exclusive content** (full access)
- "Read More" button/link

#### Email Template Logic

```php
// In NewsPublishedMail Mailable

public function __construct(Subscriber $subscriber, News $news)
{
    $this->subscriber = $subscriber;
    $this->news = $news;
    
    // Get subscriber's subscription tier
    $this->subscriptionTier = $subscriber->getSubscriptionTierLevel();
    $this->package = $subscriber->user ? $subscriber->user->currentPackage() : null;
    
    // Determine what content to include
    $this->includeFullContent = $this->subscriptionTier >= 1; // Lite and above
    $this->includeImages = $this->subscriptionTier >= 1; // Lite and above
    $this->includeVideos = $this->subscriptionTier >= 2; // Pro and above
    $this->includeExclusive = $this->subscriptionTier >= 3; // Ultra only
    
    // Check if subscriber can access this specific news
    $this->canAccessNews = $subscriber->canAccessNews($news);
}
```

#### Email Template View Structure

```blade
{{-- resources/views/mail/news-published.blade.php --}}

@if($canAccessNews)
    {{-- Full content based on subscription tier --}}
    
    @if($includeFullContent)
        {{-- Show full article content --}}
        <div class="news-content">
            {!! $news->content !!}
        </div>
    @else
        {{-- Show excerpt only for free tier --}}
        <div class="news-excerpt">
            {{ Str::limit(strip_tags($news->content), 300) }}
        </div>
        <p><em>Subscribe to read the full article</em></p>
    @endif
    
    @if($includeImages && $news->image)
        {{-- Show images for Lite+ subscribers --}}
        <img src="{{ asset($news->image) }}" alt="{{ $news->title }}" />
    @elseif($news->image)
        {{-- Show placeholder for free tier --}}
        <div class="image-placeholder">
            <p>Image available with subscription</p>
        </div>
    @endif
    
    @if($includeVideos && $news->video_url)
        {{-- Show video for Pro+ subscribers --}}
        <div class="video-container">
            <a href="{{ $news->video_url }}">Watch Video</a>
        </div>
    @elseif($news->video_url)
        {{-- Show upgrade message for lower tiers --}}
        <div class="video-upgrade">
            <p>Video content available with Pro subscription</p>
            <a href="{{ route('subscription.plans') }}">Upgrade Now</a>
        </div>
    @endif
    
    @if($news->is_exclusive && !$includeExclusive)
        {{-- Show upgrade message for exclusive content --}}
        <div class="exclusive-upgrade">
            <p>This is exclusive content. Upgrade to Ultra to access.</p>
            <a href="{{ route('subscription.plans') }}">Upgrade to Ultra</a>
        </div>
    @endif
    
@else
    {{-- Subscriber doesn't have access to this news --}}
    <div class="access-denied">
        <p>This content requires a higher subscription tier.</p>
        <a href="{{ route('subscription.plans') }}">View Subscription Plans</a>
    </div>
@endif
```

### Email Preferences
- Subscriber can:
  - Enable/disable email notifications
  - Set language preference
  - Choose email format:
    - **Full content** (if they have subscription)
    - **Excerpt only** (preview mode)
  - Unsubscribe completely
  - Receive only breaking news emails
  - Receive daily/weekly digest (future enhancement)

---

## 🔐 Security & Privacy

1. **Unsubscribe Token**:
   - Generate unique token for each subscriber
   - Use in unsubscribe links
   - Validate token before unsubscribing

2. **Email Validation**:
   - Validate email addresses before sending
   - Handle bounce emails (future enhancement)

3. **Rate Limiting**:
   - Limit email sending rate
   - Prevent spam/abuse

4. **Privacy**:
   - Don't expose subscriber emails in frontend
   - Secure notification access

---

## 📊 Admin Features

### 1. Notification Settings (Global)
- Default email notification setting
- Language-based filtering default
- Premium subscriber filtering default
- Email sending rate limit

### 2. Per-News Control
- Toggle email notification when publishing
- Override global settings per news item
- Preview email before sending

### 3. Notification Statistics
- Total notifications sent
- Email delivery rate
- Unread notification count
- Most notified subscribers

### 4. Subscriber Management
- View subscriber email preferences
- Manually send notification to specific subscribers
- Export subscriber list

---

## 🚀 Implementation Phases

### Phase 1: Database & Models (Week 1)
- [ ] Create migrations for `subscriber_notifications` table
- [ ] Update `subscribers` table migration
  - [ ] Add `user_id` column (nullable, foreign key to users)
  - [ ] Add email preferences columns
  - [ ] Add indexes for performance
- [ ] Create `SubscriberNotification` model
- [ ] Update `Subscriber` model with relationships and methods
  - [ ] Add `user()` relationship
  - [ ] Add `canAccessNews(News $news)` method
  - [ ] Add `getSubscriptionTier()` method
  - [ ] Add `hasActiveSubscription()` method
- [ ] Update `News` model with relationships
- [ ] **Create service method to link subscribers to users** (match by email)

### Phase 2: Core Notification System (Week 1-2)
- [ ] Create `NewsPublished` event
- [ ] Create `SendSubscriberNotifications` listener
- [ ] Create `SubscriberNotificationService`
- [ ] **Implement subscription-based filtering logic**
  - [ ] Filter subscribers by subscription tier access
  - [ ] Handle subscribers with user accounts
  - [ ] Handle subscribers without user accounts (free tier)
- [ ] Implement notification creation logic
- [ ] Add event firing in NewsController
- [ ] **Test subscription filtering with different news types** (free, lite, pro, ultra, exclusive)

### Phase 3: Email System (Week 2)
- [ ] Create `NewsPublishedMail` mailable
  - [ ] Add subscription-aware content logic
  - [ ] Determine content access based on subscriber tier
- [ ] Create email template view
  - [ ] Full content template for Lite+ subscribers
  - [ ] Excerpt template for free tier subscribers
  - [ ] Image rendering (conditional based on tier)
  - [ ] Video rendering (conditional based on tier)
  - [ ] Exclusive content handling
  - [ ] Upgrade prompts for premium content
- [ ] Create `SendSubscriberNotificationJob`
  - [ ] Pass subscriber and news to mailable
  - [ ] Handle subscription-based content filtering
- [ ] Create `BulkNotifySubscribersJob`
- [ ] Implement queue processing
- [ ] Add unsubscribe functionality
- [ ] **Test email content for different subscription tiers**

### Phase 4: Frontend - Subscriber Features (Week 2-3)
- [ ] Create notification listing page
- [ ] Create notification detail view
- [ ] **Implement notification click handler** (navigate to news article)
- [ ] **Create route for viewing notification** (`/notifications/{id}/view`)
- [ ] **Implement automatic mark-as-read on click**
- [ ] Implement manual mark as read functionality
- [ ] Add notification badge/indicator with dropdown
- [ ] **Make dropdown notifications clickable** (navigate to news)
- [ ] Create email preferences page
- [ ] Add unsubscribe page
- [ ] Handle deleted/unpublished news gracefully

### Phase 5: Admin Features (Week 3)
- [ ] Add email notification toggle in news form
- [ ] Create notification settings page
- [ ] Add notification statistics dashboard
- [ ] Implement preview email feature

### Phase 6: Testing & Optimization (Week 3-4)
- [ ] Test notification creation
- [ ] Test email sending with large subscriber lists
- [ ] Test unsubscribe functionality
- [ ] Optimize database queries
- [ ] Implement caching for unread counts
- [ ] Performance testing

### Phase 7: Documentation & Deployment (Week 4)
- [ ] Write API documentation
- [ ] Create user guide
- [ ] Deploy to staging
- [ ] User acceptance testing
- [ ] Deploy to production

---

## 🧪 Testing Considerations

### Unit Tests
- Subscriber eligibility logic
- Notification creation
- Email preference checks
- Mark as read functionality

### Integration Tests
- News publishing triggers notifications
- **Subscription-based filtering works correctly**
  - Free news → All subscribers get notification
  - Lite news → Only Lite+ subscribers get notification
  - Pro news → Only Pro+ subscribers get notification
  - Ultra/Exclusive news → Only Ultra subscribers get notification
- **Subscribers without user accounts only get free news notifications**
- **Subscribers with user accounts get notifications based on their subscription tier**
- **Email content filtering works correctly**
  - Free tier subscribers receive excerpt only (no images/videos)
  - Lite tier subscribers receive full content + images (no videos)
  - Pro tier subscribers receive full content + images + videos (no exclusive)
  - Ultra tier subscribers receive full content + images + videos + exclusive
- Email sending works correctly
- Unsubscribe functionality
- Notification listing and filtering

### Performance Tests
- Large subscriber list handling (10,000+ subscribers)
- Email sending rate
- Database query optimization
- Queue processing speed

---

## 🔄 Future Enhancements

1. **Notification Types**:
   - Breaking news notifications
   - Category-specific notifications
   - Author-specific notifications
   - Tag-based notifications

2. **Email Digest**:
   - Daily digest of all news
   - Weekly summary
   - Customizable digest frequency

3. **Push Notifications**:
   - Browser push notifications
   - Mobile app push notifications

4. **Advanced Preferences**:
   - Category preferences
   - Author preferences
   - Frequency preferences (immediate, daily, weekly)

5. **Analytics**:
   - Email open rates
   - Click-through rates
   - Notification engagement metrics

6. **A/B Testing**:
   - Test different email templates
   - Test notification timing
   - Test subject lines

---

## 📝 Code Structure

```
app/
├── Models/
│   ├── Subscriber.php (updated)
│   ├── SubscriberNotification.php (new)
│   └── News.php (updated)
├── Events/
│   └── NewsPublished.php (new)
├── Listeners/
│   └── SendSubscriberNotifications.php (new)
├── Jobs/
│   ├── SendSubscriberNotificationJob.php (new)
│   └── BulkNotifySubscribersJob.php (new)
├── Mail/
│   └── NewsPublishedMail.php (new)
│       - Subscription-aware email content
│       - Determines content access based on subscriber tier
│       - Filters images, videos, exclusive content
│       - Constructor: (Subscriber $subscriber, News $news)
├── Services/
│   └── SubscriberNotificationService.php (new)
└── Http/
    └── Controllers/
        ├── Frontend/
        │   ├── SubscriberNotificationController.php (new)
        │   │   - index() - List all notifications
        │   │   - view($id) - View notification and redirect to news
        │   │   - markAsRead($id) - Mark notification as read
        │   │   - markAllAsRead() - Mark all as read
        │   │   - getUnreadCount() - Get unread count (AJAX)
        │   └── SubscriberPreferencesController.php (new)
        └── Admin/
            └── NewsController.php (updated)

database/
└── migrations/
    ├── xxxx_add_columns_to_subscribers_table.php (new)
    ├── xxxx_create_subscriber_notifications_table.php (new)
    └── xxxx_add_notification_settings_to_news_table.php (new)

resources/
└── views/
    ├── frontend/
    │   ├── notifications/
    │   │   ├── index.blade.php (new)
    │   │   │   - List of notifications with clickable links
    │   │   │   - Each notification links to /notifications/{id}/view
    │   │   │   - Shows news image, title, excerpt
    │   │   │   - Unread indicator
    │   │   └── partials/
    │   │       └── notification-item.blade.php (new)
    │   │           - Reusable notification item component
    │   ├── components/
    │   │   └── notification-dropdown.blade.php (new)
    │   │       - Header notification badge/dropdown
    │   │       - Recent notifications list
    │   │       - Clickable items linking to news
    │   └── subscriber/
    │       └── preferences.blade.php (new)
    └── mail/
        └── news-published.blade.php (new)
            - Subscription-aware template
            - Conditional content rendering:
              * Full content vs excerpt
              * Images (conditional)
              * Videos (conditional)
              * Exclusive content (conditional)
              * Upgrade prompts
            - Conditional content rendering:
              * Full content vs excerpt
              * Images (conditional)
              * Videos (conditional)
              * Exclusive content (conditional)
              * Upgrade prompts

routes/
└── web.php (updated - add notification routes)
    - GET /notifications - List notifications
    - GET /notifications/{id}/view - View notification and redirect to news
    - POST /notifications/{id}/mark-read - Mark as read (AJAX)
    - POST /notifications/mark-all-read - Mark all as read
    - GET /notifications/unread-count - Get unread count (AJAX)
    - GET /subscriber/preferences - Email preferences page
```

---

## ⚠️ Important Considerations

1. **Subscription Access Control**:
   - **CRITICAL:** Only send notifications to subscribers who can actually access the news
   - Link subscribers to user accounts by email matching
   - Handle subscribers without user accounts (treat as free tier)
   - When subscription expires, subscriber should stop receiving premium notifications
   - When subscription upgrades, subscriber should start receiving higher tier notifications
   - Consider creating a background job to sync subscriber-user links periodically

2. **Performance**:
   - Use queues for email sending
   - Batch process large subscriber lists
   - Implement database indexing (especially on user_id, email)
   - Cache unread counts
   - Optimize subscription tier checking queries

2. **Scalability**:
   - Design for thousands of subscribers
   - Use job queues (Redis/Database)
   - Consider email service providers (Mailgun, SendGrid) for high volume

3. **User Experience**:
   - Don't block news publishing while sending notifications
   - Show progress/status for bulk operations
   - Provide clear unsubscribe options

4. **Compliance**:
   - Follow email marketing best practices
   - Include unsubscribe links
   - Respect subscriber preferences
   - Handle GDPR requirements (if applicable)

5. **Error Handling**:
   - Log failed email sends
   - Retry failed notifications
   - Handle invalid email addresses
   - Graceful degradation if email service fails

---

## 📚 Dependencies

- Laravel Queues (already available)
- Laravel Mail (already available)
- Laravel Events & Listeners (already available)
- Database (MySQL/PostgreSQL)
- Queue driver (Redis/Database recommended)
- **Subscription System** (already implemented)
  - `User` model with:
    - `activeSubscription()` - Get active subscription relationship
    - `currentPackage()` - Get current SubscriptionPackage
    - `canAccessNews(News $news)` - Check access based on tier level
  - `SubscriptionPackage` model with:
    - `slug` field: 'unb-lite', 'unb-pro', 'unb-ultra'
    - `getTierLevel()` method: Returns 0 (free), 1 (lite), 2 (pro), 3 (ultra)
    - `hasAccess($feature)` method: Check feature access
  - `UserSubscription` model with:
    - `isActive()` method: Check if subscription is active
    - `package()` relationship: Get SubscriptionPackage
  - `News` model with:
    - `subscription_required` enum: 'free', 'lite', 'pro', 'ultra'
    - `is_exclusive` boolean: Exclusive content flag
    - `scopeForSubscriptionTier($tier)` - Filter news by tier

---

## 🎯 Success Criteria

1. ✅ **Subscribers only receive notifications for news they can access based on subscription tier**
   - Free tier subscribers (no user account) → Only free news notifications
   - Lite tier subscribers → Free + Lite news notifications
   - Pro tier subscribers → Free + Lite + Pro news notifications
   - Ultra tier subscribers → All news notifications (including exclusive)
2. ✅ Subscribers can view and manage their notifications
3. ✅ Subscribers can mark notifications as read
4. ✅ **Subscribers can click notifications to navigate to the specific news article**
5. ✅ **Notifications are automatically marked as read when clicked**
6. ✅ **System handles deleted/unpublished news gracefully**
7. ✅ Admins can control email notifications per news item
8. ✅ Subscribers can manage their email preferences
9. ✅ System handles large subscriber lists efficiently
10. ✅ Email notifications are sent reliably
11. ✅ Unsubscribe functionality works correctly
12. ✅ **Subscribers are correctly linked to user accounts (if they have one)**
13. ✅ **Subscription tier changes are reflected in notification filtering**

---

## 📞 Support & Maintenance

- Monitor email delivery rates
- Track notification engagement
- Handle bounce emails
- Update email templates based on performance
- Regular database cleanup (old notifications)
- Monitor queue performance
- **Sync subscriber-user links periodically** (background job)
  - Match subscribers to users by email
  - Update user_id when subscriber email matches user email
  - Handle cases where user account is created after subscription
- **Monitor subscription tier changes**
  - When subscription expires, stop sending premium notifications
  - When subscription upgrades, start sending higher tier notifications
  - Handle subscription cancellations

---

**End of Plan**
