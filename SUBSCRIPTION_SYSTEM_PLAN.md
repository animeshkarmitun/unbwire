# UNB News Subscription Package System - Implementation Plan

## Overview
This document outlines the complete subscription package system implementation for UNB News Portal, featuring three subscription tiers: UNB Lite, UNB Pro, and UNB Ultra.

## Root Cause Analysis

### Why Subscription System is Needed
1. **Monetization**: Transform free content into a revenue-generating model
2. **Content Tiering**: Differentiate content access levels to create value propositions
3. **User Segmentation**: Categorize users based on engagement and willingness to pay
4. **Content Protection**: Control access to premium/exclusive content

### Technical Approach
The system implements a **role-based access control (RBAC) pattern** combined with **subscription lifecycle management**, following Laravel best practices for:
- Database normalization (separate packages, subscriptions, and users)
- Model relationships (Eloquent ORM)
- Middleware-based access control
- Query scoping for content filtering

## Architecture

### Database Structure

#### 1. `subscription_packages` Table
- Stores package definitions (UNB Lite, Pro, Ultra)
- Contains access permissions (news, images, videos, exclusive)
- Features (ad-free, priority support)
- Pricing and billing information

#### 2. `user_subscriptions` Table
- Tracks user subscription history
- Manages subscription lifecycle (active, expired, cancelled)
- Payment tracking
- Auto-renewal settings

#### 3. `news` Table Updates
- `is_exclusive`: Boolean flag for exclusive content
- `video_url`: Optional video content URL
- `subscription_required`: Enum (free, lite, pro, ultra)

### Access Control Logic

```
Free Tier (Default):
├── Access: Basic news only
└── Restrictions: No images, videos, or exclusive content

UNB Lite:
├── Access: News + Images
├── Price: $9.99/month
└── Restrictions: No videos or exclusive content

UNB Pro:
├── Access: News + Images + Videos
├── Price: $19.99/month
├── Features: Ad-free, Priority Support
└── Restrictions: No exclusive content

UNB Ultra:
├── Access: Everything (News + Images + Videos + Exclusive)
├── Price: $29.99/month
├── Features: Ad-free, Priority Support
└── Restrictions: None
```

## Implementation Details

### 1. Models & Relationships

**SubscriptionPackage Model:**
- `hasMany(UserSubscription)` - Package has many subscriptions
- `hasAccess($feature)` - Check feature access
- `getTierLevel()` - Get numeric tier level for comparison

**UserSubscription Model:**
- `belongsTo(User)` - Subscription belongs to user
- `belongsTo(SubscriptionPackage)` - Subscription belongs to package
- `isActive()` - Check if subscription is currently active
- `daysRemaining()` - Calculate days until expiration

**User Model Extensions:**
- `hasMany(UserSubscription)` - User has many subscriptions
- `activeSubscription()` - Get current active subscription
- `currentPackage()` - Get current subscription package
- `hasSubscriptionAccess($feature)` - Check feature access
- `canAccessNews($news)` - Check if user can access specific news

**News Model Extensions:**
- `scopeForSubscriptionTier()` - Filter news by subscription tier
- `requiresSubscription()` - Check if news requires subscription
- `hasVideo()` - Check if news has video content

### 2. Middleware

**SubscriptionAccess Middleware:**
- Validates user authentication
- Checks subscription access for specific features
- Redirects to subscription plans page if access denied

### 3. Controllers

**Admin Controllers:**
- `SubscriptionPackageController` - CRUD operations for packages
- Permission-based access control
- Validation and error handling

**Frontend Controllers:**
- `SubscriptionController` - User-facing subscription management
- `plans()` - Display available packages
- `checkout()` - Subscription checkout process
- `subscribe()` - Process subscription (manual/payment gateway)
- `mySubscription()` - User's subscription dashboard
- `cancel()` - Cancel auto-renewal

### 4. Content Filtering

**HomeController Updates:**
- Filters all news queries based on user's subscription tier
- Applies `forSubscriptionTier()` scope to all news queries
- Checks access before displaying individual news articles

### 5. Routes

**Admin Routes:**
- `/admin/subscription-package` - Package management (CRUD)

**Frontend Routes:**
- `/subscription/plans` - View all packages
- `/subscription/checkout/{packageId}` - Checkout page
- `/subscription/subscribe/{packageId}` - Process subscription
- `/subscription/my-subscription` - User dashboard

## Features Implemented

✅ Database migrations for all tables
✅ Models with relationships and helper methods
✅ Middleware for access control
✅ Admin CRUD interface for packages
✅ Frontend subscription management
✅ Content filtering based on subscription tier
✅ Subscription lifecycle management
✅ Default package seeder (UNB Lite, Pro, Ultra)

## Next Steps (Future Enhancements)

1. **Payment Gateway Integration**
   - Stripe integration
   - PayPal integration
   - Local payment methods

2. **Email Notifications**
   - Subscription confirmation
   - Renewal reminders
   - Expiration warnings

3. **Analytics & Reporting**
   - Subscription metrics dashboard
   - Revenue tracking
   - User engagement by tier

4. **Advanced Features**
   - Trial periods
   - Discount codes
   - Family/team plans
   - Subscription upgrades/downgrades

## Core Software Engineering Topics

### 1. **Access Control & Authorization**
- **Topic**: Role-Based Access Control (RBAC) and Permission Systems
- **Study Areas**:
  - Laravel Gates and Policies
  - Spatie Permission Package
  - Middleware-based authorization
  - Database-driven permission systems

### 2. **Subscription Lifecycle Management**
- **Topic**: State Machines and Lifecycle Management
- **Study Areas**:
  - Subscription states (active, expired, cancelled, pending)
  - Auto-renewal logic
  - Expiration handling
  - Subscription transitions

### 3. **Content Filtering & Scoping**
- **Topic**: Query Scoping and Data Filtering
- **Study Areas**:
  - Laravel Query Scopes
  - Eloquent ORM relationships
  - Database query optimization
  - Conditional data access

### 4. **Payment Processing**
- **Topic**: Payment Gateway Integration
- **Study Areas**:
  - Stripe API integration
  - PayPal SDK
  - Webhook handling
  - Payment security (PCI compliance)

### 5. **Database Design**
- **Topic**: Relational Database Design
- **Study Areas**:
  - Normalization (1NF, 2NF, 3NF)
  - Foreign key relationships
  - Indexing strategies
  - Migration management

## Testing Recommendations

1. **Unit Tests**: Model methods, access checks
2. **Feature Tests**: Subscription flow, access control
3. **Integration Tests**: Payment processing, email notifications
4. **Performance Tests**: Query optimization, caching strategies

## Security Considerations

1. **Access Control**: Always verify subscription status server-side
2. **Payment Security**: Never store payment credentials
3. **Data Validation**: Validate all subscription inputs
4. **Rate Limiting**: Prevent subscription abuse
5. **Audit Logging**: Track subscription changes

---

**Implementation Status**: ✅ Core System Complete
**Ready for**: Payment gateway integration and testing

