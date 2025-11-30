# WordPress MCP Server - Feature Overview

## What This MCP Server Enables

This server gives Claude Code direct access to your WordPress site for SEO optimization and site administration.

## Core Capabilities

### 1. Site Management & Monitoring

**wp_get_info**
- WordPress version
- Active theme
- Plugin count
- Site URL

**wp_plugin_list**
- List all plugins with status (active/inactive)
- Filter by status
- Check versions and updates

**wp_theme_list**
- All installed themes
- Active theme identification
- Version information

**wp_check_updates**
- Core WordPress updates
- Plugin updates available
- Theme updates available

### 2. Content Operations

**wp_post_list**
- Query posts, pages, custom post types
- Filter by status (publish, draft, etc.)
- Limit results
- Get title, ID, date, status

**wp_get_post**
- Full post content with HTML
- All metadata
- Featured image
- Author info
- Categories and tags

**wp_search**
- Full-text search across content
- Search posts, pages, or any post type
- Returns matching posts with context

### 3. Image Optimization

**image_analyze**
Analyze WordPress media library images:
- Current format (JPEG, PNG, WebP)
- File size and dimensions
- Alt text validation
- Transparency detection
- WebP conversion potential (estimated savings)
- Actionable recommendations

**image_optimize**
Download and optimize any image:
- Convert to WebP (or JPEG/PNG)
- Smart compression with quality control
- Automatic resizing to max dimensions
- Preserve transparency when needed
- Returns optimization statistics (savings %, size reduction)

**image_audit_site**
Bulk analyze all site images:
- Scan up to specified limit
- Generate site-wide summary
- Identify missing alt text
- Find large files (>500KB)
- Calculate total potential savings
- Per-image detailed analysis

### 4. SEO Analysis

**seo_analyze_post**
Comprehensive SEO audit including:
- Title length validation (30-60 chars recommended)
- Meta description analysis (120-160 chars)
- Word count check (300+ words recommended)
- Heading structure (H1, H2, H3)
- Image alt tag validation
- Internal/external link analysis
- Yoast/RankMath integration (if installed)

Returns actionable recommendations:
- "Title is too short (< 30 characters)"
- "3 image(s) missing alt text"
- "Add more internal links (< 2 found)"

**elementor_extract_content**
Parse Elementor page builder JSON to extract:
- All text content (clean, no HTML)
- Headings with hierarchy
- Images with alt tags
- Widget structure

Essential for SEO analysis of Elementor pages since content is stored as JSON.

## Example Use Cases

### Use Case 1: Image Optimization Workflow
```
1. Run image_audit_site to scan all images
2. Identify images with optimization potential
3. For each large image, use image_optimize to convert to WebP
4. Review alt text issues and update via WordPress
5. Monitor total bandwidth savings
```

### Use Case 2: Site-Wide SEO Audit
```
1. Use wp_post_list to get all published pages
2. For each page, run seo_analyze_post
3. Aggregate recommendations
4. Generate prioritized action list
```

### Use Case 2: Content Optimization
```
1. Search for underperforming pages (wp_search)
2. Analyze SEO metrics (seo_analyze_post)
3. Identify missing alt tags, thin content, poor titles
4. Provide specific fixes
```

### Use Case 3: Plugin Maintenance
```
1. Check available updates (wp_check_updates)
2. Review changelog for breaking changes
3. Test updates on staging first
4. Deploy to production
```

### Use Case 4: Elementor Page Analysis
```
1. Identify Elementor pages (check post meta)
2. Extract content (elementor_extract_content)
3. Analyze for SEO (headings, keywords, readability)
4. Suggest optimizations
```

## Why This Architecture?

### Maintainability
- **wp-cli** is maintained by WordPress core team
- API changes handled upstream
- Plugin compatibility managed by wp-cli
- Less code to maintain = fewer bugs

### Security
- SSH key authentication
- No password storage
- WordPress Application Passwords (revokable)
- Read-only operations by default

### Performance
- Direct server access via SSH
- REST API for real-time data
- No WordPress admin overhead
- Caching possible at MCP level

### Flexibility
- Works with any WordPress host (Hostinger, WP Engine, etc.)
- Supports custom post types
- Extensible with new tools
- Multi-site ready (with configuration)

## Future Enhancements

### Planned Features
- **Image upload**: Upload optimized images back to WordPress Media Library
- **Batch image replacement**: Replace originals with optimized versions
- **Schema.org validation**: Check structured data markup
- **Broken link checker**: Scan internal/external links
- **Performance metrics**: Core Web Vitals via Lighthouse API
- **Content suggestions**: AI-powered SEO improvements
- **Bulk operations**: Update multiple posts at once
- **LearnDash integration**: Course SEO optimization
- **WooCommerce SEO**: Product optimization tools

### Integration Opportunities
- **Google Search Console API**: Fetch real performance data
- **Ahrefs/SEMrush APIs**: Keyword research integration
- **PageSpeed Insights**: Automated performance testing
- **Sitemap analyzer**: XML sitemap validation and optimization
- **Robots.txt checker**: Crawlability validation

## Limitations & Constraints

### Current Limitations
1. **Read-mostly operations**: Write operations require additional safety checks
2. **No file uploads**: Can't upload images directly (yet)
3. **Single site**: Multi-site needs configuration updates
4. **Network dependency**: Requires stable SSH/HTTPS connection
5. **wp-cli required**: Server must have wp-cli installed

### Performance Notes
- SSH commands have ~100-500ms latency
- REST API calls are faster (~50-200ms)
- Large content extractions may be slow
- Elementor parsing is CPU-intensive for complex pages

### Security Considerations
- Credentials stored in .env (never commit!)
- Application Passwords preferred over user passwords
- SSH keys recommended over password auth
- Audit logs not implemented (use WordPress plugins)

## Compared to Alternatives

### vs. Direct Database Access
✅ Safer (uses WordPress APIs)
✅ Respects WordPress data structure
✅ Plugin-aware operations
❌ Slower than raw SQL

### vs. WordPress Admin UI
✅ Programmable and automatable
✅ Bulk operations possible
✅ No UI navigation needed
❌ Requires technical setup

### vs. WordPress Plugins (Yoast, etc.)
✅ Custom analysis possible
✅ Cross-site operations
✅ AI-powered recommendations
❌ More setup complexity

## Getting Started

1. Read `SETUP.md` for installation
2. Configure `.env` with your credentials
3. Test with `wp_get_info` tool
4. Explore with `wp_plugin_list` and `wp_post_list`
5. Run SEO analysis on a test page
6. Build custom workflows!

## Support

For issues:
1. Check `SETUP.md` troubleshooting section
2. Verify SSH and API credentials
3. Test wp-cli manually on server
4. Check WordPress REST API is enabled
