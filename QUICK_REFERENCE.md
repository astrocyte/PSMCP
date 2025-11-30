# WordPress MCP Server - Quick Reference

## Your Server Details

```
Site: https://sst.nyc
SSH: u629344933@147.93.88.8:65002
Password: RvALk23Zgdyw4Zn
Path: /home/u629344933/domains/staging.sst.nyc/public_html
```

---

## Quick Start Commands

### Test Connection
```bash
# In project directory
source .venv/bin/activate
python src/server.py
```

### SSH Test
```bash
ssh -p 65002 u629344933@147.93.88.8
```

---

## Claude Desktop Config Location

```
~/Library/Application Support/Claude/claude_desktop_config.json
```

### Minimal Config
```json
{
  "mcpServers": {
    "wordpress-seo-admin": {
      "command": "bash",
      "args": ["-c", "source .venv/bin/activate && python src/server.py"],
      "cwd": "/Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server",
      "env": {
        "WP_SITE_URL": "https://sst.nyc",
        "WP_SSH_HOST": "147.93.88.8",
        "WP_SSH_USER": "u629344933",
        "WP_SSH_PORT": "65002",
        "WP_SSH_PASSWORD": "RvALk23Zgdyw4Zn",
        "WP_REMOTE_PATH": "/home/u629344933/domains/staging.sst.nyc/public_html",
        "WP_API_USER": "admin",
        "WP_API_PASSWORD": "YOUR_APP_PASSWORD"
      }
    }
  }
}
```

---

## Essential Commands in Claude

### Course Management
```
"Create a course called 'Python Basics' at $99"
"Add 10 lessons to course ID 123"
"Enroll john@example.com in course 123"
"List all my courses"
```

### Product Management
```
"Create a product for this course at $149"
"List all products"
"Show orders from this week"
"Create a 30% off coupon"
```

### Email Marketing
```
"List my Mailchimp audiences"
"Add subscriber@example.com with tag 'VIP'"
"Create campaign: New Course Launch"
"Show campaign performance for ID abc123"
```

### SEO & Images
```
"Analyze post 1 for SEO"
"Audit all site images"
"Optimize images over 500KB"
"Check homepage SEO"
```

---

## Tool Categories (33 Total)

### WordPress Core (3)
- wp_get_info
- wp_plugin_list
- wp_theme_list

### Content (3)
- wp_post_list
- wp_get_post
- wp_search

### SEO (2)
- seo_analyze_post
- elementor_extract_content

### Images (3)
- image_analyze
- image_optimize
- image_audit_site

### LearnDash (9)
- ld_create_course
- ld_update_course
- ld_list_courses
- ld_create_lesson
- ld_update_lesson
- ld_create_quiz
- ld_add_quiz_question
- ld_enroll_user
- ld_create_group

### WooCommerce (6)
- wc_create_product
- wc_update_product
- wc_list_products
- wc_list_orders
- wc_create_coupon
- wc_get_sales_report

### Mailchimp (6)
- mc_list_audiences
- mc_add_subscriber
- mc_create_campaign
- mc_send_campaign
- mc_get_campaign_report
- mc_tag_course_student

### Monitoring (1)
- wp_check_updates

---

## Troubleshooting Quick Fixes

### Server not appearing in Claude
```bash
# Check config syntax
cat ~/Library/Application\ Support/Claude/claude_desktop_config.json | python -m json.tool

# Restart Claude
pkill -9 Claude
open -a Claude
```

### SSH failing
```bash
# Test manually
ssh -p 65002 u629344933@147.93.88.8

# Add SSH key to Hostinger
cat ~/.ssh/id_ed25519.pub
# → Go to Hostinger → SSH Keys → Add & Activate
```

### Tools not working
```bash
# Test configuration
source .venv/bin/activate
python -c "from src.config import WordPressConfig; c=WordPressConfig.from_env(); print(c.validate())"
```

---

## Documentation Index

1. **README.md** - System overview
2. **DEPLOYMENT_GUIDE.md** - Complete deployment steps ⭐
3. **QUICKSTART.md** - 5-minute setup
4. **SSH_SETUP.md** - SSH configuration
5. **QUICK_REFERENCE.md** - This file
6. **FEATURES.md** - All 33 tools
7. **LEARNDASH_WOOCOMMERCE_GUIDE.md** - LMS workflows
8. **MAILCHIMP_GUIDE.md** - Email marketing
9. **IMAGE_OPTIMIZATION_GUIDE.md** - Image SEO
10. **SETUP.md** - Detailed setup
11. **CLAUDE.md** - AI agent instructions
12. **FINAL_COMPLETE_SYSTEM.md** - System overview

---

## Two Steps to Go Live

### 1. Set Up SSH (RECOMMENDED)
```bash
# Copy your public key
cat ~/.ssh/id_ed25519.pub

# Add to Hostinger:
# → Files → SSH Access → Manage SSH Keys
# → Add key and ACTIVATE
```

### 2. Get WordPress App Password
```bash
# Go to: https://sst.nyc/wp-admin/profile.php
# → Scroll to "Application Passwords"
# → Name: "MCP Server"
# → Copy password
# → Add to Claude config above
```

**Then restart Claude Desktop and you're live!**

---

## Support

- Check logs: `tail -f ~/Library/Logs/Claude/mcp*.log`
- Test modules: `source .venv/bin/activate && python src/server.py`
- Read guides: See documentation index above

---

**Your WordPress empire is ready to launch! 🚀**
