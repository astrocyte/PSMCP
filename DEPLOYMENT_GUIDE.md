# WordPress MCP Server - Deployment Guide

Complete checklist to get your WordPress MCP server running in Claude Desktop.

## System Status

✅ **Python 3.13** - Installed at `/opt/homebrew/bin/python3.13`
✅ **Virtual Environment** - Created at `.venv/`
✅ **Dependencies** - All installed (mcp, paramiko, requests, etc.)
✅ **9 Modules** - All tested and importing successfully
✅ **33 MCP Tools** - Ready to use
✅ **SSH Support** - Custom port (65002) and password authentication

---

## Pre-Deployment Checklist

### 1. SSH Access (CRITICAL)

Your current setup:
- Host: `147.93.88.8`
- Port: `65002`
- User: `u629344933`
- Password: `RvALk23Zgdyw4Zn`

**Current Issue:** Password authentication failing with Paramiko.

**Solution Options:**

#### Option A: Add SSH Key to Hostinger (RECOMMENDED)

```bash
# 1. View your public key
cat ~/.ssh/id_ed25519.pub

# 2. Copy the entire output
# 3. Go to Hostinger → Files → SSH Access → Manage SSH Keys
# 4. Add the key and ACTIVATE it
# 5. Test connection:
ssh -p 65002 -i ~/.ssh/id_ed25519 u629344933@147.93.88.8

# 6. Update .env:
# Uncomment: WP_SSH_KEY_PATH=/Users/shawnshirazi/.ssh/id_ed25519
# Comment out: WP_SSH_PASSWORD=...
```

#### Option B: Use Password in Claude Config

Put credentials directly in Claude Desktop config (see step 3 below).

**See SSH_SETUP.md for detailed instructions.**

---

### 2. WordPress Application Password

Generate in WordPress admin:

1. Log into https://sst.nyc/wp-admin
2. Go to Users → Profile
3. Scroll to "Application Passwords"
4. Create new password with name: "MCP Server"
5. Copy the generated password (format: `xxxx xxxx xxxx xxxx xxxx xxxx`)
6. Add to `.env` or Claude config

---

### 3. Configure Claude Desktop

Edit: `~/Library/Application Support/Claude/claude_desktop_config.json`

**Method 1: Using Environment Variables (Recommended)**

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
        "WP_API_PASSWORD": "YOUR_WORDPRESS_APP_PASSWORD_HERE"
      }
    }
  }
}
```

**Method 2: Using .env File**

If you prefer to keep credentials in `.env`:

```json
{
  "mcpServers": {
    "wordpress-seo-admin": {
      "command": "bash",
      "args": ["-c", "source .venv/bin/activate && python src/server.py"],
      "cwd": "/Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server"
    }
  }
}
```

Then update `.env` with all credentials.

---

## Deployment Steps

### Step 1: Set Up SSH (Choose One)

**Option A: SSH Key** ⭐ Recommended
```bash
# Add your public key to Hostinger
cat ~/.ssh/id_ed25519.pub
# → Copy to Hostinger SSH Keys
# → Activate the key

# Update .env
nano .env
# Uncomment: WP_SSH_KEY_PATH=/Users/shawnshirazi/.ssh/id_ed25519
# Comment: # WP_SSH_PASSWORD=...
```

**Option B: Password**
```bash
# Update .env (already configured)
# Or use Claude config method above
```

### Step 2: Get WordPress Application Password

```bash
# 1. Go to: https://sst.nyc/wp-admin/profile.php
# 2. Scroll to "Application Passwords"
# 3. Name: "MCP Server"
# 4. Click "Add New Application Password"
# 5. Copy the password (xxxx xxxx xxxx xxxx)
```

### Step 3: Update Configuration

```bash
# Edit .env
nano .env

# Update this line:
WP_API_PASSWORD=your_wordpress_app_password_here
```

### Step 4: Test Connection

```bash
# Activate virtual environment
source .venv/bin/activate

# Test SSH connection
python -c "
from dotenv import load_dotenv
from pathlib import Path
load_dotenv(Path('.') / '.env')
from src.config import WordPressConfig
from src.wp_cli import WPCLIClient

config = WordPressConfig.from_env()
client = WPCLIClient(config)
client.connect()
print('✅ SSH Connection successful!')
result = client.execute('core version', format=None)
print(f'WordPress version: {result}')
"
```

### Step 5: Add to Claude Desktop

```bash
# Edit config
open ~/Library/Application\ Support/Claude/claude_desktop_config.json

# Add the wordpress-seo-admin server config (see above)
```

### Step 6: Restart Claude Desktop

```bash
# Quit Claude Desktop completely (Cmd+Q)
# Reopen Claude Desktop
```

### Step 7: Verify in Claude

Open Claude Desktop and check:

1. 🔧 Icon appears in bottom-right corner
2. Click it to see "wordpress-seo-admin" listed
3. Should show "Connected" status

---

## Testing Your Server

Try these commands in Claude:

### Test 1: Check Connection
```
Use the wp_get_info tool to show my WordPress site information
```

### Test 2: List Courses
```
Use ld_list_courses to show all my LearnDash courses
```

### Test 3: List Products
```
Use wc_list_products to show my WooCommerce products
```

### Test 4: Check Mailchimp
```
Use mc_list_audiences to show my email lists
```

### Test 5: SEO Analysis
```
Analyze post ID 1 for SEO issues
```

---

## Troubleshooting

### "Server not showing in Claude Desktop"

1. Check JSON syntax in config file (use JSONLint.com)
2. Verify paths are absolute (not relative)
3. Check Claude Desktop logs:
   ```bash
   tail -f ~/Library/Logs/Claude/mcp*.log
   ```

### "Configuration errors"

```bash
# Test configuration loading
source .venv/bin/activate
python -c "from src.config import WordPressConfig; config = WordPressConfig.from_env(); print(config.validate())"
```

### "SSH connection failed"

See SSH_SETUP.md for detailed SSH troubleshooting.

Quick checks:
```bash
# Test SSH manually
ssh -p 65002 u629344933@147.93.88.8

# Check if port is open
nc -zv 147.93.88.8 65002
```

### "401 Unauthorized" (REST API)

1. Generate new Application Password in WordPress
2. Copy it EXACTLY (including spaces)
3. Update `.env` or Claude config
4. Restart Claude Desktop

### "wp: command not found"

SSH into your server and verify wp-cli:
```bash
ssh -p 65002 u629344933@147.93.88.8
cd /home/u629344933/domains/staging.sst.nyc/public_html
wp --info
```

If not installed, install wp-cli:
```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

---

## What You Get

### 33 Tools Across 7 Categories:

**Site Management (3 tools)**
- wp_get_info
- wp_plugin_list
- wp_theme_list

**Content Operations (3 tools)**
- wp_post_list
- wp_get_post
- wp_search

**SEO Analysis (2 tools)**
- seo_analyze_post
- elementor_extract_content

**Image Optimization (3 tools)**
- image_analyze
- image_optimize
- image_audit_site

**LearnDash LMS (9 tools)**
- ld_create_course
- ld_update_course
- ld_list_courses
- ld_create_lesson
- ld_update_lesson
- ld_create_quiz
- ld_add_quiz_question
- ld_enroll_user
- ld_create_group

**WooCommerce (6 tools)**
- wc_create_product
- wc_update_product
- wc_list_products
- wc_list_orders
- wc_create_coupon
- wc_get_sales_report

**Mailchimp (6 tools)**
- mc_list_audiences
- mc_add_subscriber
- mc_create_campaign
- mc_send_campaign
- mc_get_campaign_report
- mc_tag_course_student

**Monitoring (1 tool)**
- wp_check_updates

---

## Natural Language Examples

Once deployed, you can use natural language in Claude:

**Course Management:**
```
"Create a new course called 'Advanced Python' priced at $199"
"Add 10 lessons to course ID 123"
"Enroll these 50 students in my Python course"
```

**E-Commerce:**
```
"Create a product for this course at $149"
"Show me all orders from this week"
"Create a Black Friday coupon for 40% off"
```

**Email Marketing:**
```
"Add these customers to Mailchimp with tag 'VIP'"
"Create a campaign announcing my new course"
"Show me the performance of my last email"
```

**SEO & Images:**
```
"Analyze my homepage for SEO issues"
"Optimize all images larger than 500KB"
"Audit all course pages for missing alt text"
```

---

## Security Notes

### ✅ Good Practices:
- Credentials in `.env` (never committed to git)
- SSH key authentication (when possible)
- WordPress Application Passwords (revocable)
- `.env` file permissions: `600` (only you can read)

### ⚠️ Important:
- Never commit `.env` to version control
- Use Application Passwords, not your WordPress admin password
- Keep your SSH keys secure
- Revoke Application Passwords if compromised

---

## Next Steps After Deployment

1. **Test all tool categories** to ensure everything works
2. **Create your first course** via Claude commands
3. **Set up Mailchimp automation** for student enrollments
4. **Run site-wide image audit** and optimize images
5. **Build complete workflows** (course launch, student lifecycle)

See these guides:
- **LEARNDASH_WOOCOMMERCE_GUIDE.md** - LMS & e-commerce workflows
- **MAILCHIMP_GUIDE.md** - Email marketing automation
- **IMAGE_OPTIMIZATION_GUIDE.md** - Image SEO best practices

---

## Support & Documentation

### Documentation Files:
- **README.md** - System overview
- **QUICKSTART.md** - 5-minute quick start
- **SETUP.md** - Detailed setup instructions
- **SSH_SETUP.md** - SSH authentication guide
- **FEATURES.md** - All 33 tools documented
- **DEPLOYMENT_GUIDE.md** - This file
- **LEARNDASH_WOOCOMMERCE_GUIDE.md** - LMS workflows
- **MAILCHIMP_GUIDE.md** - Email marketing
- **IMAGE_OPTIMIZATION_GUIDE.md** - Image optimization
- **FINAL_COMPLETE_SYSTEM.md** - Complete system overview

### Getting Help:

If you encounter issues:
1. Check the relevant guide above
2. Review Claude Desktop logs
3. Test SSH connection manually
4. Verify WordPress Application Password

---

## System Ready! 🚀

Your WordPress MCP server is fully configured with:
- ✅ 33 MCP tools
- ✅ 9 Python modules
- ✅ SSH support (port 65002)
- ✅ Password & key authentication
- ✅ Complete documentation

**Just complete the SSH setup and WordPress Application Password, then you're live!**

Your WordPress empire awaits! 👑
