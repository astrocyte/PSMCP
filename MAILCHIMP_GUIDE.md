# Mailchimp Email Marketing Guide

Complete guide to email marketing automation with Mailchimp integration.

## 📧 Overview

The Mailchimp integration enables complete email marketing automation for your WordPress LMS:
- Sync WooCommerce customers automatically
- Tag students when they enroll in courses
- Create and send email campaigns
- Track campaign performance
- Build automated workflows

## 🚀 Setup

### 1. Get Your Mailchimp API Key

1. Log into Mailchimp
2. Go to Account → Extras → API Keys
3. Create a new API key
4. Copy the key (looks like: `xxxxxxxxxxxxxxxxxxxxx-us19`)

### 2. Find Your Server Prefix

The server prefix is in your API key after the dash (e.g., `us19`, `us1`, `us21`)

### 3. Get Your Audience (List) ID

1. Go to Audience → All contacts
2. Click Settings → Audience name and defaults
3. Copy the Audience ID (looks like: `abc123def4`)

### 4. Add to `.env`

```bash
MAILCHIMP_API_KEY=your-api-key-here-us19
MAILCHIMP_SERVER=us19
MAILCHIMP_LIST_ID=your-default-audience-id
```

## 📊 Available Tools (6 Total)

### 1. mc_list_audiences
List all your Mailchimp audiences (email lists).

**Use Case:**
```
"Show me all my Mailchimp audiences"
```

**Returns:**
- Audience names
- Subscriber counts
- Audience IDs

### 2. mc_add_subscriber
Add or update a subscriber in an audience.

**Parameters:**
- `email` (required) - Email address
- `first_name` (optional) - First name
- `last_name` (optional) - Last name
- `tags` (optional) - Array of tags
- `list_id` (optional) - Uses default if not provided

**Use Case:**
```
"Add subscriber@example.com to my Mailchimp list
First name: John
Last name: Doe
Tags: ['New Customer', 'Python Course']"
```

**Auto-tags available:**
- Custom tags you specify
- Merge fields (FNAME, LNAME)

### 3. mc_create_campaign
Create an email campaign.

**Parameters:**
- `subject` (required) - Email subject
- `from_name` (required) - Sender name
- `reply_to` (required) - Reply email
- `list_id` (optional) - Audience to send to

**Use Case:**
```
"Create Mailchimp campaign
Subject: New Python Course Launch!
From: SST.NYC Team
Reply to: support@sst.nyc"
```

**Returns campaign ID** for next steps (add content, send)

### 4. mc_send_campaign
Send a campaign immediately.

**Parameters:**
- `campaign_id` (required) - Campaign to send

**Use Case:**
```
"Send Mailchimp campaign ID abc123"
```

⚠️ **Warning:** This sends immediately to all subscribers!

### 5. mc_get_campaign_report
Get campaign performance statistics.

**Parameters:**
- `campaign_id` (required) - Campaign ID

**Returns:**
- Open rate
- Click rate
- Unsubscribes
- Bounces
- Revenue (if e-commerce tracking enabled)

**Use Case:**
```
"Show me the report for campaign abc123"
```

### 6. mc_tag_course_student
Automatically tag a student when they enroll in a course.

**Parameters:**
- `email` (required) - Student email
- `course_name` (required) - Course title
- `course_id` (required) - Course ID
- `list_id` (optional) - Audience ID

**Auto-adds tags:**
- "Course Student"
- "Course: {course_name}"
- "Course ID: {course_id}"

**Use Case:**
```
"Tag student@example.com for enrolling in Python Basics course ID 123"
```

## 🔄 Automation Workflows

### Workflow 1: New Customer Welcome Series

**When:** Customer makes first purchase

**Steps:**
1. WooCommerce order completes
2. Add customer to Mailchimp (mc_add_subscriber)
3. Tag with "New Customer"
4. Mailchimp automation sends welcome series

**Setup in Mailchimp:**
1. Create automation: "Customer Journey"
2. Trigger: Tag "New Customer"
3. Emails:
   - Day 0: Welcome + login info
   - Day 3: Getting started guide
   - Day 7: Course tips
   - Day 14: Feedback request

### Workflow 2: Course Launch Campaign

**When:** New course is ready to launch

**Complete Flow:**
```
1. Create course (ld_create_course) → Course ID 123
2. Create WooCommerce product (wc_create_product) → Link to course
3. Create launch coupon (wc_create_coupon) → "LAUNCH50" 50% off
4. Create Mailchimp campaign (mc_create_campaign)
5. Craft email content (manual in Mailchimp or via API)
6. Send campaign (mc_send_campaign)
7. Monitor performance (mc_get_campaign_report)
```

### Workflow 3: Student Enrollment Tagging

**When:** Student enrolls in course

**Automatic:**
```
1. Student purchases course
2. WooCommerce processes payment
3. LearnDash enrolls student (ld_enroll_user)
4. Tag in Mailchimp (mc_tag_course_student)
   - Tags: "Course Student", "Course: Python Basics", "Course ID: 123"
5. Mailchimp automation sends course-specific emails
```

**Automation in Mailchimp:**
- Trigger: Tag "Course: Python Basics"
- Day 0: Welcome to course
- Day 1: Lesson 1 reminder
- Day 3: Progress check-in
- Day 7: Quiz reminder
- Day 14: Completion follow-up

### Workflow 4: Abandoned Cart Recovery

**When:** Customer adds to cart but doesn't purchase

**Flow:**
```
1. Customer adds course to cart (tracked via WooCommerce)
2. 1 hour: No purchase → Add tag "Abandoned Cart"
3. Mailchimp automation triggered
4. Send recovery email with coupon
5. Track conversions
```

### Workflow 5: Course Completion Celebration

**When:** Student completes course

**Flow:**
```
1. Student completes final lesson/quiz
2. LearnDash marks course complete
3. Add Mailchimp tag "Completed: Python Basics"
4. Send congratulations email
5. Offer advanced course discount
6. Request testimonial/review
```

## 💡 Best Practices

### List Management

**Segment Your Audience:**
- All customers
- Course students only
- Product interested (tagged by product)
- High-value customers ($500+ spent)
- Inactive (no purchase 90+ days)

**Tag Strategy:**
```
Customer Status:
- New Customer
- Repeat Customer
- VIP Customer

Course Enrollments:
- Course Student
- Course: [Course Name]
- Course ID: [ID]
- Completed: [Course Name]

Purchase Behavior:
- High Spender
- Sale Shopper
- Bundle Buyer

Engagement:
- Email Opener
- Link Clicker
- Non-Engaged
```

### Campaign Tips

**Subject Lines:**
- Keep under 50 characters
- Use personalization: "Hi {{FNAME}}"
- Create urgency: "24 hours left!"
- Ask questions: "Ready to level up?"
- A/B test everything

**Email Content:**
- Mobile-first design
- Clear CTA button
- Single focus per email
- Include social proof
- Always test before sending

**Sending Times:**
- Tuesday-Thursday: Best open rates
- 10 AM - 2 PM: Peak engagement
- Avoid Mondays/Fridays
- Test your specific audience

### Automation Rules

**Do:**
- Welcome new subscribers immediately
- Send course content on schedule
- Re-engage inactive subscribers
- Celebrate milestones (completions, anniversaries)
- Request feedback after completion

**Don't:**
- Email too frequently (max 2-3/week)
- Send same message to entire list
- Forget to segment
- Skip A/B testing
- Ignore unsubscribes

## 🎯 Advanced Use Cases

### Use Case 1: Course Launch Sequence

**Goal:** Maximize enrollment for new course

**7-Day Launch Plan:**

**Day -7 (Pre-launch):**
```
Campaign: "Something Big Coming..."
- Tease new course
- Build anticipation
- Tag "Launch Interested"
```

**Day 0 (Launch):**
```
Campaign: "It's Here! Python Mastery Course"
- Official announcement
- Early bird pricing
- Launch coupon code
```

**Day 2:**
```
Campaign: "What You'll Learn"
- Course curriculum
- Student testimonials
- Success stories
```

**Day 4:**
```
Campaign: "Last Chance - 48 Hours"
- Urgency messaging
- Social proof
- FAQ answers
```

**Day 6:**
```
Campaign: "Final Hours!"
- Countdown to price increase
- Final push
```

**Day 7:**
```
Campaign: "Thank You + What's Next"
- To enrolled students
- Getting started guide
- Next course teaser
```

### Use Case 2: Student Lifecycle Email

**New Enrollment → Completion → Upsell**

**Phase 1: Welcome (Day 0)**
```
- Welcome email
- Login instructions
- Course roadmap
- Support contact
```

**Phase 2: Engagement (Days 1-30)**
```
- Lesson reminders
- Progress updates
- Helpful tips
- Community invitation
```

**Phase 3: Support (Throughout)**
```
- If inactive 7 days → Encouragement email
- If stuck on quiz → Help resources
- If 50% complete → Motivation boost
```

**Phase 4: Completion (End)**
```
- Congratulations
- Certificate download
- Request review/testimonial
- Advanced course discount (30% off)
```

**Phase 5: Alumni (Post-completion)**
```
- New course announcements
- Community events
- Exclusive offers
- Referral program
```

### Use Case 3: Win-Back Campaign

**Target:** Inactive subscribers (no opens in 90 days)

**Strategy:**
```
Email 1: "We miss you!"
- Personal message
- Highlight what they're missing
- Special win-back offer

Email 2 (7 days later): "One more try..."
- Different angle
- Student success stories
- Limited time offer

Email 3 (14 days later): "Final chance"
- Last email before cleanup
- Reconfirm subscription
- Unsubscribe option prominent

Result:
- Re-engaged → Tag "Re-engaged"
- No response → Remove or separate list
```

## 📊 Tracking & Analytics

### Key Metrics

**Campaign Performance:**
- Open rate (target: 20-25%)
- Click rate (target: 2-5%)
- Conversion rate (target: 1-3%)
- Unsubscribe rate (keep under 0.5%)

**Audience Health:**
- Growth rate
- Engagement score
- Tag distribution
- Segment sizes

**Revenue Attribution:**
- Sales from campaigns
- Revenue per subscriber
- Customer lifetime value
- ROI per campaign

### Monthly Reporting

**Pull These Reports:**
```
1. All campaign performance (mc_get_campaign_report)
2. Audience growth
3. Top-performing campaigns
4. Course enrollment attribution
5. Revenue generated
```

## 🔗 Integration with WordPress

### Automatic Syncing

**WooCommerce Customers:**
When order completes:
1. Customer data synced to Mailchimp
2. Tagged "WooCommerce Customer"
3. Merge fields updated (name, etc.)

**LearnDash Students:**
When enrolled:
1. Student synced to Mailchimp
2. Tagged with course name
3. Course-specific automation triggered

### Manual Operations

**Add Single Subscriber:**
```
"Add john@example.com to Mailchimp
First name: John
Last name: Doe
Tags: VIP Customer"
```

**Bulk Import:**
For mass operations, use Mailchimp's CSV import or API batch operations.

## 🆘 Troubleshooting

### "Mailchimp not configured"
- Add `MAILCHIMP_API_KEY` to `.env`
- Add `MAILCHIMP_SERVER` (e.g., us19)
- Restart server

### "No list_id provided"
- Add `MAILCHIMP_LIST_ID` to `.env` for default
- Or specify `list_id` in each call

### "400 Bad Request"
- Check API key is valid
- Verify server prefix matches API key
- Check list ID exists

### "Member already exists"
- Use update instead of create
- Or include `status_if_new` parameter

### Low open rates
- Check subject lines
- Verify sending time
- Clean inactive subscribers
- Improve content quality

## 🎓 Learning Resources

**Mailchimp Resources:**
- Mailchimp Marketing API docs
- Email marketing best practices
- Automation workflows guide

**Your Setup:**
- 6 MCP tools for Mailchimp
- Full integration with WooCommerce
- LearnDash student tagging
- Campaign creation and tracking

## 🚀 Next Steps

1. **Set up `.env` with Mailchimp credentials**
2. **Test with:** `mc_list_audiences`
3. **Create first campaign** for existing students
4. **Set up automation** for new enrollments
5. **Track performance** and iterate

Your complete email marketing stack is ready! 📧
