# Zapier MCP Server Setup Guide

## What is Zapier MCP?

The Zapier MCP (Model Context Protocol) server gives Claude Code direct access to 8,000+ apps and 30,000+ actions through your Zapier account. This means Claude can create Zaps, trigger workflows, and manage integrations programmatically.

## Setup Instructions

### Step 1: Get Your Zapier MCP Server URL

1. Visit https://zapier.com/mcp
2. Log in to your Zapier account
3. Copy your unique MCP server URL (looks like: `https://mcp.zapier.com/api/mcp/a/YOUR_ID/mcp`)

**Important:** This URL is like a password - it can trigger your Zapier actions. Keep it private.

### Step 2: Configure Claude Desktop

Add the Zapier MCP server to your Claude Desktop configuration:

**Location:** `~/Library/Application Support/Claude/claude_desktop_config.json`

**Configuration:**
```json
{
  "mcpServers": {
    "zapier": {
      "url": "https://mcp.zapier.com/api/mcp/a/YOUR_ID/mcp"
    }
  }
}
```

If you already have other MCP servers, just add the `"zapier"` entry to your existing `mcpServers` object.

### Step 3: Restart Claude Desktop

Close and reopen Claude Desktop (or Claude Code) to load the new MCP server.

### Step 4: Verify Installation

Run this command to check if Zapier MCP is available:
```bash
claude mcp list
```

You should see `zapier` in the list of configured servers.

## What Can You Do With Zapier MCP?

Once configured, Claude can:

### Create Zaps (Workflows)
- Set up triggers (e.g., "When WPForms submission received")
- Configure actions (e.g., "Add row to Google Sheets", "Send Gmail")
- Map data fields between apps

### Manage Existing Zaps
- List all your Zaps
- Enable/disable Zaps
- Update Zap configurations
- Check Zap history and errors

### Trigger Actions Directly
- Send emails via Gmail
- Create/update Google Sheets rows
- Post to Slack
- Create calendar events
- And 8,000+ other app integrations

## Usage Examples

### Example 1: Create a Google Sheet
```
Claude, use Zapier to create a new Google Sheet called "SST Affiliate Signups" with these columns: Timestamp, Affiliate ID, First Name, Last Name, Email, Phone
```

### Example 2: Set Up a Workflow
```
Claude, create a Zap that triggers when there's a new WPForms submission (Form ID: 5066), then adds a row to the Google Sheet "SST Affiliate Signups", and sends a confirmation email via Gmail.
```

### Example 3: Check Zap Status
```
Claude, list all my active Zaps and show me their task usage this month
```

## Cost Considerations

**Zapier Task Usage:**
- Each MCP tool call uses **2 tasks** from your Zapier plan quota
- Example: Creating a Zap = 2 tasks, Triggering an action = 2 tasks

**Zapier Plans:**
- Free: 100 tasks/month
- Starter: $29.99/mo - 750 tasks/month
- Professional: $73.50/mo - 2,000 tasks/month

**Recommendation:** Start with the free tier for testing, upgrade to Starter if you're automating regularly.

## For This Project (SST.NYC Affiliate Program)

The Zapier MCP is configured to help automate:

1. **Google Sheet Creation** - Automatic affiliate signup tracking
2. **Form → Sheet Integration** - WPForms submissions → Google Sheets
3. **Email Automation** - Confirmation emails to applicants and admin
4. **Approval Workflows** - QR code generation when status changes to "Approved"

## Troubleshooting

### "MCP server not found"
- Verify the config file exists at `~/Library/Application Support/Claude/claude_desktop_config.json`
- Check that the JSON is valid (no syntax errors)
- Restart Claude Desktop

### "Authentication error"
- Your MCP URL may have expired - generate a new one at https://zapier.com/mcp
- Update the URL in your config file

### "Tool call failed"
- Check your Zapier task quota at https://zapier.com/app/history
- Verify the Zapier action is supported (some premium actions require paid Zapier plans)

## Security Best Practices

1. **Never commit the config file** to git (it contains your private MCP URL)
2. **Rotate the URL periodically** - regenerate a new one every few months
3. **Monitor Zapier history** - Check https://zapier.com/app/history for unexpected activity
4. **Use environment-specific URLs** - Different MCP URLs for dev/staging/production if needed

## Alternative: Traditional Zapier Setup

If you prefer not to use the MCP server, you can still set up Zapier workflows manually:

1. Go to https://zapier.com
2. Click "Create Zap"
3. Follow the visual workflow builder
4. See `ZAPIER_SETUP_INSTRUCTIONS.md` for step-by-step guide

The MCP approach is faster for bulk operations and automation, while the web UI is better for visual workflow design and ongoing management.

## Documentation & Support

- **Zapier MCP Docs:** https://help.zapier.com/hc/en-us/articles/36265392843917-Use-Zapier-MCP-with-your-client
- **GitHub Repository:** https://github.com/zapier/zapier-mcp
- **Zapier Support:** https://zapier.com/help
- **Claude Code MCP Docs:** https://docs.claude.com/en/docs/claude-code/mcp

---

**Last Updated:** December 2, 2025
**Configured For:** SST.NYC Affiliate Program Automation
**MCP Server Version:** Latest (auto-updated by Zapier)
