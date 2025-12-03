#!/usr/bin/env python3
"""
Simple Google Sheet Creator - Uses browser OAuth
Creates SST Affiliate Management sheet with proper structure
"""

import gspread
from oauth2client.service_account import ServiceAccountCredentials
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request
import pickle
import os
from datetime import datetime

SCOPES = [
    'https://www.googleapis.com/auth/spreadsheets',
    'https://www.googleapis.com/auth/drive'
]

def authenticate():
    """Authenticate using OAuth flow"""
    creds = None
    token_file = 'token.pickle'

    # Check for existing token
    if os.path.exists(token_file):
        print("📂 Found existing token...")
        with open(token_file, 'rb') as token:
            creds = pickle.load(token)

    # If no valid credentials, authenticate
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            print("🔄 Refreshing token...")
            creds.refresh(Request())
        else:
            print("\n🌐 Opening browser for Google authentication...")
            print("   Please sign in and authorize access to Google Sheets\n")

            # Create a minimal OAuth credentials JSON on-the-fly
            client_config = {
                "installed": {
                    "client_id": "YOUR_CLIENT_ID.apps.googleusercontent.com",
                    "project_id": "quickstart",
                    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
                    "token_uri": "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
                    "client_secret": "YOUR_CLIENT_SECRET",
                    "redirect_uris": ["http://localhost"]
                }
            }

            print("⚠️  ERROR: OAuth credentials not configured")
            print("\nTo use OAuth, you need to:")
            print("1. Go to: https://console.cloud.google.com/apis/credentials")
            print("2. Create OAuth 2.0 Client ID (Desktop app)")
            print("3. Download JSON as 'credentials.json'")
            print("4. Place it in this directory")
            print("\nOR use the simpler CSV import method instead!")
            return None

        # Save token
        with open(token_file, 'wb') as token:
            pickle.dump(creds, token)

    return creds

def create_sheet_via_csv():
    """Alternative: Just open Google Sheets with the CSV file"""
    import webbrowser
    csv_path = os.path.join(os.path.dirname(__file__), 'affiliate_template.csv')

    print("\n" + "="*60)
    print("SIMPLE CSV UPLOAD METHOD")
    print("="*60)
    print("\n📋 I'll open Google Sheets for you...")
    print(f"📁 CSV file location: {csv_path}")
    print("\nSteps:")
    print("1. Google Sheets will open in your browser")
    print("2. Click 'Blank' or File → Import → Upload")
    print(f"3. Upload: {csv_path}")
    print("4. Choose 'Create new spreadsheet'")
    print("5. Follow the setup guide to add formulas\n")

    input("Press Enter to open Google Sheets...")

    webbrowser.open('https://sheets.google.com')
    webbrowser.open('file://' + csv_path)

    print("\n✅ Google Sheets opened!")
    print("📄 CSV file opened in your file browser")
    print("\n📖 See AFFILIATE_SHEET_SETUP.md for next steps")

if __name__ == "__main__":
    print("="*60)
    print("SST Affiliate Management - Sheet Creator")
    print("="*60)
    print("\n⚠️  OAuth requires credentials.json from Google Cloud Console")
    print("   This is complex for a one-time sheet creation.\n")
    print("Choose method:")
    print("  1. Simple CSV Upload (recommended)")
    print("  2. OAuth Automation (requires setup)")

    choice = input("\nEnter choice (1 or 2): ").strip()

    if choice == "1":
        create_sheet_via_csv()
    else:
        print("\n❌ OAuth method requires credentials.json setup")
        print("   Using CSV upload instead...\n")
        create_sheet_via_csv()
