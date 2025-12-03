#!/bin/bash

#############################################
# Predictive Safety Affiliate Program
# Production Deployment Script
#
# This script deploys the affiliate program from staging to production
#############################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
SSH_HOST="147.93.88.8"
SSH_PORT="65002"
SSH_USER="u629344933"
STAGING_PATH="/home/u629344933/domains/sst.nyc/public_html/staging"
PRODUCTION_PATH="/home/u629344933/domains/sst.nyc/public_html"
ZAPIER_WEBHOOK_URL="https://hooks.zapier.com/hooks/catch/25126567/uklhp7y/"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Predictive Safety Affiliate Deployment${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

#############################################
# Step 1: Backup Production
#############################################
echo -e "${YELLOW}Step 1: Creating production backup...${NC}"

ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp db export /tmp/production_backup_$(date +%Y%m%d_%H%M%S).sql"

echo -e "${GREEN}✓ Backup created${NC}"
echo ""

#############################################
# Step 2: Deploy Custom Plugin
#############################################
echo -e "${YELLOW}Step 2: Deploying custom webhook plugin...${NC}"

# Copy plugin from staging to production
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cp $STAGING_PATH/wp-content/plugins/sst-affiliate-zapier-webhook.php $PRODUCTION_PATH/wp-content/plugins/"

# Activate plugin
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp plugin activate sst-affiliate-zapier-webhook"

# Configure webhook URL
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp option update sst_zapier_webhook_url '$ZAPIER_WEBHOOK_URL'"

echo -e "${GREEN}✓ Plugin deployed and configured${NC}"
echo ""

#############################################
# Step 3: Export WPForms from Staging
#############################################
echo -e "${YELLOW}Step 3: Exporting affiliate form from staging...${NC}"

# Get form data from staging
FORM_DATA=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $STAGING_PATH && wp post get 5066 --field=post_content")

echo -e "${GREEN}✓ Form exported${NC}"
echo ""

#############################################
# Step 4: Import Form to Production
#############################################
echo -e "${YELLOW}Step 4: Creating affiliate form on production...${NC}"

# Check if form already exists on production
EXISTING_FORM=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post list --post_type=wpforms --post_title='Predictive Safety Affiliate Program' --format=ids" || echo "")

if [ -z "$EXISTING_FORM" ]; then
    # Create new form
    ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post create --post_type=wpforms --post_title='Predictive Safety Affiliate Program' --post_status=publish --post_content='$FORM_DATA' --porcelain" > /tmp/prod_form_id.txt
    PROD_FORM_ID=$(cat /tmp/prod_form_id.txt)
    echo -e "${GREEN}✓ New form created (ID: $PROD_FORM_ID)${NC}"
else
    # Update existing form
    ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post update $EXISTING_FORM --post_content='$FORM_DATA'"
    PROD_FORM_ID=$EXISTING_FORM
    echo -e "${GREEN}✓ Existing form updated (ID: $PROD_FORM_ID)${NC}"
fi

echo ""

#############################################
# Step 5: Create/Update Affiliate Page
#############################################
echo -e "${YELLOW}Step 5: Deploying /somos/ page...${NC}"

# Get page content from staging
PAGE_CONTENT=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $STAGING_PATH && wp post get 5082 --field=post_content")

# Update shortcode to use production form ID
PAGE_CONTENT_UPDATED=$(echo "$PAGE_CONTENT" | sed "s/wpforms id=\"5066\"/wpforms id=\"$PROD_FORM_ID\"/g")

# Check if page exists
EXISTING_PAGE=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post list --post_type=page --name=somos --format=ids" || echo "")

if [ -z "$EXISTING_PAGE" ]; then
    # Create new page
    ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post create --post_type=page --post_title='Somos - Grow Together' --post_name=somos --post_status=publish --post_content='$PAGE_CONTENT_UPDATED'"
    echo -e "${GREEN}✓ New page created at /somos/${NC}"
else
    # Update existing page
    ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post update $EXISTING_PAGE --post_content='$PAGE_CONTENT_UPDATED'"
    echo -e "${GREEN}✓ Existing page updated${NC}"
fi

echo ""

#############################################
# Step 6: Flush Cache
#############################################
echo -e "${YELLOW}Step 6: Flushing cache...${NC}"

ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp cache flush"

echo -e "${GREEN}✓ Cache flushed${NC}"
echo ""

#############################################
# Step 7: Verification
#############################################
echo -e "${YELLOW}Step 7: Running verification checks...${NC}"

# Check plugin status
PLUGIN_STATUS=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp plugin list --name=sst-affiliate-zapier-webhook --field=status")
echo "  Plugin status: $PLUGIN_STATUS"

# Check webhook URL
WEBHOOK_URL=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp option get sst_zapier_webhook_url")
echo "  Webhook URL: $WEBHOOK_URL"

# Check page exists
PAGE_URL=$(ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post list --post_type=page --name=somos --field=url")
echo "  Page URL: $PAGE_URL"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Visit: https://sst.nyc/somos/"
echo "2. Test form submission"
echo "3. Check Zapier receives webhook"
echo "4. Verify Google Sheets updates"
echo "5. Test email notifications"
echo ""
echo -e "${YELLOW}Important URLs:${NC}"
echo "  • Affiliate Page: https://sst.nyc/somos/"
echo "  • Form Editor: https://sst.nyc/wp-admin/admin.php?page=wpforms-builder&view=fields&form_id=$PROD_FORM_ID"
echo "  • Plugin Settings: https://sst.nyc/wp-admin/options-general.php?page=sst-affiliate-webhook"
echo ""
echo -e "${RED}WARNING: This script modified production!${NC}"
echo -e "${RED}Database backup saved to /tmp/ on server${NC}"
echo ""
