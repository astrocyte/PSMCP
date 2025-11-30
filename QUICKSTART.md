# Quick Start Guide - WordPress MCP Server

Get up and running in 5 minutes!

## ✅ Prerequisites Checked

- ✅ Python 3.13 installed (`/opt/homebrew/bin/python3.13`)
- ✅ Virtual environment created (`.venv/`)
- ✅ All dependencies installed
- ✅ All 12 tools tested and working

## 🚀 Next: Configure Your WordPress Site

### Step 1: Create Environment File

```bash
cp .env.example .env
```

### Step 2: Edit .env with Your Credentials

Open `.env` and fill in your SST.NYC details:

```bash
# Your WordPress site
WP_SITE_URL=https://sst.nyc

# SSH credentials (from Hostinger)
WP_SSH_HOST=your-hostinger-server.com
WP_SSH_USER=your-ssh-username
WP_SSH_KEY_PATH=/Users/shawnshirazi/.ssh/id_ed25519
WP_REMOTE_PATH=/home/username/public_html

# WordPress REST API (generate in WordPress admin)
WP_API_USER=your-admin-username
WP_API_PASSWORD=xxxx xxxx xxxx xxxx xxxx xxxx
```

**Don't have these yet?** See `SETUP.md` for detailed instructions.

### Step 3: Add to Claude Desktop

Edit `~/Library/Application Support/Claude/claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "wordpress-seo-admin": {
      "command": "bash",
      "args": [
        "-c",
        "source .venv/bin/activate && python src/server.py"
      ],
      "cwd": "/Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server",
      "env": {
        "WP_SITE_URL": "https://staging.sst.nyc",
        "WP_SSH_HOST": "147.93.88.8",
        "WP_SSH_USER": "u629344933",
        "WP_SSH_PORT": "65002",
        "WP_SSH_PASSWORD": "RvALk23Zgdyw4Zn",
        "WP_REMOTE_PATH": "/home/u629344933/domains/staging.sst.nyc/public_html",
        "WP_API_USER": "admin",
        "WP_JWT_TOKEN": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3N0YWdpbmcuc3N0Lm55YyIsImlhdCI6MTc2Mzg3MjIxOCwiZXhwIjoxNzYzOTE1NDE4LCJ1c2VyX2lkIjoxLCJqdGkiOiIySWVHc0VrWXIwQUZyTURjeXM1MVNsMzh4OXpKM1QxZiJ9.wzt2mCxrViadi5T_drypnIXDTp54qhbxoz6K3U_MQYU"
      }
    }
  }
}
```

**Note:** You can either:
1. Put credentials in the `env` section above (shown), OR
2. Use the `.env` file (make sure it's in the project directory)

### Step 4: Restart Claude Desktop

Quit and reopen Claude Desktop. You should see:
- 🔧 Icon in the bottom-right showing MCP servers
- "wordpress-seo-admin" listed as connected
- 12 tools available when you ask about WordPress

## 🎯 Test It Out

Try these commands in Claude:

### Test 1: Site Info
```
Use the wp_get_info tool to check my WordPress site
```

### Test 2: Plugin List
```
List all active plugins on my WordPress site
```

### Test 3: Image Audit
```
Run an image audit on my site to find optimization opportunities
```

### Test 4: SEO Analysis
```
Analyze post ID 1 for SEO issues
```

## 📊 Your 12 Available Tools

### Site Management (3)
- `wp_get_info` - WordPress version, theme, plugins
- `wp_plugin_list` - List installed plugins
- `wp_theme_list` - List themes

### Content (3)
- `wp_post_list` - Query posts/pages
- `wp_get_post` - Get post details
- `wp_search` - Search content

### SEO (2)
- `seo_analyze_post` - Complete SEO audit
- `elementor_extract_content` - Parse Elementor pages

### Images (3)
- `image_analyze` - Analyze single image
- `image_optimize` - Convert/compress images
- `image_audit_site` - Bulk image audit

### Maintenance (1)
- `wp_check_updates` - Check for updates

## 🔧 Troubleshooting

### Server not showing in Claude Desktop?
1. Check JSON syntax in config file
2. Verify paths are absolute (not relative)
3. Check Claude Desktop logs: `~/Library/Logs/Claude/`

### "Configuration errors" when running?
1. Check `.env` file exists
2. Verify all required fields are filled
3. Test SSH connection manually: `ssh user@host`

### "wp: command not found"?
1. SSH into your server
2. Install wp-cli (see SETUP.md)
3. Verify path in `WP_REMOTE_PATH`

### "401 Unauthorized" from API?
1. Generate new Application Password in WordPress
2. Copy it exactly (with spaces) to `.env`
3. Verify username is correct

## 📚 Learn More

- **SETUP.md** - Complete setup instructions
- **FEATURES.md** - Detailed tool documentation
- **IMAGE_OPTIMIZATION_GUIDE.md** - Image optimization workflows
- **PROJECT_STATUS.md** - Full project overview

## 🎉 You're Ready!

Your WordPress MCP server is installed and tested. Once you add your credentials and restart Claude Desktop, you'll have full WordPress management capabilities through Claude Code!

**Happy optimizing! 🚀**
