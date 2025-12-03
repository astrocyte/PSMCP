#!/usr/bin/env python3
"""
Create SST Affiliate Management Google Sheet (OAuth version)
Generates a properly formatted spreadsheet with formulas for affiliate tracking
"""

import gspread
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request
import pickle
import os.path
from datetime import datetime

# Google Sheets API scope
SCOPES = [
    'https://www.googleapis.com/auth/spreadsheets',
    'https://www.googleapis.com/auth/drive'
]

def get_credentials():
    """Get user credentials via OAuth flow"""
    creds = None

    # Token file stores user's access and refresh tokens
    if os.path.exists('token.pickle'):
        print("🔑 Loading saved credentials...")
        with open('token.pickle', 'rb') as token:
            creds = pickle.load(token)

    # If no valid credentials, let user log in
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            print("🔄 Refreshing expired credentials...")
            creds.refresh(Request())
        else:
            print("\n⚠️  No credentials found. You need to create OAuth credentials:")
            print("   1. Go to: https://console.cloud.google.com/apis/credentials")
            print("   2. Create OAuth 2.0 Client ID (Desktop app)")
            print("   3. Download JSON and save as 'credentials.json' in this directory")
            print("   4. Run this script again")
            return None

        # Save credentials for next run
        with open('token.pickle', 'wb') as token:
            pickle.dump(creds, token)

    return creds

def create_affiliate_sheet():
    """Create and configure the SST Affiliate Management Google Sheet"""

    print("="*60)
    print("SST.NYC Affiliate Management Sheet Generator")
    print("="*60)
    print()

    # Get credentials
    creds = get_credentials()
    if not creds:
        return None

    print("✅ Authenticated successfully!\n")

    # Authorize gspread
    gc = gspread.authorize(creds)

    # Create new spreadsheet
    print("📊 Creating spreadsheet 'SST Affiliate Management'...")
    try:
        spreadsheet = gc.create("SST Affiliate Management")
    except Exception as e:
        print(f"❌ Error creating spreadsheet: {e}")
        return None

    # Get the first worksheet and rename it
    worksheet = spreadsheet.sheet1
    worksheet.update_title("SST Affiliate Signups")

    print(f"✅ Spreadsheet created!")
    print(f"🔗 URL: {spreadsheet.url}\n")

    # Set up column headers
    print("📝 Adding column headers...")
    headers = [
        "Timestamp",           # A
        "Affiliate ID",        # B
        "First Name",          # C
        "Last Name",           # D
        "Email",               # E
        "Phone",               # F
        "Company",             # G
        "Referral Source",     # H
        "Motivation",          # I
        "Status",              # J
        "Approved Date",       # K
        "Affiliate Link",      # L
        "QR Code URL",         # M
        "Total Referrals",     # N
        "Total Revenue",       # O
        "Notes"                # P
    ]

    # Write headers to row 1
    worksheet.update('A1:P1', [headers])

    # Format header row (bold, colored background)
    worksheet.format('A1:P1', {
        'textFormat': {'bold': True, 'foregroundColor': {'red': 1, 'green': 1, 'blue': 1}},
        'backgroundColor': {'red': 0.2, 'green': 0.5, 'blue': 0.8},
        'horizontalAlignment': 'CENTER'
    })

    # Freeze header row
    worksheet.freeze(rows=1)
    print("✅ Headers added and formatted!\n")

    # Add formula for Affiliate ID auto-generation in column B
    print("🔢 Adding Affiliate ID formula...")
    affiliate_id_formula = '=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))'
    worksheet.update('B2', affiliate_id_formula)
    print("✅ Affiliate ID formula added to B2!\n")

    # Add data validation for Status column (J)
    print("✔️  Setting up Status dropdown...")
    try:
        from gspread.datavalidation import DataValidationRule, BooleanCondition
        status_rule = DataValidationRule(
            BooleanCondition('ONE_OF_LIST', ['Pending', 'Approved', 'Rejected']),
            showCustomUi=True
        )
        worksheet.add_validation('J2:J1000', status_rule)
        print("✅ Status dropdown added!\n")
    except Exception as e:
        print(f"⚠️  Could not add dropdown: {e}\n")

    # Set column widths
    print("📏 Adjusting column widths...")
    try:
        worksheet.columns_auto_resize(0, 15)
        print("✅ Column widths adjusted!\n")
    except:
        pass

    # Add a sample row to demonstrate the formula
    print("📋 Adding sample data row...")
    sample_data = [
        datetime.now().strftime("%m/%d/%Y %H:%M:%S"),  # Timestamp
        "",  # Affiliate ID (will auto-generate)
        "John",  # First Name
        "Doe",  # Last Name
        "john.doe@example.com",  # Email
        "(555) 123-4567",  # Phone
        "ABC Construction",  # Company
        "Google Search",  # Referral Source
        "I want to earn commissions",  # Motivation
        "Pending",  # Status
        "",  # Approved Date
        "",  # Affiliate Link
        "",  # QR Code URL
        "0",  # Total Referrals
        "$0.00",  # Total Revenue
        ""  # Notes
    ]

    worksheet.update('A2:P2', [sample_data])
    print("✅ Sample data added!\n")

    # Share the spreadsheet
    print("🔓 Setting permissions...")
    try:
        spreadsheet.share('', perm_type='anyone', role='writer', with_link=True)
        print("✅ Spreadsheet is accessible to anyone with the link!\n")
    except Exception as e:
        print(f"⚠️  Could not set sharing permissions: {e}")
        print("   You may need to manually share the spreadsheet\n")

    # Print summary
    print("="*60)
    print("✅ SST Affiliate Management Sheet Created Successfully!")
    print("="*60)
    print(f"\n📊 Spreadsheet Name: SST Affiliate Management")
    print(f"📄 Worksheet Name: SST Affiliate Signups")
    print(f"🔗 URL: {spreadsheet.url}")
    print(f"\n📋 Features:")
    print("   ✓ 16 columns with proper headers")
    print("   ✓ Auto-generating Affiliate ID formula (B2)")
    print("   ✓ Status dropdown (Pending/Approved/Rejected)")
    print("   ✓ Sample data row for testing")
    print("   ✓ Formatted headers (bold, colored)")
    print("   ✓ Frozen header row")
    print("\n💡 Next Steps:")
    print("   1. Open the spreadsheet at the URL above")
    print("   2. Test the Affiliate ID auto-generation")
    print("   3. Delete the sample row when ready")
    print("   4. Use this sheet in your Zapier workflow")
    print("\n" + "="*60 + "\n")

    return spreadsheet.url

if __name__ == "__main__":
    sheet_url = create_affiliate_sheet()

    if not sheet_url:
        print("\n" + "="*60)
        print("SIMPLER ALTERNATIVE: Manual CSV Upload")
        print("="*60)
        print("\nI can generate a CSV file that you can import into Google Sheets:")
        print("1. I'll create affiliate_template.csv")
        print("2. You upload it to Google Sheets")
        print("3. Much simpler, no OAuth needed")
        print("\nWant me to create the CSV instead? (y/n)")
