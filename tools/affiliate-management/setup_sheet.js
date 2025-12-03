/**
 * Google Apps Script to automatically setup SST Affiliate Management Sheet
 *
 * Instructions:
 * 1. Open your Google Sheet: https://docs.google.com/spreadsheets/d/1kj91Gh4sXc_s1T3ud8Ip13ZJy-STLqBqF5tiBuWnohY/edit
 * 2. Click Extensions → Apps Script
 * 3. Delete any existing code
 * 4. Paste this entire script
 * 5. Click the Save icon (💾)
 * 6. Click Run (▶️)
 * 7. Authorize the script when prompted
 * 8. Close the Apps Script tab and refresh your sheet
 */

function setupAffiliateSheet() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheets()[0]; // Get first sheet

  Logger.log('Starting SST Affiliate Management sheet setup...');

  // 1. Rename the spreadsheet
  ss.rename('SST Affiliate Management');
  Logger.log('✅ Renamed spreadsheet');

  // 2. Rename the sheet tab
  sheet.setName('SST Affiliate Signups');
  Logger.log('✅ Renamed sheet tab');

  // 3. Format header row
  const headerRange = sheet.getRange('A1:P1');
  headerRange.setFontWeight('bold');
  headerRange.setBackground('#4A90E2'); // Blue background
  headerRange.setFontColor('#FFFFFF'); // White text
  headerRange.setHorizontalAlignment('center');
  Logger.log('✅ Formatted header row');

  // 4. Freeze header row
  sheet.setFrozenRows(1);
  Logger.log('✅ Froze header row');

  // 5. Add Affiliate ID formula to B2
  const formulaCell = sheet.getRange('B2');
  const formula = '=IF(A2="","",TEXT(A2,"YYYYMMDD") & "-" & UPPER(LEFT(C2,1)) & UPPER(LEFT(D2,1)) & TEXT(ROW()-1,"000"))';
  formulaCell.setFormula(formula);
  Logger.log('✅ Added Affiliate ID formula to B2');

  // 6. Add data validation for Status column (J2:J1000)
  const statusRange = sheet.getRange('J2:J1000');
  const rule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Pending', 'Approved', 'Rejected'], true)
    .setAllowInvalid(false)
    .setHelpText('Select status: Pending, Approved, or Rejected')
    .build();
  statusRange.setDataValidation(rule);
  Logger.log('✅ Added Status dropdown validation');

  // 7. Set column widths for better readability
  sheet.setColumnWidth(1, 150);  // Timestamp
  sheet.setColumnWidth(2, 150);  // Affiliate ID
  sheet.setColumnWidth(3, 100);  // First Name
  sheet.setColumnWidth(4, 100);  // Last Name
  sheet.setColumnWidth(5, 200);  // Email
  sheet.setColumnWidth(6, 120);  // Phone
  sheet.setColumnWidth(7, 150);  // Company
  sheet.setColumnWidth(8, 150);  // Referral Source
  sheet.setColumnWidth(9, 250);  // Motivation
  sheet.setColumnWidth(10, 100); // Status
  sheet.setColumnWidth(11, 120); // Approved Date
  sheet.setColumnWidth(12, 250); // Affiliate Link
  sheet.setColumnWidth(13, 250); // QR Code URL
  sheet.setColumnWidth(14, 80);  // Total Referrals
  sheet.setColumnWidth(15, 100); // Total Revenue
  sheet.setColumnWidth(16, 200); // Notes
  Logger.log('✅ Set column widths');

  // 8. Format the sample data row (row 2)
  const sampleRow = sheet.getRange('A2:P2');
  sampleRow.setBackground('#F5F5F5'); // Light gray to indicate it's sample data
  Logger.log('✅ Formatted sample row');

  // 9. Add notes to key cells
  sheet.getRange('B1').setNote('Auto-generated from Timestamp + First/Last Name initials + Sequential number');
  sheet.getRange('J1').setNote('Dropdown: Pending, Approved, or Rejected');
  Logger.log('✅ Added cell notes');

  // 10. Protect the formula column (B) so it doesn't get overwritten
  const formulaColumn = sheet.getRange('B2:B1000');
  const protection = formulaColumn.protect();
  protection.setDescription('Affiliate ID Formula - Auto-generated, do not edit');
  protection.setWarningOnly(true); // Allow editing with warning
  Logger.log('✅ Protected formula column');

  Logger.log('\n' + '='.repeat(60));
  Logger.log('✅ SST AFFILIATE MANAGEMENT SHEET SETUP COMPLETE!');
  Logger.log('='.repeat(60));
  Logger.log('\nSheet is ready to use with Zapier automation!');
  Logger.log('Next step: Configure Zapier to write to this sheet');

  // Show success message to user
  SpreadsheetApp.getUi().alert(
    '✅ Setup Complete!',
    'Your SST Affiliate Management sheet is now fully configured:\n\n' +
    '✓ Affiliate ID auto-generation formula\n' +
    '✓ Status dropdown (Pending/Approved/Rejected)\n' +
    '✓ Formatted headers and columns\n' +
    '✓ Frozen header row\n\n' +
    'Ready to connect with Zapier!',
    SpreadsheetApp.getUi().ButtonSet.OK
  );
}

// Optional: Function to test the Affiliate ID generation
function testAffiliateIDGeneration() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheets()[0];

  // Add test data to row 3
  sheet.getRange('A3').setValue(new Date()); // Current timestamp
  sheet.getRange('C3').setValue('Jane');      // First Name
  sheet.getRange('D3').setValue('Smith');     // Last Name
  sheet.getRange('E3').setValue('jane.smith@example.com');
  sheet.getRange('F3').setValue('(555) 987-6543');
  sheet.getRange('G3').setValue('XYZ Corp');
  sheet.getRange('H3').setValue('Referral');
  sheet.getRange('I3').setValue('Test affiliate signup');
  sheet.getRange('J3').setValue('Pending');
  sheet.getRange('N3').setValue('0');
  sheet.getRange('O3').setValue('$0.00');

  // Copy formula from B2 to B3
  sheet.getRange('B2').copyTo(sheet.getRange('B3'));

  Logger.log('✅ Test data added to row 3');
  Logger.log('Check cell B3 for generated Affiliate ID');

  SpreadsheetApp.getUi().alert(
    '✅ Test Data Added!',
    'Added test affiliate "Jane Smith" to row 3.\n\n' +
    'Check column B to see the auto-generated Affiliate ID!',
    SpreadsheetApp.getUi().ButtonSet.OK
  );
}
