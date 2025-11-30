# 🚀 START HERE - WordPress MCP Server

## ✅ System Status: READY TO DEPLOY

Your WordPress MCP Server is **100% complete** with 33 tools ready to use!

---

## 📋 Two Simple Steps to Go Live

### Step 1: Set Up SSH Authentication (5 minutes)

You have two options:

#### Option A: SSH Key (Recommended - Most Reliable)

1. **Copy your public key:**
   ```bash
   cat ~/.ssh/id_ed25519.pub
   ```

2. **Add to Hostinger:**
   - Log into Hostinger control panel (hpanel)
   - Go to: **Files** → **SSH Access** → **Manage SSH Keys**
   - Click **Add New SSH Key**
   - Paste your public key
   - Name it "MacBook Pro"
   - **Important: ACTIVATE the key** (toggle switch)

3. **Test it works:**
   ```bash
   ssh -p 65002 -i ~/.ssh/id_ed25519 u629344933@147.93.88.8
   ```
   Should connect without password!

4. **Update .env:**
   ```bash
   nano .env
   # Uncomment: WP_SSH_KEY_PATH=/Users/shawnshirazi/.ssh/id_ed25519
   # Comment out: # WP_SSH_PASSWORD=...
   ```

#### Option B: Use Password in Claude Config

Skip SSH key setup and put password directly in Claude Desktop config (see Step 2).

**Need help? See SSH_SETUP.md**

---

### Step 2: Get WordPress Application Password (2 minutes)

1. **Go to WordPress:**
   ```
   https://sst.nyc/wp-admin/profile.php
   ```

2. **Scroll to "Application Passwords"**

3. **Create new password:**
   - Name: "MCP Server"
   - Click "Add New Application Password"

4. **Copy the password** (format: `xxxx xxxx xxxx xxxx xxxx xxxx`)

---

### Step 3: Add to Claude Desktop (3 minutes)

1. **Edit Claude Desktop config:**
   ```bash
   nano ~/Library/Application\ Support/Claude/claude_desktop_config.json
   ```

2. **Add this configuration:**
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

3. **Replace `YOUR_WORDPRESS_APP_PASSWORD_HERE`** with the password from Step 2

4. **If using SSH key instead of password:**
   - Remove the `WP_SSH_PASSWORD` line
   - Add: `"WP_SSH_KEY_PATH": "/Users/shawnshirazi/.ssh/id_ed25519"`

---

### Step 4: Launch! (1 minute)

1. **Restart Claude Desktop**
   - Quit completely (Cmd+Q)
   - Reopen Claude Desktop

2. **Check connection:**
   - Look for 🔧 icon in bottom-right
   - Click it
   - Should see "wordpress-seo-admin" connected

3. **Test with first command:**
   ```
   Use the wp_get_info tool to show my WordPress site information
   ```

---

## 🎯 What You Can Do Now

### Course Management
```
"Create a course called 'Python Basics' priced at $99"
"Add 10 lessons covering variables, functions, classes..."
"Create a quiz with 20 questions"
"Enroll these 50 students in the course"
```

### E-Commerce
```
"Create a WooCommerce product for this course at $149"
"Show me all orders from this week"
"Create a Black Friday coupon for 40% off"
"Link this product to course ID 123"
```

### Email Marketing
```
"List my Mailchimp audiences"
"Add subscriber@example.com with tag 'VIP Customer'"
"Create a campaign announcing my new course"
"Show me the performance of campaign abc123"
```

### SEO & Images
```
"Analyze my homepage for SEO issues"
"Audit all site images for optimization"
"Convert all images over 500KB to WebP"
"Check post 123 for missing alt text"
```

---

## 📚 Documentation Available

All guides are in the project directory:

1. **START_HERE.md** - This file (quick start)
2. **DEPLOYMENT_GUIDE.md** - Complete deployment steps
3. **QUICK_REFERENCE.md** - Command reference card
4. **SSH_SETUP.md** - SSH troubleshooting
5. **SYSTEM_COMPLETE.md** - Full system overview
6. **LEARNDASH_WOOCOMMERCE_GUIDE.md** - Course workflows
7. **MAILCHIMP_GUIDE.md** - Email automation
8. **IMAGE_OPTIMIZATION_GUIDE.md** - Image SEO
9. **FEATURES.md** - All 33 tools documented

---

## 🆘 Quick Troubleshooting

### Server not showing in Claude Desktop?
```bash
# Check config syntax
python -m json.tool ~/Library/Application\ Support/Claude/claude_desktop_config.json
```

### SSH connection failing?
```bash
# Test manually
ssh -p 65002 u629344933@147.93.88.8

# See SSH_SETUP.md for solutions
```

### WordPress API 401 error?
- Double-check Application Password is correct
- Make sure username is "admin"
- Try generating a new Application Password

---

## ✅ System Ready!

**You have:**
- ✅ 33 MCP tools
- ✅ 9 Python modules
- ✅ 13 documentation guides
- ✅ SSH support (port 65002)
- ✅ Password & key authentication
- ✅ Complete LMS platform
- ✅ Full e-commerce
- ✅ Email marketing
- ✅ SEO optimization
- ✅ Image optimization

**Total deployment time: ~10 minutes**

---

## 🎊 Ready to Launch Your WordPress Empire!

Follow the 4 steps above and you'll be managing your entire WordPress platform via natural language in Claude Code!

**Let's go! 🚀**
