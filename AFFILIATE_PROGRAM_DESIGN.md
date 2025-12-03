# SST.NYC Affiliate Program Implementation

## Overview

Simple two-phase affiliate program for SST.NYC Site Safety Training courses.

**Tech Stack:**
- WP Forms (signup collection)
- Zapier (automation middleware)
- Google Workspace (data storage)
- Python QR code generation (Phase 2)

---

## Phase 1: Affiliate Signup System

### Objective
Collect affiliate registrations through a simple form and store in Google Sheets for management.

### 1.1 WP Forms Setup

**Form Name:** "SST Affiliate Program Application"

**Required Fields:**
```
1. First Name (text, required)
2. Last Name (text, required)
3. Email (email, required, unique validation)
4. Phone (phone, required)
5. Company/Organization (text, optional)
6. How did you hear about us? (dropdown)
   - Current Student
   - Past Student
   - Online Search
   - Social Media
   - Referral
   - Other
7. Why do you want to become an affiliate? (paragraph, required)
8. Terms & Conditions (checkbox, required)
   - "I agree to the SST.NYC Affiliate Terms and Conditions"
```

**Form Settings:**
- **Notification Email**: Admin notification on new signup
- **Confirmation**: Display success message with next steps
- **Anti-Spam**: Enable Google reCAPTCHA
- **Entry Storage**: Save to WordPress database + Zapier webhook

**Success Message:**
```
Thank you for applying to the SST.NYC Affiliate Program!

We've received your application and will review it within 2-3 business days.

Next Steps:
1. You'll receive an email confirmation shortly
2. Our team will review your application
3. Once approved, you'll receive your unique affiliate link and QR code
4. Start earning commissions on course referrals!

Questions? Email: affiliates@sst.nyc
```

### 1.2 Google Sheets Structure

**Sheet Name:** "SST Affiliate Signups"

**Columns:**
| Column | Description | Auto-populated |
|--------|-------------|----------------|
| A: Timestamp | Submission date/time | Yes (Zapier) |
| B: Affiliate ID | Unique identifier | Yes (Formula) |
| C: First Name | From form | Yes |
| D: Last Name | From form | Yes |
| E: Email | From form | Yes |
| F: Phone | From form | Yes |
| G: Company | From form | Yes |
| H: Referral Source | How they heard | Yes |
| I: Motivation | Why affiliate | Yes |
| J: Status | Pending/Approved/Rejected | Manual |
| K: Approved Date | Date approved | Manual |
| L: Affiliate Link | Unique URL | Phase 2 |
| M: QR Code URL | Link to QR image | Phase 2 |
| N: Total Referrals | Count | Future |
| O: Total Revenue | $ amount | Future |
| P: Notes | Admin notes | Manual |

**Affiliate ID Formula (Column B):**
```
=TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & RIGHT(TEXT(ROW(),"0000"),3)
```
Example: `20251202-SS001` (Date-Initials-Sequence)

### 1.3 Zapier Workflow (Phase 1)

**Trigger:** WP Forms - New Entry Received
- Form: "SST Affiliate Program Application"
- Instant trigger via webhook

**Action 1:** Google Sheets - Create Spreadsheet Row
- Spreadsheet: "SST Affiliate Management"
- Worksheet: "SST Affiliate Signups"
- Map fields:
  - Timestamp → A
  - First Name → C
  - Last Name → D
  - Email → E
  - Phone → F
  - Company → G
  - Referral Source → H
  - Motivation → I
  - Status → "Pending" (default)

**Action 2:** Gmail - Send Email (Confirmation to Applicant)
- To: {{Email}}
- Subject: "Your SST.NYC Affiliate Application Received"
- Body:
```html
Hi {{First Name}},

Thank you for applying to the SST.NYC Affiliate Program!

Application Details:
- Name: {{First Name}} {{Last Name}}
- Email: {{Email}}
- Company: {{Company}}

Your application is now under review. Our team will evaluate your submission and respond within 2-3 business days.

What happens next?
✓ Our team reviews your application
✓ You'll receive approval or further instructions via email
✓ Once approved, you'll get your unique affiliate link and QR code
✓ Start earning 10% commission on every course referral!

Questions? Reply to this email or contact affiliates@sst.nyc

Best regards,
The SST.NYC Team
```

**Action 3:** Gmail - Send Email (Admin Notification)
- To: admin@sst.nyc (or your admin email)
- Subject: "New Affiliate Application: {{First Name}} {{Last Name}}"
- Body:
```html
New affiliate application received:

Applicant: {{First Name}} {{Last Name}}
Email: {{Email}}
Phone: {{Phone}}
Company: {{Company}}
Source: {{Referral Source}}

Motivation:
{{Motivation}}

Review and approve at:
https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/edit

Action Required:
1. Review application details
2. Update Status column to "Approved" or "Rejected"
3. If approved, proceed to Phase 2 (QR code generation)
```

### 1.4 WordPress Setup Steps

**Step 1: Install WP Forms**
```bash
ssh -p 65002 u629344933@147.93.88.8
cd /home/u629344933/domains/sst.nyc/public_html/staging
wp plugin install wpforms-lite --activate
```

**Step 2: Create Form**
- Navigate to: WP Admin → WP Forms → Add New
- Template: Blank Form
- Add fields as specified in section 1.1
- Enable Zapier webhook in form settings

**Step 3: Create Dedicated Page**
```bash
wp post create \
  --post_type=page \
  --post_title='Join Our Affiliate Program' \
  --post_status=publish \
  --post_content='[wpforms id="XXX"]' \
  --porcelain
```
- Insert WP Forms shortcode
- Set permalink: `/affiliate-signup/`
- Add to main navigation menu

**Step 4: Configure Zapier Webhook**
- WP Forms → Settings → Integrations → Zapier
- Copy webhook URL
- Paste into Zapier trigger setup

---

## Phase 2: QR Code Auto-Generation

### Objective
Automatically generate unique QR codes for approved affiliates that link to courses with tracking parameters.

### 2.1 Affiliate Link Structure

**Base Pattern:**
```
https://sst.nyc/courses/[COURSE-SLUG]/?ref=[AFFILIATE_ID]
```

**Examples:**
```
https://sst.nyc/courses/32hr-supervisor/?ref=20251202-SS001
https://sst.nyc/courses/10hr-construction/?ref=20251202-SS001
```

**Course Slugs:**
- `10hr-construction`
- `30hr-scaffold`
- `32hr-supervisor`
- `8hr-renewal`
- `16hr-refresher`
- `40hr-demolition`

**Generic Link (All Courses):**
```
https://sst.nyc/?ref=[AFFILIATE_ID]
```

### 2.2 QR Code Generation Architecture

**Option A: Python Script via MCP Server** (Recommended)

Create new tool in `wordpress-mcp-server`:

```python
# src/qr_code_generator.py

import qrcode
from io import BytesIO
import base64
from typing import Dict

def generate_affiliate_qr(affiliate_id: str, course_slug: str = None) -> Dict:
    """
    Generate QR code for affiliate link.

    Args:
        affiliate_id: Unique affiliate identifier (e.g., "20251202-SS001")
        course_slug: Optional course slug. If None, creates generic link.

    Returns:
        Dict with base64 encoded QR image and link URL
    """
    # Build URL
    if course_slug:
        url = f"https://sst.nyc/courses/{course_slug}/?ref={affiliate_id}"
    else:
        url = f"https://sst.nyc/?ref={affiliate_id}"

    # Generate QR code
    qr = qrcode.QRCode(
        version=1,  # Size: 1 = 21x21, auto-adjust
        error_correction=qrcode.constants.ERROR_CORRECT_H,  # High error correction
        box_size=10,  # Pixel size of each box
        border=4,  # Border width in boxes
    )
    qr.add_data(url)
    qr.make(fit=True)

    # Create image
    img = qr.make_image(fill_color="black", back_color="white")

    # Convert to base64
    buffered = BytesIO()
    img.save(buffered, format="PNG")
    img_base64 = base64.b64encode(buffered.getvalue()).decode()

    return {
        "url": url,
        "qr_code_base64": img_base64,
        "affiliate_id": affiliate_id,
        "course_slug": course_slug or "general"
    }

def generate_affiliate_qr_set(affiliate_id: str) -> Dict:
    """
    Generate QR codes for all courses + 1 generic.

    Returns dict with 7 QR codes.
    """
    courses = [
        "10hr-construction",
        "30hr-scaffold",
        "32hr-supervisor",
        "8hr-renewal",
        "16hr-refresher",
        "40hr-demolition"
    ]

    qr_set = {}

    # Generic QR
    qr_set["general"] = generate_affiliate_qr(affiliate_id, None)

    # Course-specific QRs
    for course in courses:
        qr_set[course] = generate_affiliate_qr(affiliate_id, course)

    return qr_set
```

**Add MCP Tool:**
```python
# src/server.py (add to tool list)

@server.call_tool()
async def generate_affiliate_qr_codes(affiliate_id: str) -> list[types.TextContent]:
    """Generate QR codes for affiliate links."""
    from qr_code_generator import generate_affiliate_qr_set

    qr_set = generate_affiliate_qr_set(affiliate_id)

    result = {
        "affiliate_id": affiliate_id,
        "qr_codes": qr_set,
        "total_generated": len(qr_set)
    }

    return [types.TextContent(
        type="text",
        text=json.dumps(result, indent=2)
    )]
```

**Option B: Google Apps Script** (Alternative)

For pure cloud solution, use Google Apps Script to generate QR codes in Google Sheets:

```javascript
// Google Apps Script attached to spreadsheet

function generateQRCode(affiliateId, courseSlug) {
  let url = courseSlug
    ? `https://sst.nyc/courses/${courseSlug}/?ref=${affiliateId}`
    : `https://sst.nyc/?ref=${affiliateId}`;

  // Use Google Charts API for QR generation
  let qrUrl = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${encodeURIComponent(url)}&choe=UTF-8`;

  return qrUrl;
}

function onApproval() {
  // Triggered when Status column changes to "Approved"
  let sheet = SpreadsheetApp.getActiveSheet();
  let row = sheet.getActiveRange().getRow();
  let status = sheet.getRange(row, 10).getValue(); // Column J

  if (status === "Approved") {
    let affiliateId = sheet.getRange(row, 2).getValue(); // Column B

    // Generate generic QR
    let qrUrl = generateQRCode(affiliateId, null);
    sheet.getRange(row, 13).setValue(qrUrl); // Column M

    // Generate affiliate link
    let link = `https://sst.nyc/?ref=${affiliateId}`;
    sheet.getRange(row, 12).setValue(link); // Column L
  }
}
```

### 2.3 Zapier Workflow (Phase 2)

**Trigger:** Google Sheets - Updated Spreadsheet Row
- Spreadsheet: "SST Affiliate Management"
- Worksheet: "SST Affiliate Signups"
- Trigger Column: J (Status)
- Trigger Value: "Approved"

**Filter:** Only Continue If
- Status = "Approved"
- QR Code URL is empty (hasn't been generated yet)

**Action 1:** Webhooks by Zapier - POST Request
- URL: `https://your-mcp-server.com/generate-qr`
- Method: POST
- Data:
  ```json
  {
    "affiliate_id": "{{Affiliate ID}}",
    "email": "{{Email}}",
    "first_name": "{{First Name}}"
  }
  ```

**Action 2:** Google Sheets - Update Spreadsheet Row
- Spreadsheet: Same
- Row: {{Row Number}}
- Columns to Update:
  - L (Affiliate Link): `https://sst.nyc/?ref={{Affiliate ID}}`
  - M (QR Code URL): Response from webhook
  - K (Approved Date): Current date

**Action 3:** Gmail - Send Email (Approval + QR Code)
- To: {{Email}}
- Subject: "🎉 Welcome to SST.NYC Affiliate Program - Your Links Inside!"
- Attachments: QR code image(s)
- Body:
```html
Hi {{First Name}},

Congratulations! Your application has been APPROVED! 🎉

You're now an official SST.NYC affiliate partner. Here's everything you need to start earning:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
YOUR AFFILIATE DETAILS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Affiliate ID: {{Affiliate ID}}

Your Unique Link:
{{Affiliate Link}}

Commission Rate: 10% per course sale
Payment Terms: Net 30 days
Cookie Duration: 60 days

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
HOW TO USE YOUR AFFILIATE LINK
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. SHARE YOUR LINK
   Copy your link and share it on:
   ✓ Social media (Instagram, Facebook, LinkedIn)
   ✓ Email newsletters
   ✓ Your website or blog
   ✓ WhatsApp/text messages

2. USE YOUR QR CODE
   Print your QR code and post it:
   ✓ Construction job sites
   ✓ Community bulletin boards
   ✓ Business cards
   ✓ Flyers and brochures

3. TRACK YOUR EARNINGS
   Login to your affiliate dashboard:
   https://sst.nyc/affiliate-dashboard/

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
MARKETING TIPS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✓ Target construction workers, supervisors, contractors
✓ Emphasize NYC DOB compliance requirements
✓ Highlight flexible online training format
✓ Mention we're DOB-approved providers
✓ Share completion time: Most finish in 2-4 weeks

Sample Social Post:
"Need your NYC DOB Site Safety Training? I recommend SST.NYC -
they're approved, affordable, and 100% online. Use my link for
easy enrollment: [YOUR LINK]"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SUPPORT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Questions? We're here to help!
📧 Email: affiliates@sst.nyc
📞 Phone: [Your Phone]
💬 Live Chat: sst.nyc/chat

Your success is our success. Let's grow together!

Best regards,
The SST.NYC Team

P.S. Your QR code is attached to this email. Download and start sharing today!
```

### 2.4 WooCommerce Affiliate Tracking

**Install Plugin:**
```bash
wp plugin install affiliatewp --activate
```
OR use built-in WooCommerce coupon tracking as lightweight alternative.

**Lightweight Alternative: URL Parameter Tracking**

Add to `functions.php` or custom plugin:

```php
<?php
/**
 * Track affiliate referrals via URL parameter
 */

// Store affiliate ref in cookie
add_action('init', 'sst_track_affiliate_referral');
function sst_track_affiliate_referral() {
    if (isset($_GET['ref'])) {
        $affiliate_id = sanitize_text_field($_GET['ref']);

        // Store in cookie for 60 days
        setcookie('sst_affiliate_ref', $affiliate_id, time() + (60 * 86400), '/');

        // Store in session
        if (!session_id()) {
            session_start();
        }
        $_SESSION['sst_affiliate_ref'] = $affiliate_id;
    }
}

// Add affiliate ID to order meta on purchase
add_action('woocommerce_checkout_create_order', 'sst_add_affiliate_to_order', 10, 2);
function sst_add_affiliate_to_order($order, $data) {
    $affiliate_id = null;

    // Check session first
    if (!session_id()) {
        session_start();
    }
    if (isset($_SESSION['sst_affiliate_ref'])) {
        $affiliate_id = $_SESSION['sst_affiliate_ref'];
    }
    // Fallback to cookie
    elseif (isset($_COOKIE['sst_affiliate_ref'])) {
        $affiliate_id = $_COOKIE['sst_affiliate_ref'];
    }

    if ($affiliate_id) {
        $order->update_meta_data('_affiliate_id', $affiliate_id);
        $order->update_meta_data('_affiliate_commission_rate', 0.10); // 10%

        // Calculate commission
        $order_total = $order->get_total();
        $commission = $order_total * 0.10;
        $order->update_meta_data('_affiliate_commission', $commission);
    }
}

// Admin column to show affiliate referrals
add_filter('manage_edit-shop_order_columns', 'sst_add_affiliate_column');
function sst_add_affiliate_column($columns) {
    $columns['affiliate'] = 'Affiliate';
    return $columns;
}

add_action('manage_shop_order_posts_custom_column', 'sst_affiliate_column_content');
function sst_affiliate_column_content($column) {
    global $post;

    if ($column === 'affiliate') {
        $order = wc_get_order($post->ID);
        $affiliate_id = $order->get_meta('_affiliate_id');

        if ($affiliate_id) {
            echo '<strong>' . esc_html($affiliate_id) . '</strong><br>';
            echo '$' . number_format($order->get_meta('_affiliate_commission'), 2);
        } else {
            echo '—';
        }
    }
}
```

### 2.5 Zapier Sales Tracking (Phase 2 Enhancement)

**Trigger:** WooCommerce - New Order
- Status: Completed

**Filter:** Only Continue If
- Order Meta contains `_affiliate_id`

**Action 1:** Google Sheets - Update Spreadsheet Row
- Spreadsheet: "SST Affiliate Management"
- Worksheet: "SST Affiliate Signups"
- Lookup Column: B (Affiliate ID)
- Lookup Value: {{Order Meta: _affiliate_id}}
- Update Columns:
  - N (Total Referrals): Increment by 1
  - O (Total Revenue): Add commission amount

**Action 2:** Gmail - Send Email (Commission Notification)
- To: Affiliate email (lookup from sheet)
- Subject: "💰 You earned a commission! Order #{{Order Number}}"
- Body:
```html
Great news, {{First Name}}!

One of your referrals just completed a purchase!

Order Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Order #: {{Order Number}}
Course: {{Product Name}}
Order Total: ${{Order Total}}
Your Commission (10%): ${{Commission}}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

This commission will be paid out on: {{Next Payout Date}}

View your full earnings:
https://sst.nyc/affiliate-dashboard/

Keep up the great work!

The SST.NYC Team
```

---

## Implementation Checklist

### Phase 1: Signup System
- [ ] Install WP Forms plugin on staging
- [ ] Create "SST Affiliate Program Application" form
- [ ] Create `/affiliate-signup/` page
- [ ] Add page to main navigation
- [ ] Create Google Sheet "SST Affiliate Management"
- [ ] Set up Zapier Zap #1 (Form → Sheet)
- [ ] Configure email confirmations (applicant + admin)
- [ ] Test full flow with dummy submission
- [ ] Deploy to production

### Phase 2: QR Code Generation
- [ ] Choose implementation method (Python MCP vs Google Apps Script)
- [ ] If Python: Add `qrcode` library to requirements.txt
- [ ] If Python: Create `qr_code_generator.py` module
- [ ] If Python: Add MCP tool to server.py
- [ ] If Google: Write Apps Script for QR generation
- [ ] Set up Zapier Zap #2 (Approval → QR Generation)
- [ ] Design approval email template with QR attachment
- [ ] Add affiliate tracking code to functions.php
- [ ] Test affiliate link tracking with test order
- [ ] Create affiliate dashboard page (optional)

### Phase 3: Sales Tracking (Future)
- [ ] Set up Zapier Zap #3 (Order → Commission Tracking)
- [ ] Configure commission notification emails
- [ ] Build affiliate payout process
- [ ] Create monthly commission reports

---

## Cost Breakdown

| Item | Cost | Notes |
|------|------|-------|
| WP Forms Lite | Free | Sufficient for basic forms |
| Zapier Starter | $29.99/mo | 20 Zaps, 750 tasks/month |
| Google Workspace | $0 | Already have |
| Python qrcode lib | Free | Open source |
| AffiliateWP (optional) | $149.50/yr | Only if needed for advanced features |

**Total Monthly Cost: ~$30** (Zapier only)

---

## Testing Plan

### Phase 1 Testing
1. Submit test application via form
2. Verify data appears in Google Sheet
3. Check confirmation email received (applicant)
4. Check admin notification email
5. Verify Affiliate ID auto-generates correctly
6. Test form validation (required fields, email format)
7. Test duplicate email prevention

### Phase 2 Testing
1. Manually set Status to "Approved" in Google Sheet
2. Verify Zapier triggers QR generation
3. Check QR code saves to correct column
4. Verify approval email sends with QR attachment
5. Scan QR code with phone → verify lands on correct URL
6. Check URL parameter (?ref=XXXX) is present
7. Test affiliate tracking cookie is set
8. Complete test purchase with affiliate link
9. Verify order meta contains affiliate ID
10. Check commission calculation is correct

---

## Next Steps

1. **Immediate (Phase 1)**:
   - Install WP Forms on staging
   - Create signup form
   - Set up Google Sheet
   - Configure Zapier workflow

2. **Week 1 (Phase 2 Prep)**:
   - Decide: Python MCP vs Google Apps Script for QR generation
   - Install required dependencies
   - Write QR generation code
   - Test locally

3. **Week 2 (Phase 2 Deploy)**:
   - Configure Zapier approval workflow
   - Test full end-to-end flow
   - Deploy to production
   - Recruit first 5 affiliates for beta test

4. **Month 1 (Optimization)**:
   - Monitor signup conversion rate
   - Gather affiliate feedback
   - Optimize email copy
   - Add sales tracking (Phase 3)

---

**Questions to Answer:**

1. What commission rate? (Suggested: 10% per course sale)
2. Manual or auto-approval for affiliates?
3. Cookie duration for tracking? (Suggested: 60 days)
4. Payment terms? (Suggested: Net 30 days)
5. Minimum payout threshold? (Suggested: $50)
6. QR code hosting: Google Drive, WordPress Media Library, or CDN?

---

**Last Updated:** December 2, 2025
**Status:** Design Complete - Ready for Phase 1 Implementation
