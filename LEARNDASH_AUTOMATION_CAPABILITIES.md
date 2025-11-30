# LearnDash Automation Capabilities for SST.NYC

## Current Automation Stack (Installed & Active)

Your staging site currently has these automation tools installed:

| Plugin | Version | Status | Purpose |
|--------|---------|--------|---------|
| **learndash-notifications** | 1.6.6 | ✅ Active | Built-in email notifications |
| **learndash-zapier** | 2.3.1 | ✅ Active | LearnDash → Zapier integration |
| **zapier** | 1.5.3 | ✅ Active | Core Zapier WordPress plugin |
| **woocommerce-zapier** | 2.16.0 | ⚠️ Inactive | WooCommerce → Zapier integration |

**Status:** Notifications not yet configured (checked wp-options)

---

## LearnDash Built-In Automation Features

### 1. Email Notifications Add-On ✅ INSTALLED

**13 Available Triggers:**
- Course enrollment
- Course completion
- Lesson completion
- Topic completion
- Quiz completion
- Quiz passed
- Quiz failed
- Group enrollment
- Group completion
- Assignment upload
- Assignment approval
- Essay submission
- Essay graded

**34 Dynamic Shortcodes Available:**
- User info (name, email, username)
- Course info (title, URL, completion date)
- Lesson/Topic info (title, URL)
- Quiz info (title, score, percentage, results)
- Group info (title, leader name)
- Certificate links

**Recipients:**
- Student (user)
- Group Leader
- Site Admin
- Custom email addresses

**Capabilities:**
- Custom email templates
- HTML email support
- Delay notifications (send X hours/days after trigger)
- Conditional logic based on quiz scores
- Attach files/PDFs to emails

---

### 2. Certificate Automation

**Built-in Certificate Features:**
- ✅ Auto-generate certificates on course completion
- ✅ Auto-generate certificates on quiz pass
- ✅ Auto-generate certificates on group completion
- ✅ Dynamic certificate content (user name, date, score, etc.)
- ✅ Gutenberg-based Certificate Builder add-on

**Limitations:**
- ❌ Certificates NOT auto-emailed (must be downloaded manually)
- ❌ Only ONE certificate per course/quiz by default
- ❌ No bulk certificate generation

**Solutions for Auto-Emailing Certificates:**
- Use **LearnDash Notifications** + certificate URL shortcode
- Use **Multiple Certificates Pro** plugin (SaffireTech)
- Use **Uncanny Toolkit Pro** Email Quiz Certificates module

---

### 3. Zapier Integration ✅ INSTALLED

**7 LearnDash Triggers (Events that start automation):**
1. User enrolls in course
2. User completes course
3. User completes lesson
4. User completes topic
5. User passes quiz
6. User fails quiz
7. User completes quiz
8. User enrolls in group
9. User completes all courses in group

**2 LearnDash Actions (Things Zapier can do in LearnDash):**
1. Add user to group(s)
2. Remove user access from course(s)

**Custom Payload Filter:**
```php
// Add custom data to Zapier webhook
add_filter('learndash_zapier_api_payload', function($payload, $subscription, $trigger) {
    // Add custom user meta
    $payload['user_company'] = get_user_meta($payload['user_id'], 'company', true);
    $payload['user_dob_number'] = get_user_meta($payload['user_id'], 'dob_sst_number', true);

    // Add course meta
    $payload['course_instructor'] = get_post_meta($payload['course_id'], 'instructor_name', true);

    return $payload;
}, 10, 3);
```

**1,500+ Zapier App Integrations Available:**
- Google Sheets (student progress tracking)
- Mailchimp (email marketing)
- Slack (team notifications)
- Airtable (CRM/database)
- Google Calendar (schedule follow-ups)
- Salesforce (CRM updates)
- HubSpot (marketing automation)
- Stripe/PayPal (payment processing)
- SMS services (Twilio, TextMagic)

---

## Third-Party Automation Solutions

### 1. Uncanny Automator ⭐ RECOMMENDED

**What it is:** WordPress automation plugin (think Zapier but for WordPress only)

**Capabilities:**
- 110+ WordPress plugin integrations
- Unlimited automation recipes (pro version)
- Conditional logic
- User management automation
- Course enrollment automation
- Email automation
- WooCommerce integration

**Example Automations for SST.NYC:**
- When WooCommerce order is complete → Enroll user in course
- When user completes 32Hr Supervisor course → Add to "Certified Supervisors" group
- When quiz score < 70% → Send remedial resources email
- When course expires → Send renewal reminder 30 days before

**Cost:** Free version available, Pro $149/year

---

### 2. Bit Flows

**What it is:** No-code automation for WordPress + 200+ external apps

**Features:**
- Drag-and-drop workflow builder
- LearnDash triggers and actions
- CRM integrations
- Email marketing integrations
- Google Sheets/Airtable
- SMS notifications

**Use Cases:**
- Auto-sync enrollments to Google Sheets
- Update CRM when student completes course
- Send SMS reminders for upcoming live sessions

---

### 3. FunnelKit Automations (formerly AutomateWoo)

**Specialty:** WooCommerce + LearnDash integration

**Key Features:**
- Cart abandonment recovery
- Course upsells after completion
- Subscription renewal reminders
- Drip email campaigns
- Pre-built automation recipes

**Perfect for:**
- E-commerce automation
- Course sales funnels
- Subscription management

---

## NYC DOB SST Compliance Automation Opportunities

### 1. Certificate Management
**Requirement:** NYC DOB requires physical certificates for SST training

**Automation:**
- ✅ Auto-generate certificates on course completion
- ✅ Email PDF certificates automatically
- ✅ Include DOB course code and expiration date
- ⚠️ Log certificate issuance for compliance (custom dev needed)

### 2. Course Expiration & Renewal
**Requirement:** SST cards expire and require renewal

**Automation:**
- Send renewal reminder emails 90/60/30 days before expiration
- Auto-unenroll from expired courses
- Offer renewal course discount via WooCommerce coupon
- Update user meta with expiration dates

### 3. Attendance Tracking
**Requirement:** DOB requires attendance records

**Automation:**
- Log lesson completion timestamps
- Track time spent on course (requires Uncanny Toolkit Pro)
- Export attendance reports to Google Sheets via Zapier
- Generate compliance reports monthly

### 4. Instructor Notifications
**Automation:**
- Notify instructor when student fails quiz
- Alert on essay/assignment submissions
- Daily digest of course completions
- Weekly progress reports

### 5. Student Engagement
**Automation:**
- Welcome email sequence on enrollment
- Nudge emails for incomplete courses
- Congratulations email on course completion
- Survey/feedback request after completion

---

## Recommended Automation Setup for SST.NYC

### Phase 1: Core Automations (Using Installed Plugins)

**1. Configure LearnDash Notifications**
```
✅ Course completion → Certificate email (with PDF link)
✅ Quiz failed → Remedial resources email
✅ Course enrollment → Welcome email with course outline
✅ Quiz passed → Congratulations + next steps
✅ 7 days no activity → Re-engagement email
```

**2. Setup Zapier Workflows**
```
✅ Course completion → Update Google Sheets attendance log
✅ Course completion → Add to Mailchimp "Graduates" list
✅ Quiz failed → Notify instructor via Slack/Email
✅ New enrollment → Create CRM contact
✅ Certificate issued → Log in compliance database
```

### Phase 2: Advanced Automations (Additional Plugins)

**3. Install Uncanny Automator (Free/Pro)**
```
✅ WooCommerce purchase → Auto-enroll in course
✅ Course expiration approaching → Send renewal offer
✅ Complete beginner course → Upsell advanced course
✅ Group enrollment → Add to private Facebook group
```

**4. Enhanced Certificate System**
```
✅ Install Multiple Certificates Pro
✅ Auto-email certificates with DOB compliance data
✅ Issue multiple certificates (course + quiz)
✅ Bulk generate certificates for past students
```

### Phase 3: MCP-Powered Automation (Our Custom Solution)

**5. Custom MCP Automation Scripts**

Using our wordpress-mcp-server, we can create Python scripts for:

```python
# Auto-generate compliance reports
def generate_monthly_compliance_report():
    """
    - Query all course completions for month
    - Generate attendance CSV for DOB submission
    - Email report to compliance officer
    """

# Batch student operations
def process_expired_certifications():
    """
    - Find students with expired SST cards
    - Send renewal reminders
    - Auto-apply discount codes
    - Update student records
    """

# Course content synchronization
def sync_course_content_from_templates():
    """
    - Update all 6 SST courses with new regulations
    - Maintain consistent branding/formatting
    - Notify students of content updates
    """

# Automated quality control
def audit_course_quizzes():
    """
    - Verify all courses have required number of quiz questions
    - Check passing score thresholds
    - Ensure certificates are properly configured
    """
```

---

## Automation Best Practices for SST.NYC

### 1. Email Frequency
- ⚠️ **Don't over-email:** Max 2 automated emails per week per student
- ✅ **Use delays:** Space out email sequences by 2-3 days
- ✅ **Unsubscribe options:** Required for marketing emails

### 2. Testing
- ✅ Create test student account
- ✅ Test all email triggers before going live
- ✅ Check spam folder placement
- ✅ Verify links work correctly

### 3. Compliance
- ✅ Log all certificate issuances with timestamps
- ✅ Backup student data regularly
- ✅ Maintain GDPR/privacy compliance
- ✅ Store completion records for DOB audits (5+ years)

### 4. Monitoring
- ✅ Track email open rates
- ✅ Monitor automation failures (Zapier task history)
- ✅ Review student complaints about emails
- ✅ A/B test email subject lines

---

## Cost Analysis

| Solution | Cost | Best For |
|----------|------|----------|
| **LearnDash Notifications** | Included | Basic email automation |
| **LearnDash Zapier** | Included | External app integration |
| **Zapier Free** | $0/mo | <5 automations, 100 tasks/month |
| **Zapier Starter** | $29.99/mo | 20 Zaps, 750 tasks/month |
| **Zapier Professional** | $73.50/mo | Unlimited, 2K tasks/month |
| **Uncanny Automator Free** | $0 | WordPress-only automation |
| **Uncanny Automator Pro** | $149/yr | Unlimited recipes + webhooks |
| **Bit Flows** | $99/yr | No-code workflow builder |
| **Multiple Certificates Pro** | $49 | Auto-email certificates |

**Recommended Budget:** $150-200/year
- Uncanny Automator Pro ($149/yr)
- Multiple Certificates Pro ($49 one-time)
- Zapier Free tier (start free, upgrade if needed)

---

## Quick Win Automations (Implement This Week)

### 1. Welcome Email on Course Enrollment
**Setup Time:** 5 minutes
**Impact:** High - First impression

### 2. Certificate Email on Course Completion
**Setup Time:** 10 minutes
**Impact:** High - Reduces support tickets

### 3. Quiz Failure → Retry Instructions
**Setup Time:** 5 minutes
**Impact:** Medium - Improves pass rates

### 4. Admin Notification on New Enrollment
**Setup Time:** 3 minutes
**Impact:** Medium - Track revenue

### 5. Inactivity Reminder (7 days no login)
**Setup Time:** 10 minutes
**Impact:** High - Re-engage students

---

## Resources

**LearnDash Documentation:**
- [Notifications Add-On](https://learndash.com/support/kb/add-ons/notifications-add-on/notifications-2/)
- [Zapier Integration](https://www.learndash.com/support/docs/add-ons/zapier/)
- [Email Settings](https://learndash.com/support/kb/core/settings/emails/)
- [Certificate Builder](https://learndash.com/support/kb/add-ons/certificate-builder-add-on/certificate-builder-add-on/)

**Automation Platforms:**
- [LearnDash Zapier Integration](https://zapier.com/apps/learndash/integrations)
- [Uncanny Automator LearnDash](https://www.uncannyowl.com/)
- [Bit Flows LearnDash Guide](https://bit-flows.com/users-guide/triggers/learndash-integrations/)
- [FunnelKit Automations](https://funnelkit.com/learndash-notifications/)

**Third-Party Tools:**
- [Multiple Certificates Pro](https://www.saffiretech.com/multiple-certificates-for-learndash/)
- [Better Notifications](https://www.uncannyowl.com/better-notifications-for-learndash/)
- [Uncanny Toolkit Pro](https://www.uncannyowl.com/toolkit-pro-4-0-generate-learndash-certificates-in-bulk/)

---

**Last Updated:** November 23, 2025
**Environment:** SST.NYC Staging (staging.sst.nyc)
**Enrollment Status:** 5 students, 3 in 32Hr Supervisor, 2 in 8Hr Renewal
