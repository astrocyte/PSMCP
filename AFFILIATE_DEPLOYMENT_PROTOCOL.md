# Affiliate Program Deployment Protocol

## Overview
This document describes the complete deployment process for the Predictive Safety affiliate program from staging to production.

**Last Updated:** December 3, 2025
**Production URL:** https://sst.nyc/somos/
**Staging URL:** https://staging.sst.nyc/ (page ID 5082)

---

## Architecture

### Components
1. **Custom WordPress Plugin** (`sst-affiliate-zapier-webhook.php`)
   - Hooks into WPForms submission (form ID varies by environment)
   - Disables HTML5 email validation (allows .nyc domains)
   - Custom success message with gradient design
   - Sends webhook to Zapier
   - Admin settings page at Settings → SST Affiliate Webhook

2. **WPForms Form**
   - Staging: Form ID 5066
   - Production: Form ID 5025
   - Title: "Predictive Safety Affiliate Program"
   - Fields: Name, Email, Phone, Company, Referral Source, Motivation, Terms

3. **Landing Page** (`/somos/`)
   - Responsive two-column layout
   - Mobile-first design (form appears first on mobile)
   - Hero section: "Somos - Grow Together"
   - Commission: "At least 10%" with negotiation blurb
   - Production: Page ID 5053

4. **Zapier Integration**
   - Webhook URL: `https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/`
   - Action: Update Google Sheets with affiliate applications
   - Auto-generates Affiliate ID in sheet

5. **Email Notifications**
   - Handled by WP Mail SMTP Pro (already installed on production)
   - Optional: Configure WPForms notifications in Phase 1.5

---

## Deployment Process

### Prerequisites
- SSH access to Hostinger server
- sshpass installed locally (for password authentication)
- wp-cli available on production server

### SSH Connection
```bash
# Standard connection
ssh -p 65002 u629344933@147.93.88.8

# With sshpass (for automation)
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8
```

### Server Paths
- **Production:** `/home/u629344933/domains/sst.nyc/public_html`
- **Staging:** `/home/u629344933/domains/sst.nyc/public_html/staging`
- **Backups:** `/tmp/production_backup_*.sql` (temp location on server)

---

## Manual Deployment Steps

### Step 1: Copy Plugin
```bash
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
'cp /home/u629344933/domains/sst.nyc/public_html/staging/wp-content/plugins/sst-affiliate-zapier-webhook.php \
/home/u629344933/domains/sst.nyc/public_html/wp-content/plugins/'
```

### Step 2: Activate Plugin
```bash
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
'cd /home/u629344933/domains/sst.nyc/public_html && wp plugin activate sst-affiliate-zapier-webhook'
```

### Step 3: Configure Webhook URL
```bash
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
'cd /home/u629344933/domains/sst.nyc/public_html && \
wp option update sst_zapier_webhook_url "https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/"'
```

### Step 4: Export Form from Staging
```bash
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
'cd /home/u629344933/domains/sst.nyc/public_html/staging && \
wp post get 5066 --field=post_content' > /tmp/form_data.txt
```

### Step 5: Update Form on Production
**Important:** Production form ID is 5025 (not 5066)

```bash
# Update form with new configuration
# Remember to change staging.sst.nyc URLs to sst.nyc in terms link
# Update form description from "Join and earn 10% commission" to "Join and earn at least 10% commission"
```

### Step 6: Export Page Content from Staging
```bash
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
'cd /home/u629344933/domains/sst.nyc/public_html/staging && \
wp post get 5082 --field=post_content' > /tmp/page_content.txt
```

### Step 7: Create/Update Page on Production
**Important:** Update form shortcode from `[wpforms id="5066"]` to `[wpforms id="5025"]`

```bash
# If page doesn't exist:
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
"cd /home/u629344933/domains/sst.nyc/public_html && \
wp post create --post_type=page --post_title='Somos - Grow Together' \
--post_name=somos --post_status=publish --post_content='[CONTENT_HERE]'"

# If page exists, update it:
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
"cd /home/u629344933/domains/sst.nyc/public_html && \
wp post update 5053 --post_content='[CONTENT_HERE]'"
```

### Step 8: Flush Cache
```bash
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 -o StrictHostKeyChecking=no u629344933@147.93.88.8 \
'cd /home/u629344933/domains/sst.nyc/public_html && wp cache flush'
```

---

## Verification Checklist

After deployment, verify:

1. **Plugin Status**
   ```bash
   wp plugin list --name=sst-affiliate-zapier-webhook --field=status
   # Should return: active
   ```

2. **Webhook URL**
   ```bash
   wp option get sst_zapier_webhook_url
   # Should return: https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/
   ```

3. **Page URL**
   ```bash
   wp post list --post_type=page --name=somos --field=url
   # Should return: https://sst.nyc/somos/
   ```

4. **Manual Testing**
   - Visit: https://sst.nyc/somos/
   - Check layout (two columns on desktop, form first on mobile)
   - Submit test application
   - Verify success message displays
   - Check Zapier task history: https://zapier.com/app/history
   - Confirm Google Sheets updates
   - Verify Affiliate ID auto-generates

---

## Important URLs

### Production
- **Affiliate Page:** https://sst.nyc/somos/
- **Plugin Settings:** https://sst.nyc/wp-admin/options-general.php?page=sst-affiliate-webhook
- **Form Editor:** https://sst.nyc/wp-admin/admin.php?page=wpforms-builder&view=fields&form_id=5025
- **WordPress Admin:** https://sst.nyc/wp-admin/

### Staging
- **Test Page:** https://staging.sst.nyc/ (page with form, ID 5082)
- **Form ID:** 5066

### External Services
- **Zapier History:** https://zapier.com/app/history
- **Google Sheets:** [SST Affiliate Management Sheet]

---

## Known Issues & Solutions

### Issue: wp db export fails with exit code 255
**Status:** Ongoing - database export command fails during automated deployment
**Workaround:** Skip automated backup, rely on Hostinger's automatic backups
**Alternative:** Create manual backup via Hostinger panel before deployment

### Issue: Form ID mismatch between environments
**Solution:** Always update shortcode from staging form ID (5066) to production form ID (5025)

### Issue: Staging URLs in content
**Solution:** Replace `staging.sst.nyc` with `sst.nyc` in all links (especially affiliate terms link)

---

## Rollback Procedure

If deployment fails:

1. **Deactivate Plugin**
   ```bash
   wp plugin deactivate sst-affiliate-zapier-webhook
   ```

2. **Delete Plugin**
   ```bash
   wp plugin delete sst-affiliate-zapier-webhook
   rm -f /home/u629344933/domains/sst.nyc/public_html/wp-content/plugins/sst-affiliate-zapier-webhook.php
   ```

3. **Remove Configuration**
   ```bash
   wp option delete sst_zapier_webhook_url
   ```

4. **Delete Page** (if needed)
   ```bash
   wp post delete 5053 --force
   ```

---

## Key Design Decisions

1. **Custom Plugin over WPForms Pro**
   - WPForms Lite doesn't support native Zapier integration
   - Custom plugin provides full control
   - No recurring subscription costs
   - More maintainable

2. **WP Mail SMTP Pro for Emails**
   - Already installed on production
   - Zapier only handles Google Sheets integration
   - Simpler architecture, fewer points of failure

3. **"At Least 10%" Language**
   - Allows flexibility for higher commission negotiations
   - Includes call-to-action: "Think you deserve more? Reach out and convince us."

4. **URL: /somos/**
   - Spanish for "we are"
   - Implies togetherness and mutual benefit
   - Hero: "Somos - Grow Together"
   - Tagline: "When construction professionals succeed together, everyone wins"

5. **Responsive Design**
   - CSS Grid for flexible two-column layout
   - Mobile-first: Form appears before content on small screens
   - Sticky form card on desktop
   - clamp() for fluid typography

---

## Future Enhancements

### Phase 1.5: Email Notifications
Configure WPForms to send:
1. **Applicant Confirmation Email**
   - Subject: "Your SST.NYC Affiliate Application Received"
   - Body: Confirmation + what happens next

2. **Admin Notification Email**
   - To: support@sst.nyc
   - Subject: "New Affiliate Application"
   - Body: Applicant details for review

### Phase 2: Affiliate Dashboard
- Unique referral links
- QR code generation
- Real-time commission tracking
- Payout management
- Marketing materials download

---

## Troubleshooting

### Form doesn't submit
- Check webhook URL in plugin settings
- Verify Zapier Zap is turned ON
- Check browser console for JavaScript errors
- Test webhook manually: `curl -X POST [webhook_url] -d '{"test":"data"}'`

### Success message not appearing
- Verify plugin is active
- Check WPForms AJAX setting is enabled
- Clear WordPress cache
- Check for theme conflicts

### Zapier not receiving data
- Verify webhook URL is correct
- Check Zapier task history for errors
- Test webhook in Zapier editor
- Ensure all required fields are being sent

### Layout broken on mobile
- Check CSS media query at 900px breakpoint
- Verify form-card-wrapper has `order: 1`
- Test in Chrome DevTools mobile emulator

---

## Contact

**Support Email:** support@sst.nyc
**Company:** Predictive Safety
**Domain:** sst.nyc
