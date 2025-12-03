# SST Affiliate Management Sheet Setup

## Quick Upload Method (Recommended - 2 minutes)

1. **Upload the CSV:**
   - Go to https://sheets.google.com
   - Click "File" → "Import" → "Upload"
   - Select `affiliate_template.csv` from this directory
   - Choose "Replace spreadsheet" and click "Import data"

2. **Rename the spreadsheet:**
   - Click "Untitled spreadsheet" at top
   - Rename to: **SST Affiliate Management**
   - Rename "Sheet1" to: **SST Affiliate Signups**

3. **Add the Affiliate ID Formula:**
   - Click cell **B2**
   - Paste this formula:
   ```
   =IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))
   ```
   - Press Enter
   - This will auto-generate IDs like `20251202-JD001`

4. **Format the headers:**
   - Select row 1 (A1:P1)
   - Click Format → Text → Bold
   - Click Format → Fill color → Choose blue
   - Click View → Freeze → 1 row

5. **Add Status dropdown:**
   - Select cells J2:J1000 (Status column)
   - Click Data → Data validation
   - Criteria: "List of items"
   - Enter: `Pending,Approved,Rejected`
   - Check "Show dropdown list in cell"
   - Click Save

6. **Test the formula:**
   - Add data to row 3 (Timestamp, First Name, Last Name)
   - Watch cell B3 auto-generate the Affiliate ID!

7. **Delete sample row:**
   - Right-click row 2 → Delete row
   - Your sheet is ready!

8. **Share the link:**
   - Click "Share" button (top right)
   - Change "Restricted" to "Anyone with the link"
   - Set to "Editor"
   - Copy the link and send it to me

---

## Alternative: Python Script Method

If you want full automation:

### Requirements
```bash
python3 -m pip install gspread google-auth google-auth-oauthlib
```

### Setup OAuth Credentials
1. Go to https://console.cloud.google.com/apis/credentials
2. Create new project: "SST Affiliate Management"
3. Enable Google Sheets API and Google Drive API
4. Create OAuth 2.0 Client ID (Desktop app)
5. Download JSON and save as `credentials.json` in this directory

### Run the script
```bash
python3 create_affiliate_sheet_oauth.py
```

This will:
- Authenticate via browser OAuth flow
- Create the spreadsheet automatically
- Add all formulas and formatting
- Return the shareable link

---

## Column Reference

| Column | Name | Purpose | Auto-filled |
|--------|------|---------|-------------|
| A | Timestamp | When form was submitted | By Zapier |
| B | Affiliate ID | Unique ID (format: YYYYMMDD-XX000) | By formula |
| C | First Name | Applicant first name | From form |
| D | Last Name | Applicant last name | From form |
| E | Email | Contact email | From form |
| F | Phone | Phone number | From form |
| G | Company | Company/organization name | From form |
| H | Referral Source | How they heard about SST | From form |
| I | Motivation | Why they want to be affiliate | From form |
| J | Status | Pending/Approved/Rejected | Manual |
| K | Approved Date | When status changed to Approved | Phase 2 |
| L | Affiliate Link | Unique tracking URL | Phase 2 |
| M | QR Code URL | Link to QR code image | Phase 2 |
| N | Total Referrals | Number of sales | Phase 2 |
| O | Total Revenue | Total commission earned | Phase 2 |
| P | Notes | Admin notes | Manual |

---

## Affiliate ID Formula Explained

```
=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))
```

**Breakdown:**
- `IF(A2="","",...)` - Only generate if Timestamp exists
- `TEXT(A2,"YYYYMMDD")` - Convert timestamp to YYYYMMDD (e.g., 20251202)
- `UPPER(LEFT(C2,1))` - First letter of First Name (uppercase)
- `UPPER(LEFT(D2,1))` - First letter of Last Name (uppercase)
- `TEXT(ROW()-1,"000")` - Sequential number (001, 002, etc.)

**Example:**
- Timestamp: 12/02/2025
- First Name: John
- Last Name: Doe
- Result: `20251202-JD001`

---

## Next Steps After Setup

1. Copy the Google Sheet URL
2. Configure Zapier to write to this sheet
3. Test the workflow end-to-end
4. Set up Phase 2 (QR code generation on approval)
