# SST.NYC Server Path Reference

**CRITICAL: ALWAYS TEST ON STAGING FIRST, NEVER ON PRODUCTION**

## SSH Connection
- **Host:** 147.93.88.8
- **Port:** 65002
- **User:** u629344933
- **Password:** RvALk23Zgdyw4Zn

## Directory Structure

### Production Site
- **URL:** https://sst.nyc
- **Path:** `/home/u629344933/domains/sst.nyc/public_html/`
- **WordPress Root:** `/home/u629344933/domains/sst.nyc/public_html/`
- **Plugins:** `/home/u629344933/domains/sst.nyc/public_html/wp-content/plugins/`
- **Themes:** `/home/u629344933/domains/sst.nyc/public_html/wp-content/themes/`
- **Uploads:** `/home/u629344933/domains/sst.nyc/public_html/wp-content/uploads/`

### Staging Site
- **URL:** https://staging.sst.nyc (accessed via subdirectory)
- **Path:** `/home/u629344933/domains/sst.nyc/public_html/staging/`
- **WordPress Root:** `/home/u629344933/domains/sst.nyc/public_html/staging/`
- **Plugins:** `/home/u629344933/domains/sst.nyc/public_html/staging/wp-content/plugins/`
- **Themes:** `/home/u629344933/domains/sst.nyc/public_html/staging/wp-content/themes/`
- **Uploads:** `/home/u629344933/domains/sst.nyc/public_html/staging/wp-content/uploads/`

## Testing Protocol
1. **ALWAYS** upload and test on staging first
2. **NEVER** test directly on production
3. Once verified on staging, then deploy to production
4. Keep test data on staging, not production

## Database
- Production DB Prefix: `zush_`
- Staging DB Prefix: `zush_` (shared database with different site URL)

## Common Commands

### Staging
```bash
# Navigate to staging
cd /home/u629344933/domains/sst.nyc/public_html/staging

# WP-CLI commands (add --allow-root)
wp plugin list --allow-root
wp db query "SELECT * FROM zush_sst_affiliates LIMIT 5;" --allow-root
```

### Production
```bash
# Navigate to production
cd /home/u629344933/domains/sst.nyc/public_html

# WP-CLI commands (add --allow-root)
wp plugin list --allow-root
```

## SCP Upload Examples
```bash
# Upload to staging
sshpass -p 'RvALk23Zgdyw4Zn' scp -P 65002 -r plugin-folder u629344933@147.93.88.8:/home/u629344933/domains/sst.nyc/public_html/staging/wp-content/plugins/

# Upload to production (only after staging verification)
sshpass -p 'RvALk23Zgdyw4Zn' scp -P 65002 -r plugin-folder u629344933@147.93.88.8:/home/u629344933/domains/sst.nyc/public_html/wp-content/plugins/
```
