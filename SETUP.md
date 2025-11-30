# WordPress MCP Server Setup Guide

Complete guide to setting up the WordPress MCP server for Claude Code.

## Prerequisites

### 1. Python Environment
- Python 3.10 or higher
- pip or uv for package management

### 2. WordPress Server Requirements
- SSH access to your WordPress hosting (Hostinger)
- wp-cli installed on the server
- WordPress REST API enabled (default in modern WordPress)

### 3. WordPress Configuration
- Admin account with full permissions
- Application Password generated for REST API access

## Step-by-Step Setup

### Step 1: Check wp-cli on Server

SSH into your Hostinger server and verify wp-cli is installed:

```bash
ssh your-username@your-server.hostinger.com
wp --version
```

If not installed, install wp-cli:

```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

### Step 2: Generate WordPress Application Password

1. Log into your WordPress admin dashboard
2. Go to **Users** → **Profile**
3. Scroll down to **Application Passwords** section
4. Enter name: `MCP Server`
5. Click **Add New Application Password**
6. Copy the generated password (you won't see it again!)

### Step 3: Set Up SSH Key (Recommended)

Generate SSH key pair if you don't have one:

```bash
ssh-keygen -t ed25519 -C "wordpress-mcp"
```

Copy public key to server:

```bash
ssh-copy-id -i ~/.ssh/id_ed25519.pub your-username@your-server.hostinger.com
```

### Step 4: Install MCP Server

Clone or navigate to the project:

```bash
cd /Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server
```

Install dependencies:

```bash
# Using pip
pip install -e .

# Or using uv (faster)
uv pip install -e .
```

### Step 5: Configure Environment

Copy example environment file:

```bash
cp .env.example .env
```

Edit `.env` with your credentials:

```bash
# WordPress Site Configuration
WP_SITE_URL=https://sst.nyc
WP_SSH_HOST=your-server.hostinger.com
WP_SSH_USER=your-ssh-username
WP_SSH_KEY_PATH=/Users/shawnshirazi/.ssh/id_ed25519
WP_REMOTE_PATH=/home/your-username/public_html

# WordPress REST API Authentication
WP_API_USER=your-admin-username
WP_API_PASSWORD=xxxx xxxx xxxx xxxx xxxx xxxx  # Application Password from Step 2
```

**Important Notes:**
- `WP_REMOTE_PATH` is where WordPress is installed on your server (ask Hostinger support if unsure)
- Application Password has spaces - keep them in the .env file
- Use absolute path for SSH key

### Step 6: Test Connection

Test wp-cli connection:

```bash
python -c "
from dotenv import load_dotenv
load_dotenv()
from src.config import WordPressConfig
from src.wp_cli import WPCLIClient

config = WordPressConfig.from_env()
client = WPCLIClient(config)
print(client.get_info())
"
```

Expected output:
```
{'wordpress_version': '6.4.2', 'site_url': 'https://sst.nyc'}
```

### Step 7: Configure Claude Desktop

Add to your Claude Desktop configuration (`~/Library/Application Support/Claude/claude_desktop_config.json` on macOS):

```json
{
  "mcpServers": {
    "wordpress-seo-admin": {
      "command": "python",
      "args": [
        "/Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server/src/server.py"
      ],
      "env": {
        "WP_SITE_URL": "https://sst.nyc",
        "WP_SSH_HOST": "your-server.hostinger.com",
        "WP_SSH_USER": "your-ssh-username",
        "WP_SSH_KEY_PATH": "/Users/shawnshirazi/.ssh/id_ed25519",
        "WP_REMOTE_PATH": "/home/your-username/public_html",
        "WP_API_USER": "your-admin-username",
        "WP_API_PASSWORD": "your-application-password"
      }
    }
  }
}
```

Or use `uv` for isolated environment:

```json
{
  "mcpServers": {
    "wordpress-seo-admin": {
      "command": "uv",
      "args": [
        "--directory",
        "/Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server",
        "run",
        "python",
        "src/server.py"
      ]
    }
  }
}
```

### Step 8: Restart Claude Desktop

Restart Claude Desktop to load the MCP server.

## Verification

After restart, you should see the WordPress tools available in Claude:

- `wp_get_info` - Site information
- `wp_plugin_list` - List plugins
- `seo_analyze_post` - SEO analysis
- And more...

## Troubleshooting

### "Permission denied (publickey)"
- Check SSH key path in `.env`
- Verify key is added to server: `ssh-copy-id`
- Test SSH manually: `ssh user@host`

### "wp: command not found"
- wp-cli not installed on server
- Check `WP_REMOTE_PATH` is correct
- Install wp-cli (see Step 1)

### "401 Unauthorized" from REST API
- Application Password incorrect
- Check username matches WordPress admin
- Regenerate Application Password

### Connection timeout
- Check firewall settings
- Verify server allows SSH from your IP
- Contact Hostinger support

## Security Best Practices

1. **Never commit .env file** - Already in `.gitignore`
2. **Use SSH keys** - More secure than passwords
3. **Rotate Application Passwords** - Regenerate periodically
4. **Limit permissions** - Use dedicated WordPress user for API access
5. **Monitor access** - Check WordPress logs regularly

## Next Steps

Once configured, you can use Claude to:

- Analyze SEO across your entire site
- Monitor plugin updates
- Search and audit content
- Extract Elementor page data
- Generate SEO reports

See `README.md` for available commands and examples.
