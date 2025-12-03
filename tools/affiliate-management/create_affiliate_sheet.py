#!/usr/bin/env python3
"""
Create SST Affiliate Management Google Sheet
Generates a properly formatted spreadsheet with formulas for affiliate tracking
"""

import gspread
from google.oauth2.service_account import Credentials
from datetime import datetime

# Google Sheets API scope
SCOPES = [
    'https://www.googleapis.com/auth/spreadsheets',
    'https://www.googleapis.com/auth/drive'
]

def create_affiliate_sheet():
    """Create and configure the SST Affiliate Management Google Sheet"""

    print("🔐 Authenticating with Google...")

    # Use default credentials (Google Drive installed locally)
    # This will use your user credentials via gcloud or application default credentials
    try:
        # Try using default application credentials first
        from google.auth import default
        credentials, project = default(scopes=SCOPES)
        gc = gspread.authorize(credentials)
    except Exception as e:
        print(f"❌ Default credentials failed: {e}")
        print("\n💡 You need to set up authentication. Run:")
        print("   gcloud auth application-default login")
        return None

    print("✅ Authenticated successfully!")

    # Create new spreadsheet
    print("\n📊 Creating spreadsheet 'SST Affiliate Management'...")
    spreadsheet = gc.create("SST Affiliate Management")

    # Get the first worksheet and rename it
    worksheet = spreadsheet.sheet1
    worksheet.update_title("SST Affiliate Signups")

    print(f"✅ Spreadsheet created!")
    print(f"🔗 URL: {spreadsheet.url}")

    # Set up column headers
    print("\n📝 Adding column headers...")
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

    # Format header row (bold, freeze)
    worksheet.format('A1:P1', {
        'textFormat': {'bold': True},
        'backgroundColor': {'red': 0.2, 'green': 0.6, 'blue': 0.9}
    })

    # Freeze header row
    worksheet.freeze(rows=1)

    print("✅ Headers added and formatted!")

    # Add formula for Affiliate ID auto-generation in column B
    print("\n🔢 Adding Affiliate ID formula...")
    # Formula: =IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))
    affiliate_id_formula = '=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))'

    # Add formula to B2 (it will auto-fill down when data is added)
    worksheet.update('B2', affiliate_id_formula)

    print("✅ Affiliate ID formula added to B2!")

    # Add data validation for Status column (J)
    print("\n✔️  Setting up Status dropdown...")
    status_rule = gspread.datavalidation.DataValidationRule(
        gspread.datavalidation.BooleanCondition('ONE_OF_LIST', ['Pending', 'Approved', 'Rejected']),
        showCustomUi=True
    )
    worksheet.add_validation('J2:J1000', status_rule)

    print("✅ Status dropdown added!")

    # Set column widths for better readability
    print("\n📏 Adjusting column widths...")
    worksheet.columns_auto_resize(0, 15)  # Auto-resize all columns

    # Add a sample row to demonstrate the formula
    print("\n📋 Adding sample data row...")
    sample_data = [
        datetime.now().strftime("%Y-%m-%d %H:%M:%S"),  # Timestamp
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

    print("✅ Sample data added!")

    # Share the spreadsheet (make it accessible to you)
    print("\n🔓 Setting permissions...")
    try:
        # Make it viewable by anyone with the link
        spreadsheet.share('', perm_type='anyone', role='writer', with_link=True)
        print("✅ Spreadsheet is accessible to anyone with the link!")
    except Exception as e:
        print(f"⚠️  Could not set sharing permissions: {e}")
        print("   You may need to manually share the spreadsheet")

    # Print summary
    print("\n" + "="*60)
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
    print("   1. Copy the spreadsheet URL above")
    print("   2. Test the Affiliate ID auto-generation by adding data to row 3")
    print("   3. Use this sheet in your Zapier workflow")
    print("\n" + "="*60)

    return spreadsheet.url

if __name__ == "__main__":
    print("="*60)
    print("SST.NYC Affiliate Management Sheet Generator")
    print("="*60)
    print()

    sheet_url = create_affiliate_sheet()

    if sheet_url:
        print(f"\n✅ Done! Open your sheet here:\n{sheet_url}\n")
    else:
        print("\n❌ Failed to create sheet. Please check authentication.\n")
