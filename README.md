# WordPress MCP Server

A Model Context Protocol (MCP) server for managing WordPress sites, with specialized tools for SEO optimization and site administration.

## Features

- **wp-cli Integration**: Execute WordPress CLI commands remotely via SSH
- **REST API Access**: Query WordPress data in real-time
- **SEO Tools**: Analyze pages, audit metadata, validate schema
- **Elementor Support**: Parse and analyze Elementor page builder content
- **Multi-Environment**: Support for production, staging, and local environments

## Prerequisites

- Python 3.10+
- SSH access to WordPress server with wp-cli installed
- WordPress Application Password for REST API access

## Installation

```bash
# Install dependencies
pip install -e .

# Copy environment configuration
cp .env.example .env

# Edit .env with your WordPress credentials
```

## Configuration

Edit `.env` with your WordPress site details:

- `WP_SITE_URL`: Your WordPress site URL
- `WP_SSH_HOST`: SSH hostname (from Hostinger)
- `WP_SSH_USER`: SSH username
- `WP_SSH_KEY_PATH`: Path to SSH private key
- `WP_API_USER`: WordPress admin username
- `WP_API_PASSWORD`: Application Password (generated in WordPress)

### Generate WordPress Application Password

1. Log into WordPress admin
2. Go to Users → Profile
3. Scroll to "Application Passwords"
4. Create new password with name "MCP Server"
5. Copy the generated password to `.env`

## Usage

Run the MCP server:

```bash
python src/server.py
```

The server will be available via stdio for MCP clients (Claude Desktop, etc.)

## System Status

✅ **All modules tested and working**
✅ **33 MCP tools ready**
✅ **SSH support with custom port (65002)**
✅ **Password & SSH key authentication**
✅ **Complete documentation (12 guides)**

**See DEPLOYMENT_GUIDE.md to get started!**

---

## Available Tools (33 Total!)

### Site Management (3 tools)
- `wp_get_info`: Get WordPress version, theme, plugin info
- `wp_plugin_list`: List all installed plugins
- `wp_theme_list`: List available themes

### Content Operations (3 tools)
- `wp_post_list`: Query posts/pages with filters
- `wp_get_post`: Get full post content and metadata
- `wp_search`: Search across site content

### SEO Analysis (2 tools)
- `seo_analyze_post`: Complete SEO analysis of a page
- `elementor_extract_content`: Parse Elementor JSON for SEO

### Image Optimization (3 tools)
- `image_analyze`: Analyze a single WordPress media image for optimization
- `image_optimize`: Convert to WebP, compress, and resize images
- `image_audit_site`: Audit all site images for SEO and performance issues

### LearnDash LMS (9 tools)
- `ld_create_course`: Create new courses
- `ld_update_course`: Update existing courses
- `ld_list_courses`: List all courses
- `ld_create_lesson`: Add lessons to courses
- `ld_update_lesson`: Update lesson content/order
- `ld_create_quiz`: Create quizzes
- `ld_add_quiz_question`: Add quiz questions
- `ld_enroll_user`: Enroll students in courses
- `ld_create_group`: Create student groups

### WooCommerce (6 tools)
- `wc_create_product`: Create products
- `wc_update_product`: Update product details/pricing
- `wc_list_products`: List all products
- `wc_list_orders`: View orders and sales
- `wc_create_coupon`: Create discount coupons
- `wc_get_sales_report`: Get sales analytics

### Mailchimp Email Marketing (6 tools)
- `mc_list_audiences`: List all email lists
- `mc_add_subscriber`: Add/update subscribers
- `mc_create_campaign`: Create email campaigns
- `mc_send_campaign`: Send campaigns
- `mc_get_campaign_report`: Campaign analytics
- `mc_tag_course_student`: Auto-tag course enrollments

### Monitoring (1 tool)
- `wp_check_updates`: Check for available updates

## Development

```bash
# Install dev dependencies
pip install -e ".[dev]"

# Run tests
pytest

# Format code
black src/

# Lint
ruff check src/
```

## Architecture

This MCP server uses a hybrid approach:

1. **wp-cli via SSH**: For reliable WordPress operations maintained by core team
2. **REST API**: For real-time data queries and content access
3. **Custom SEO tools**: Built on top of wp-cli and REST API data

This keeps maintenance low while providing powerful WordPress management capabilities.
