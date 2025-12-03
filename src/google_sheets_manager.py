"""
Google Sheets Manager for WordPress MCP Server
Provides Google Sheets integration for affiliate management and data automation
"""

import gspread
from google.oauth2.service_account import Credentials
from google.auth import default
from typing import List, Dict, Any, Optional
import os
import json


class GoogleSheetsManager:
    """Manage Google Sheets operations for SST.NYC affiliate program and automation"""

    def __init__(self, credentials_path: Optional[str] = None):
        """
        Initialize Google Sheets manager

        Args:
            credentials_path: Path to service account JSON or None for default credentials
        """
        self.credentials_path = credentials_path
        self.gc = None
        self._authenticate()

    def _authenticate(self):
        """Authenticate with Google Sheets API"""
        scopes = [
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/drive'
        ]

        try:
            if self.credentials_path and os.path.exists(self.credentials_path):
                # Use service account credentials
                creds = Credentials.from_service_account_file(
                    self.credentials_path,
                    scopes=scopes
                )
            else:
                # Use application default credentials (gcloud auth)
                creds, _ = default(scopes=scopes)

            self.gc = gspread.authorize(creds)

        except Exception as e:
            raise Exception(
                f"Failed to authenticate with Google Sheets: {e}\n"
                "Setup options:\n"
                "1. Run: gcloud auth application-default login\n"
                "2. Or provide service account JSON path"
            )

    def create_spreadsheet(self, title: str) -> Dict[str, Any]:
        """
        Create a new Google Spreadsheet

        Args:
            title: Name of the spreadsheet

        Returns:
            dict with spreadsheet info
        """
        try:
            spreadsheet = self.gc.create(title)
            return {
                'success': True,
                'spreadsheet_id': spreadsheet.id,
                'url': spreadsheet.url,
                'title': title
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def open_spreadsheet(self, spreadsheet_id: str = None, title: str = None):
        """
        Open an existing spreadsheet by ID or title

        Args:
            spreadsheet_id: The spreadsheet ID from URL
            title: The spreadsheet title

        Returns:
            gspread Spreadsheet object
        """
        try:
            if spreadsheet_id:
                return self.gc.open_by_key(spreadsheet_id)
            elif title:
                return self.gc.open(title)
            else:
                raise ValueError("Must provide either spreadsheet_id or title")
        except Exception as e:
            raise Exception(f"Failed to open spreadsheet: {e}")

    def add_row(self, spreadsheet_id: str, worksheet_name: str, values: List[Any]) -> Dict[str, Any]:
        """
        Add a row to a worksheet

        Args:
            spreadsheet_id: The spreadsheet ID
            worksheet_name: Name of the worksheet
            values: List of values for the row

        Returns:
            dict with success status
        """
        try:
            spreadsheet = self.open_spreadsheet(spreadsheet_id=spreadsheet_id)
            worksheet = spreadsheet.worksheet(worksheet_name)
            worksheet.append_row(values)

            return {
                'success': True,
                'row_added': values,
                'worksheet': worksheet_name
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def update_cell(self, spreadsheet_id: str, worksheet_name: str,
                   row: int, col: int, value: Any) -> Dict[str, Any]:
        """
        Update a specific cell

        Args:
            spreadsheet_id: The spreadsheet ID
            worksheet_name: Name of the worksheet
            row: Row number (1-indexed)
            col: Column number (1-indexed)
            value: Value to set

        Returns:
            dict with success status
        """
        try:
            spreadsheet = self.open_spreadsheet(spreadsheet_id=spreadsheet_id)
            worksheet = spreadsheet.worksheet(worksheet_name)
            worksheet.update_cell(row, col, value)

            return {
                'success': True,
                'cell': f'{row},{col}',
                'value': value
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def get_all_values(self, spreadsheet_id: str, worksheet_name: str) -> Dict[str, Any]:
        """
        Get all values from a worksheet

        Args:
            spreadsheet_id: The spreadsheet ID
            worksheet_name: Name of the worksheet

        Returns:
            dict with all values
        """
        try:
            spreadsheet = self.open_spreadsheet(spreadsheet_id=spreadsheet_id)
            worksheet = spreadsheet.worksheet(worksheet_name)
            values = worksheet.get_all_values()

            return {
                'success': True,
                'values': values,
                'row_count': len(values)
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def find_row(self, spreadsheet_id: str, worksheet_name: str,
                query: str, col: int = 1) -> Dict[str, Any]:
        """
        Find a row by searching for a value in a column

        Args:
            spreadsheet_id: The spreadsheet ID
            worksheet_name: Name of the worksheet
            query: Value to search for
            col: Column number to search in (1-indexed)

        Returns:
            dict with row data if found
        """
        try:
            spreadsheet = self.open_spreadsheet(spreadsheet_id=spreadsheet_id)
            worksheet = spreadsheet.worksheet(worksheet_name)
            cell = worksheet.find(query, in_column=col)

            if cell:
                row_values = worksheet.row_values(cell.row)
                return {
                    'success': True,
                    'found': True,
                    'row': cell.row,
                    'values': row_values
                }
            else:
                return {
                    'success': True,
                    'found': False
                }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def setup_affiliate_sheet(self, spreadsheet_id: str) -> Dict[str, Any]:
        """
        Setup SST Affiliate Management sheet with formulas and formatting

        Args:
            spreadsheet_id: The spreadsheet ID

        Returns:
            dict with setup status
        """
        try:
            spreadsheet = self.open_spreadsheet(spreadsheet_id=spreadsheet_id)

            # Rename spreadsheet
            spreadsheet.update_title('SST Affiliate Management')

            # Get first worksheet and rename
            worksheet = spreadsheet.sheet1
            worksheet.update_title('SST Affiliate Signups')

            # Add headers
            headers = [
                'Timestamp', 'Affiliate ID', 'First Name', 'Last Name', 'Email',
                'Phone', 'Company', 'Referral Source', 'Motivation', 'Status',
                'Approved Date', 'Affiliate Link', 'QR Code URL',
                'Total Referrals', 'Total Revenue', 'Notes'
            ]
            worksheet.update('A1:P1', [headers])

            # Format header row
            worksheet.format('A1:P1', {
                'textFormat': {'bold': True, 'foregroundColor': {'red': 1, 'green': 1, 'blue': 1}},
                'backgroundColor': {'red': 0.29, 'green': 0.56, 'blue': 0.89},
                'horizontalAlignment': 'CENTER'
            })

            # Freeze header row
            worksheet.freeze(rows=1)

            # Add Affiliate ID formula
            formula = '=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))'
            worksheet.update('B2', formula)

            # Add data validation for Status column
            from gspread.datavalidation import DataValidationRule, BooleanCondition
            status_rule = DataValidationRule(
                BooleanCondition('ONE_OF_LIST', ['Pending', 'Approved', 'Rejected']),
                showCustomUi=True
            )
            worksheet.add_validation('J2:J1000', status_rule)

            # Set column widths
            worksheet.columns_auto_resize(0, 15)

            return {
                'success': True,
                'spreadsheet_id': spreadsheet_id,
                'url': spreadsheet.url,
                'message': 'Affiliate sheet setup complete with formulas and formatting'
            }

        except Exception as e:
            return {'success': False, 'error': str(e)}

    def batch_update(self, spreadsheet_id: str, worksheet_name: str,
                    range_name: str, values: List[List[Any]]) -> Dict[str, Any]:
        """
        Batch update multiple cells at once

        Args:
            spreadsheet_id: The spreadsheet ID
            worksheet_name: Name of the worksheet
            range_name: A1 notation range (e.g., 'A1:D5')
            values: 2D list of values

        Returns:
            dict with success status
        """
        try:
            spreadsheet = self.open_spreadsheet(spreadsheet_id=spreadsheet_id)
            worksheet = spreadsheet.worksheet(worksheet_name)
            worksheet.update(range_name, values)

            return {
                'success': True,
                'range': range_name,
                'cells_updated': len(values) * len(values[0]) if values else 0
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}
