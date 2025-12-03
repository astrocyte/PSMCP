# Production Deployment Checklist
## Predictive Safety Affiliate Program

**Date Created:** December 2, 2025
**Deploying From:** staging.sst.nyc
**Deploying To:** sst.nyc (production)

---

## 📋 Pre-Deployment Checklist

### **Before Running the Script:**

- [ ] **Staging fully tested**
  - [ ] Form submits successfully
  - [ ] Success message displays correctly
  - [ ] Webhook reaches Zapier (check history)
  - [ ] Google Sheets updates with test data
  - [ ] Affiliate ID formula generates correctly

- [ ] **Production backup created**
  - The script creates a database backup automatically
  - But you may want to create a manual backup via Hostinger panel

- [ ] **Zapier configured**
  - [ ] Webhook URL is correct: `https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/`
  - [ ] Google Sheets action is working
  - [ ] Zap is turned ON

- [ ] **Email notifications ready** (optional for Phase 1)
  - [ ] WP Mail SMTP Pro is active on production
  - [ ] Configure WPForms notifications after deployment

- [ ] **You have SSH access**
  - Test: `ssh -p 65002 u629344933@147.93.88.8`

---

## 🚀 Deployment Steps

### **Option 1: Automated Deployment (Recommended)**

```bash
cd /Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server

# Review the script first
cat deploy_to_production.sh

# Run deployment
./deploy_to_production.sh
```

**What the script does:**
1. Creates database backup
2. Copies custom plugin from staging → production
3. Activates plugin on production
4. Configures webhook URL
5. Exports form from staging
6. Creates/updates form on production
7. Creates/updates `/yourmoney/` page
8. Flushes cache
9. Runs verification checks

**Estimated time:** 2-3 minutes

---

### **Option 2: Manual Deployment**

If you prefer to do it manually:

```bash
# 1. Deploy plugin
scp -P 65002 sst-affiliate-zapier-webhook.php u629344933@147.93.88.8:/home/u629344933/domains/sst.nyc/public_html/wp-content/plugins/

ssh -p 65002 u629344933@147.93.88.8
cd /home/u629344933/domains/sst.nyc/public_html
wp plugin activate sst-affiliate-zapier-webhook
wp option update sst_zapier_webhook_url 'https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/'

# 2. Export form from staging
cd /home/u629344933/domains/sst.nyc/public_html/staging
wp post get 5066 --field=post_content > /tmp/form_data.json

# 3. Create form on production
cd /home/u629344933/domains/sst.nyc/public_html
wp post create --post_type=wpforms --post_title='Predictive Safety Affiliate Program' --post_status=publish --post_content="$(cat /tmp/form_data.json)" --porcelain
# Note the form ID returned

# 4. Create page (replace FORM_ID with ID from step 3)
wp post create --post_type=page --post_title='Earn 10% Per Referral' --post_name=yourmoney --post_status=publish --post_content='[Content with wpforms id="FORM_ID"]'

# 5. Flush cache
wp cache flush
```

---

## ✅ Post-Deployment Verification

After deployment, check:

### **1. Plugin Active**
- Visit: https://sst.nyc/wp-admin/plugins.php
- Verify "SST Affiliate Zapier Webhook" is active
- Visit: https://sst.nyc/wp-admin/options-general.php?page=sst-affiliate-webhook
- Verify webhook URL is configured

### **2. Page Live**
- Visit: https://sst.nyc/yourmoney/
- Check layout looks correct (two columns on desktop)
- Verify form displays
- Check mobile view (form should appear first)

### **3. Form Functional**
- Submit a test application
- Check for success message
- Verify data reaches Zapier (check history)
- Confirm Google Sheets updates
- Check Affiliate ID generates

### **4. Email Notifications** (if configured)
- Submit test with your email
- Verify confirmation email received
- Check admin notification email

---

## 🔧 Configuration After Deployment

### **WPForms Email Notifications** (Optional - Phase 1.5)

1. Go to: https://sst.nyc/wp-admin/admin.php?page=wpforms-builder&view=settings&form_id=XXXX
2. Click "Notifications"
3. Configure:
   - **Notification 1:** Applicant confirmation
   - **Notification 2:** Admin notification

See `wordpress-mcp-server/ZAPIER_SETUP_INSTRUCTIONS.md` for email templates.

---

## 🚨 Rollback Plan

If something goes wrong:

```bash
cd /Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server
./rollback_production.sh
```

This will:
- Deactivate and remove the custom plugin
- Delete webhook configuration
- Show you how to delete the page/form manually

**Database backup location:** `/tmp/production_backup_YYYYMMDD_HHMMSS.sql` on server

To restore database:
```bash
ssh -p 65002 u629344933@147.93.88.8
cd /home/u629344933/domains/sst.nyc/public_html
wp db import /tmp/production_backup_YYYYMMDD_HHMMSS.sql
```

---

## 📊 What Gets Deployed

| Item | Source | Destination | Notes |
|------|--------|-------------|-------|
| Custom Plugin | staging/wp-content/plugins/ | production/wp-content/plugins/ | Handles webhooks |
| Webhook Config | Option in staging DB | Option in production DB | Zapier URL |
| WPForms Form | Form ID 5066 (staging) | New form (production) | Affiliate application |
| Page Content | Page ID 5082 (staging) | /yourmoney/ (production) | Landing page |

---

## 🎯 Success Criteria

Deployment is successful when:

- [x] Plugin is active on production
- [x] Webhook URL is configured
- [x] Page is accessible at https://sst.nyc/yourmoney/
- [x] Form submits without errors
- [x] Success message displays correctly
- [x] Zapier receives webhook data
- [x] Google Sheets updates with submission
- [x] Affiliate ID auto-generates
- [x] Layout is responsive (desktop + mobile)

---

## 📞 Support

**If deployment fails:**
1. Run rollback script immediately
2. Check error logs: `ssh -p 65002 u629344933@147.93.88.8 'tail -50 /home/u629344933/domains/sst.nyc/public_html/wp-content/debug.log'`
3. Review deployment script output
4. Contact support if needed

**If form doesn't work:**
- Check webhook URL in plugin settings
- Verify Zapier Zap is ON
- Check Zapier task history for errors
- Test webhook manually: `curl -X POST [webhook_url] -d '{"test":"data"}'`

---

## 📝 Important URLs After Deployment

**Production Site:**
- Affiliate Page: https://sst.nyc/yourmoney/
- WordPress Admin: https://sst.nyc/wp-admin/
- Plugin Settings: https://sst.nyc/wp-admin/options-general.php?page=sst-affiliate-webhook
- Form Editor: https://sst.nyc/wp-admin/admin.php?page=wpforms-builder

**External Services:**
- Zapier History: https://zapier.com/app/history
- Google Sheet: [Your SST Affiliate Management sheet]

---

## 🔄 Future Updates

To update the page/form after initial deployment:

**Update Page Content:**
```bash
ssh -p 65002 u629344933@147.93.88.8
cd /home/u629344933/domains/sst.nyc/public_html
wp post update <PAGE_ID> --post_content='<new content>'
```

**Update Form:**
Edit via WordPress admin at:
https://sst.nyc/wp-admin/admin.php?page=wpforms-builder&view=fields&form_id=<FORM_ID>

**Update Plugin:**
Copy new version to server and reload.

---

**Ready to deploy?** Review this checklist, then run `./deploy_to_production.sh`

**Questions before deploying?** Stop and ask!
