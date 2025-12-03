# Zapier Setup Instructions for SST.NYC Affiliate Program

## Form Details

- **Form ID:** 5066
- **Form Name:** SST.NYC Affiliate Program Application
- **Page URL:** https://staging.sst.nyc/affiliate-signup/
- **Admin Email:** shawn@staging.sst.nyc

## Phase 1: Zapier Workflow Setup

### Step 1: Enable Zapier in WPForms

1. Log in to WordPress Admin: https://staging.sst.nyc/wp-admin/
2. Go to **WPForms → Settings → Integrations**
3. Click on **Zapier** integration
4. Click **Connect to Zapier**
5. Copy the **Zapier API Key** (you'll need this in Zapier)

### Step 2: Create Google Sheet

1. Go to Google Sheets: https://sheets.google.com
2. Create new spreadsheet: **"SST Affiliate Management"**
3. Rename Sheet1 to: **"SST Affiliate Signups"**
4. Add column headers in Row 1:

| A | B | C | D | E | F | G | H | I | J | K | L | M | N | O | P |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Timestamp | Affiliate ID | First Name | Last Name | Email | Phone | Company | Referral Source | Motivation | Status | Approved Date | Affiliate Link | QR Code URL | Total Referrals | Total Revenue | Notes |

5. In cell **B2**, add this formula (drag down for all rows):
```
=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))
```

6. Set default value for **J2** (Status):
```
Pending
```

### Step 3: Create Zapier Account

1. Go to https://zapier.com
2. Sign up or log in
3. Click **Create Zap**

### Step 4: Configure Trigger (WPForms Submission)

**Trigger Setup:**
1. **Choose App:** Search for "WPForms"
2. **Trigger Event:** "New Entry" or "New Form Entry"
3. Click **Continue**

**Configure Trigger:**
1. Click **Sign in to WPForms**
2. Enter your WordPress site URL: `https://staging.sst.nyc`
3. Enter the Zapier API Key from Step 1
4. Click **Yes, Continue**

**Choose Form:**
1. Select **"SST.NYC Affiliate Program Application"** (ID: 5066)
2. Click **Continue**

**Test Trigger:**
1. Click **Test trigger**
2. If no entries exist, submit a test form first at: https://staging.sst.nyc/affiliate-signup/
3. You should see the test data appear
4. Click **Continue**

### Step 5: Configure Action 1 (Save to Google Sheets)

**Action Setup:**
1. Click **+** to add an action
2. **Choose App:** Search for "Google Sheets"
3. **Action Event:** "Create Spreadsheet Row"
4. Click **Continue**

**Connect Google Account:**
1. Click **Sign in to Google Sheets**
2. Authorize Zapier to access your Google account
3. Click **Continue**

**Configure Spreadsheet:**
1. **Drive:** My Google Drive
2. **Spreadsheet:** SST Affiliate Management
3. **Worksheet:** SST Affiliate Signups

**Map Fields:**
Map the WPForms fields to Google Sheets columns:

| Column | Field Mapping |
|--------|---------------|
| Timestamp | `{{zap_meta_human_now}}` or current date/time |
| Affiliate ID | Leave blank (formula will auto-generate) |
| First Name | `Full Name (First)` |
| Last Name | `Full Name (Last)` |
| Email | `Email Address` |
| Phone | `Phone Number` |
| Company | `Company / Organization` |
| Referral Source | `How did you hear about us?` |
| Motivation | `Why do you want to become an SST.NYC affiliate?` |
| Status | `Pending` (type manually) |
| Approved Date | Leave blank |
| Affiliate Link | Leave blank (Phase 2) |
| QR Code URL | Leave blank (Phase 2) |
| Total Referrals | `0` (type manually) |
| Total Revenue | `0` (type manually) |
| Notes | Leave blank |

**Test Action:**
1. Click **Test action**
2. Check your Google Sheet - a new row should appear
3. Click **Continue**

### Step 6: Configure Action 2 (Email Confirmation to Applicant)

**Action Setup:**
1. Click **+** to add another action
2. **Choose App:** Search for "Gmail" (or "Email by Zapier" for simpler option)
3. **Action Event:** "Send Email"
4. Click **Continue**

**Connect Gmail:**
1. Sign in to your Gmail account
2. Authorize Zapier
3. Click **Continue**

**Configure Email:**
- **To:** `{{Email Address}}` (from WPForms)
- **From Name:** SST.NYC Affiliate Team
- **From Email:** shawn@staging.sst.nyc (or your preferred email)
- **Reply To:** shawn@staging.sst.nyc
- **Subject:** Your SST.NYC Affiliate Application Received
- **Body Type:** HTML (if using Gmail) or Plain Text
- **Body:**

```html
Hi {{Full Name (First)}},

Thank you for applying to the SST.NYC Affiliate Program!

Application Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Name: {{Full Name (First)}} {{Full Name (Last)}}
Email: {{Email Address}}
Company: {{Company / Organization}}

Your application is now under review. Our team will evaluate your submission and respond within 2-3 business days.

What happens next?
✓ Our team reviews your application
✓ You'll receive approval or further instructions via email
✓ Once approved, you'll get your unique affiliate link and QR code
✓ Start earning 10% commission on every course referral!

Program Benefits:
• 10% commission on all course sales
• 60-day cookie tracking
• Custom QR codes for offline marketing
• Marketing materials provided
• Monthly payouts

Questions? Reply to this email or contact affiliates@sst.nyc

Best regards,
The SST.NYC Team

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SST.NYC - NYC DOB Site Safety Training
https://sst.nyc
```

**Test Action:**
1. Click **Test action**
2. Check the email inbox - you should receive the confirmation
3. Click **Continue**

### Step 7: Configure Action 3 (Admin Notification Email)

**Action Setup:**
1. Click **+** to add another action
2. **Choose App:** Gmail or Email by Zapier
3. **Action Event:** "Send Email"
4. Click **Continue**

**Configure Email:**
- **To:** shawn@staging.sst.nyc
- **From Name:** SST.NYC Affiliate System
- **Subject:** 🆕 New Affiliate Application: {{Full Name (First)}} {{Full Name (Last)}}
- **Body:**

```html
New affiliate application received!

Applicant Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Name: {{Full Name (First)}} {{Full Name (Last)}}
Email: {{Email Address}}
Phone: {{Phone Number}}
Company: {{Company / Organization}}

How They Heard About Us:
{{How did you hear about us?}}

Motivation:
{{Why do you want to become an SST.NYC affiliate?}}

Terms Accepted: {{Terms and Conditions}}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ACTION REQUIRED

1. Open Google Sheet: [ADD_LINK_TO_YOUR_SHEET]
2. Review application details (new row just added)
3. Update "Status" column to "Approved" or "Rejected"
4. If approved, Phase 2 automation will trigger QR code generation

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Submitted: {{zap_meta_human_now}}
Form ID: 5066
```

**Test Action:**
1. Click **Test action**
2. Check admin email
3. Click **Continue**

### Step 8: Name and Activate Zap

1. Click on "Untitled Zap" at the top
2. Rename to: **"SST Affiliate Signup - Phase 1"**
3. Click **Publish Zap**
4. Toggle to **ON**

---

## Testing the Complete Workflow

### Test Checklist

1. ✅ Visit form page: https://staging.sst.nyc/affiliate-signup/
2. ✅ Fill out form with test data
3. ✅ Submit form
4. ✅ Verify success message appears on page
5. ✅ Check Google Sheet - new row should appear with test data
6. ✅ Check Affiliate ID column - should auto-generate (e.g., 20251202-JS001)
7. ✅ Check applicant email inbox - confirmation email received
8. ✅ Check admin email - notification received
9. ✅ Check Zapier dashboard - Zap history shows successful run

### Troubleshooting

**Form submission not triggering Zapier:**
- Check WPForms → Settings → Integrations → Zapier is enabled
- Verify API key is correct
- Check Zapier trigger is set to correct form (ID: 5066)

**Google Sheet not updating:**
- Verify spreadsheet name and worksheet name match exactly
- Check column mapping in Zapier
- Ensure Google Sheets authorization hasn't expired

**Emails not sending:**
- Check Gmail authorization in Zapier
- Verify email addresses are valid
- Check spam folder
- Try "Email by Zapier" instead of Gmail if issues persist

**Affiliate ID not generating:**
- Ensure formula is in column B starting at B2
- Check that columns A (Timestamp) and C/D (First/Last Name) have data
- Formula: `=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))`

---

## Form Field IDs Reference

Use these field IDs when setting up Zapier mappings:

| Field | Field ID | Zapier Variable |
|-------|----------|-----------------|
| First Name | 0 | Full Name (First) |
| Last Name | 0 | Full Name (Last) |
| Email | 1 | Email Address |
| Phone | 2 | Phone Number |
| Company | 3 | Company / Organization |
| Referral Source | 4 | How did you hear about us? |
| Motivation | 5 | Why do you want to become an SST.NYC affiliate? |
| Terms | 6 | Terms and Conditions |

---

## Next Steps (Phase 2)

Once Phase 1 is working:
1. Create QR code generation system (Python MCP or Google Apps Script)
2. Set up Phase 2 Zapier workflow (Approval → QR Generation)
3. Add affiliate tracking code to WordPress
4. Create affiliate dashboard page

---

## Cost Summary

**Zapier Plan Needed:**
- **Free Plan:** 100 tasks/month (sufficient for testing)
- **Starter Plan:** $29.99/month - 750 tasks/month (recommended for production)

**Task Count Per Signup:**
- 1 task: Form submission trigger
- 1 task: Google Sheets row creation
- 2 tasks: Email sends (confirmation + admin notification)
- **Total:** 4 tasks per affiliate signup

**Example:** 50 signups/month = 200 tasks (fits in Starter plan)

---

## Support Resources

- **WPForms Zapier Docs:** https://wpforms.com/docs/how-to-connect-wpforms-with-zapier/
- **Zapier WPForms Integration:** https://zapier.com/apps/wpforms/integrations
- **Google Sheets Zapier Guide:** https://zapier.com/apps/google-sheets/help

---

**Last Updated:** December 2, 2025
**Status:** Ready for implementation
**Form URL:** https://staging.sst.nyc/affiliate-signup/
