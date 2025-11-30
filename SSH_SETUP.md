# SSH Setup Guide for Hostinger

To connect the WordPress MCP server to your Hostinger server, you need to set up SSH authentication.

## Option 1: SSH Key Authentication (Recommended)

SSH keys are more secure and reliable than passwords.

### Step 1: Generate SSH Key (if you don't have one)

You already have an SSH key at: `/Users/shawnshirazi/.ssh/id_ed25519`

To view your **public** key:
```bash
cat ~/.ssh/id_ed25519.pub
```

This will show something like:
```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAA... your-email@example.com
```

### Step 2: Add Public Key to Hostinger

1. Log into your Hostinger control panel (hpanel)
2. Go to **Files** → **SSH Access**
3. Click **Manage SSH Keys**
4. Click **Add New SSH Key**
5. Paste the **entire** public key from `~/.ssh/id_ed25519.pub`
6. Give it a name like "MacBook Pro"
7. Click **Add Key**
8. **Important:** Make sure to **activate** the key (toggle switch next to it)

### Step 3: Test the Connection

```bash
ssh -p 65002 -i ~/.ssh/id_ed25519 u629344933@147.93.88.8
```

If it works without asking for a password, you're all set!

### Step 4: Update .env

```bash
# In .env file
WP_SSH_KEY_PATH=/Users/shawnshirazi/.ssh/id_ed25519
# WP_SSH_PASSWORD=RvALk23Zgdyw4Zn  # Comment out password
```

---

## Option 2: Password Authentication

If SSH keys don't work or you prefer password authentication:

### Current Issue

The password authentication is failing with Paramiko (Python SSH library). This could be due to:
- Password might contain special characters that need escaping
- Hostinger might require keyboard-interactive authentication instead of password
- Password might have changed

### To Use Password in Claude Desktop Config

Instead of using the `.env` file, add credentials directly to Claude Desktop config:

Edit `~/Library/Application Support/Claude/claude_desktop_config.json`:

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
        "WP_API_PASSWORD": "your-wordpress-app-password"
      }
    }
  }
}
```

---

## Troubleshooting

### "Permission denied (publickey,password)"

This means:
1. Your SSH key is not authorized on the server, OR
2. The password is incorrect

**Solution:** Follow Option 1 above to add your public key to Hostinger.

### "Connection refused"

Check:
- Port number is correct (65002)
- Server IP is correct (147.93.88.8)
- Firewall isn't blocking the connection

### "Host key verification failed"

Run:
```bash
ssh-keyscan -p 65002 147.93.88.8 >> ~/.ssh/known_hosts
```

### Test SSH Connection Manually

Always test SSH manually first before using the MCP server:

```bash
# With SSH key:
ssh -p 65002 -i ~/.ssh/id_ed25519 u629344933@147.93.88.8 'echo "Connection works!"'

# With password (requires sshpass):
sshpass -p 'RvALk23Zgdyw4Zn' ssh -p 65002 u629344933@147.93.88.8 'echo "Connection works!"'
```

---

## Recommended Setup

1. ✅ Add your SSH public key to Hostinger (most secure and reliable)
2. ✅ Test connection manually
3. ✅ Update `.env` with `WP_SSH_KEY_PATH`
4. ✅ Comment out `WP_SSH_PASSWORD`
5. ✅ Add Claude Desktop config
6. ✅ Restart Claude Desktop

This will give you the most reliable connection!

---

## Your Hostinger Connection Details

```
Host: 147.93.88.8
Port: 65002
User: u629344933
Path: /home/u629344933/domains/staging.sst.nyc/public_html
```

SSH command:
```bash
ssh -p 65002 u629344933@147.93.88.8
```
