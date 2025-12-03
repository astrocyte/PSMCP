# SST.NYC Tech Stack Inventory

**Last Updated:** December 2, 2025
**Site:** https://staging.sst.nyc
**Environment:** Staging (mirrors production)

---

## 🎯 Core WordPress

- **WordPress Version:** Latest (auto-updated by Hostinger)
- **PHP Version:** 8.x
- **Theme:** Astra 4.11.14 (active)
- **Hosting:** Hostinger Shared Hosting
- **SSH Access:** Port 65002, user: u629344933

---

## 📦 Active Plugins (48 total)

### **🎓 LMS & Education**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| sfwd-lms (LearnDash) | 4.25.6 | Core LMS platform | Site Safety Training courses |
| learndash-certificate-builder | 1.1.3.1 | Certificates | DOB compliance certificates |
| learndash-course-timer | 1.1.2 | Course timing | Track completion time |
| learndash-elementor | 1.0.11 | Visual builder integration | Custom course pages |
| learndash-notifications | 1.6.6 | Student notifications | Course enrollment alerts |
| learndash-woocommerce | 2.0.2 | E-commerce integration | Sell courses |
| learndash-zapier | 2.3.1 | Automation | Course enrollment automation |
| Dynamic-Learndash-for-Elementor | 0.3 | Advanced Elementor widgets | Custom course displays |

### **🛒 E-Commerce (WooCommerce)**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| woocommerce | 10.3.5 | Core e-commerce | Course sales, certificates |
| woocommerce-gateway-stripe | 10.1.0 | Payment processing | Credit card payments |
| woocommerce-direct-checkout | 3.5.7 | Streamlined checkout | Skip cart page |
| woocommerce-shipping | 2.0.1 | Shipping (not used) | Digital products only |
| woo-update-manager | 1.0.3 | Plugin updates | License management |
| funnel-builder | 3.13.1.3 | Sales funnels | Upsells, landing pages |

### **🎨 Page Builder & Design**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| elementor | 3.33.1 | Page builder | Primary design tool |
| elementor-pro | 3.33.1 | Pro features | Forms, popups, theme builder |
| elementskit-lite | 3.7.5 | Extra widgets | Enhanced Elementor features |
| skyboot-custom-icons-for-elementor | 1.1.0 | Icon packs | Custom icons |
| image-optimization | 1.6.9 | Image compression | Hostinger optimizer |

### **📧 Email & Marketing**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| wp-mail-smtp-pro | 4.7.0 | **Email delivery** | **SMTP configuration - USE THIS!** |
| mailchimp-for-wp | 4.10.8 | Email marketing | Newsletter signups |
| mailchimp-for-woocommerce | 5.6 | E-commerce emails | Customer sync |

### **🔗 Automation & Integration**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| zapier | 1.5.3 | Workflow automation | General Zapier integration |
| wordpress-mcp | 0.2.4 | Claude Code integration | MCP server for automation |
| sst-affiliate-zapier-webhook | 1.0 | **Custom affiliate webhook** | **Sends form data to Zapier** |
| sst-affiliate-banner | 1.0 | Affiliate promotions | Custom banners |

### **📝 Forms**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| wpforms | 1.9.8.5 | **Form builder** | **Affiliate signup form (Form ID: 5066)** |
| wpforms-lite | 1.9.8.4 (inactive) | Free version | Not needed - using Pro |

### **👥 Users & Access**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| userswp | 1.2.48 | User profiles | Student profiles |
| user-menus | 1.3.2 | Menu visibility | Role-based menus |
| password-protected | 2.7.12 | Site protection | Staging site password |
| pojo-accessibility | 3.9.0 (inactive) | Accessibility | WCAG compliance |

### **🔧 Development & Utilities**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| code-snippets | 3.9.2 | Custom code | PHP/CSS/JS snippets |
| code-snippets-pro | 3.9.2 | Pro snippets | Advanced features |
| aryo-activity-log | 2.11.2 | Activity logging | Track changes |
| connect-polylang-elementor | 2.5.3 | Multi-language | If needed later |

### **🏢 Hostinger Tools**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| hostinger | 3.0.54 | Hostinger integration | Hosting features |
| hostinger-ai-assistant | 3.0.16 | AI tools | Content suggestions |
| hostinger-reach | 1.2.4 | Analytics | Visitor tracking |

### **📊 Analytics & SEO**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| google-site-kit | 1.166.0 | Google integration | Analytics, Search Console |
| google-listings-and-ads | 3.5.0 | Google Ads | Product listings |

### **⚡ Performance**
| Plugin | Version | Purpose | Notes |
|--------|---------|---------|-------|
| litespeed-cache | 7.6.2 (inactive) | Caching | Not using currently |
| object-cache.php | dropin | Redis cache | Hostinger managed |

---

## 🎯 Key Integrations We're Using

### **Current Active Workflows:**

#### 1. **Affiliate Program (NEW - Phase 1)**
- **Form:** WPForms (ID: 5066)
- **Webhook Plugin:** sst-affiliate-zapier-webhook.php
- **Zapier:** Webhook trigger → Google Sheets
- **Email:** WP Mail SMTP Pro (native WordPress emails)
- **Status:** ✅ Configured, testing phase

#### 2. **LearnDash + WooCommerce**
- **Purpose:** Sell courses through WooCommerce
- **Flow:** Product purchase → Course enrollment → Certificate
- **Status:** ✅ Active

#### 3. **LearnDash + Zapier**
- **Purpose:** Enrollment automation
- **Plugin:** learndash-zapier (2.3.1)
- **Status:** Available for use

#### 4. **Mailchimp Marketing**
- **Purpose:** Email campaigns, student communications
- **Integration:** mailchimp-for-wp + mailchimp-for-woocommerce
- **Status:** ✅ Active

---

## 📋 Important Form IDs

| Form Name | ID | Purpose | Status |
|-----------|----|---------| -------|
| SST.NYC Affiliate Program Application | 5066 | Affiliate signups | ✅ Active |

---

## 📄 Important Page IDs

| Page Name | ID | URL | Status |
|-----------|----|----- |--------|
| Join Our Affiliate Program | 5067 | /affiliate-signup/ | ✅ Published |

---

## 🔐 Email Configuration

**WP Mail SMTP Pro is ACTIVE**

- **Status:** ✅ Configured
- **Provider:** (Check WP Admin → WP Mail SMTP → Settings)
- **Use for:**
  - ✅ WPForms notifications
  - ✅ WooCommerce order emails
  - ✅ LearnDash student emails
  - ✅ Affiliate program confirmations

**Don't use Zapier for emails - use WP Mail SMTP Pro instead!**

---

## 🎓 LearnDash Course Structure

**Courses Available:**
- 10-Hour Construction
- 30-Hour Scaffold
- 32-Hour Supervisor
- 8-Hour Renewal
- 16-Hour Refresher
- 40-Hour Demolition

**Course Slugs for Affiliate Links:**
- `/courses/10hr-construction/?ref=AFFILIATE_ID`
- `/courses/30hr-scaffold/?ref=AFFILIATE_ID`
- `/courses/32hr-supervisor/?ref=AFFILIATE_ID`
- `/courses/8hr-renewal/?ref=AFFILIATE_ID`
- `/courses/16hr-refresher/?ref=AFFILIATE_ID`
- `/courses/40hr-demolition/?ref=AFFILIATE_ID`

---

## 🚀 Custom Plugins (Our Code)

### 1. **sst-affiliate-zapier-webhook**
- **Location:** `/wp-content/plugins/sst-affiliate-zapier-webhook.php`
- **Purpose:** Send affiliate form submissions to Zapier
- **Features:**
  - Disables HTML5 email validation (allows .nyc domains)
  - Custom success message with gradient design
  - Sends webhook to Zapier on form submit
  - Admin settings page: Settings → SST Webhook
- **Status:** ✅ Active

### 2. **sst-affiliate-banner**
- **Location:** `/wp-content/plugins/`
- **Purpose:** Display affiliate promotional banners
- **Status:** ✅ Active

---

## 🔄 Inactive But Available Plugins

**Keep these disabled unless needed:**
- wpforms-lite (using Pro version)
- litespeed-cache (conflicts with Hostinger cache)
- wp-file-manager (security risk)
- woocommerce-zapier (using custom webhook instead)
- migrate-guru (only needed for migrations)
- template-kit-import (only needed for theme imports)

---

## ⚙️ Server Configuration

**SSH Access:**
```bash
ssh -p 65002 u629344933@147.93.88.8
```

**Staging Site Path:**
```
/home/u629344933/domains/sst.nyc/public_html/staging/
```

**WP-CLI Available:** ✅ Yes
```bash
cd /home/u629344933/domains/sst.nyc/public_html/staging
wp plugin list
wp post list
wp user list
```

---

## 🎯 Recommended Actions

### **For Affiliate Program:**
1. ✅ **Use WPForms notifications** for emails (not Zapier)
2. ✅ **Use Zapier only for** Google Sheets integration
3. ✅ Keep webhook plugin active
4. ⏳ Configure WPForms email templates

### **For Performance:**
1. Keep object-cache.php (Redis) active
2. Use Hostinger image optimization
3. Don't activate LiteSpeed cache (conflicts)

### **For Security:**
1. Keep wp-file-manager disabled
2. Use password-protected plugin for staging
3. Regular plugin updates via Hostinger

---

## 📞 Quick Reference

**WordPress Admin:** https://staging.sst.nyc/wp-admin/
**Affiliate Form:** https://staging.sst.nyc/affiliate-signup/
**WP Mail SMTP Settings:** WP Admin → WP Mail SMTP
**WPForms Editor:** WP Admin → WPForms → All Forms
**Zapier Webhook URL:** https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/
**Google Sheet:** SST Affiliate Management

---

**This inventory helps Claude Code understand your complete tech stack when making recommendations and configurations.**
