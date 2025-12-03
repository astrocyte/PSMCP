#!/bin/bash

#############################################
# Predictive Safety Affiliate Program
# Production Rollback Script
#
# Use this if deployment goes wrong
#############################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

SSH_HOST="147.93.88.8"
SSH_PORT="65002"
SSH_USER="u629344933"
PRODUCTION_PATH="/home/u629344933/domains/sst.nyc/public_html"

echo -e "${RED}========================================${NC}"
echo -e "${RED}PRODUCTION ROLLBACK${NC}"
echo -e "${RED}========================================${NC}"
echo ""

read -p "Are you sure you want to rollback? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "Rollback cancelled."
    exit 0
fi

echo ""
echo -e "${YELLOW}Rolling back changes...${NC}"
echo ""

# Deactivate and remove plugin
echo "1. Removing custom plugin..."
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp plugin deactivate sst-affiliate-zapier-webhook || true"
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp plugin delete sst-affiliate-zapier-webhook || true"
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "rm -f $PRODUCTION_PATH/wp-content/plugins/sst-affiliate-zapier-webhook.php"
echo -e "${GREEN}✓ Plugin removed${NC}"

# Delete webhook option
echo "2. Removing webhook configuration..."
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp option delete sst_zapier_webhook_url || true"
echo -e "${GREEN}✓ Config removed${NC}"

# Delete the page (optional - you may want to keep it)
echo "3. Listing affiliate pages for manual deletion..."
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post list --post_type=page --name=yourmoney --format=table"

echo ""
echo -e "${YELLOW}To delete the page, run:${NC}"
echo "  ssh -p $SSH_PORT $SSH_USER@$SSH_HOST 'cd $PRODUCTION_PATH && wp post delete <PAGE_ID> --force'"

# Delete the form (optional)
echo ""
echo "4. Listing affiliate forms for manual deletion..."
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "cd $PRODUCTION_PATH && wp post list --post_type=wpforms --s='Predictive Safety Affiliate' --format=table"

echo ""
echo -e "${YELLOW}To delete the form, run:${NC}"
echo "  ssh -p $SSH_PORT $SSH_USER@$SSH_HOST 'cd $PRODUCTION_PATH && wp post delete <FORM_ID> --force'"

echo ""
echo -e "${GREEN}Rollback complete!${NC}"
echo ""
