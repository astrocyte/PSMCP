# Image Optimization Guide

Complete guide to optimizing WordPress images using the MCP server.

## Why Optimize Images?

### SEO Benefits
- **Page Speed**: Faster loading = better rankings
- **Core Web Vitals**: Optimized images improve LCP (Largest Contentful Paint)
- **Mobile Performance**: Smaller files = better mobile experience
- **Accessibility**: Proper alt text helps screen readers and SEO

### Performance Impact
- **WebP format**: ~30% smaller than JPEG/PNG with same quality
- **Compression**: Can reduce file size by 50-70% without visible quality loss
- **Bandwidth**: Less data transfer = lower hosting costs

## Available Tools

### 1. image_analyze
Analyze a single image from WordPress Media Library.

**Parameters:**
- `media_id`: WordPress attachment ID

**Returns:**
- Current format, dimensions, file size
- Alt text status
- Estimated WebP savings
- Optimization recommendations

**Example Use:**
```
Analyze media ID 123 to see if it needs optimization
```

### 2. image_optimize
Download and optimize any image URL.

**Parameters:**
- `url`: Image URL (required)
- `format`: Target format - auto, webp, jpeg, png (default: auto)
- `quality`: Quality 1-100 (default: 85)
- `max_width`: Maximum width in pixels (default: 2048)
- `max_height`: Maximum height in pixels (default: 2048)

**Returns:**
- Original and optimized file sizes
- Savings in KB and percentage
- Output format and dimensions
- Note: Optimized data is ready (not displayed in text)

**Example Use:**
```
Optimize image at https://sst.nyc/wp-content/uploads/2024/01/hero.jpg
Convert to WebP with quality 85
```

### 3. image_audit_site
Scan entire site for image optimization opportunities.

**Parameters:**
- `limit`: Number of images to analyze (default: 50)

**Returns:**
- Total images analyzed
- Count of missing alt text
- Large files (>500KB)
- Total potential WebP savings
- Detailed per-image analysis

**Example Use:**
```
Audit all images on the site to find optimization opportunities
```

## Common Workflows

### Workflow 1: Quick Site Audit

**Goal:** Find biggest optimization wins

**Steps:**
1. Run `image_audit_site` with limit=100
2. Sort results by file size or potential savings
3. Identify top 10 images for optimization
4. Use `image_optimize` on each
5. Upload optimized versions back to WordPress

**Expected Results:**
- Identify images wasting bandwidth
- Calculate total potential savings
- Prioritize optimization efforts

### Workflow 2: New Content Optimization

**Goal:** Optimize images before publishing

**Steps:**
1. Upload images to WordPress
2. Use `image_analyze` on each media ID
3. Review recommendations
4. For large images, use `image_optimize`
5. Update alt text if missing
6. Publish content

**Expected Results:**
- All new content is optimized from day one
- No missing alt text
- Smaller file sizes

### Workflow 3: Alt Text Audit

**Goal:** Find and fix missing alt text

**Steps:**
1. Run `image_audit_site` with high limit
2. Filter results for `has_alt_text: false`
3. Create list of media IDs needing alt text
4. Update alt text via WordPress admin or API
5. Re-run audit to verify

**Expected Results:**
- 100% alt text coverage
- Better accessibility
- Improved SEO

### Workflow 4: Legacy Content Cleanup

**Goal:** Optimize old images

**Steps:**
1. Use `wp_post_list` to get old posts (e.g., from 2022)
2. Extract image URLs from content
3. Run `image_optimize` on each
4. Replace old images with optimized versions
5. Monitor bandwidth savings

**Expected Results:**
- Reduced page load times
- Better Core Web Vitals scores
- Lower hosting bandwidth

## Best Practices

### Format Selection

**Use WebP when:**
- Modern browser support is acceptable (95%+ browsers)
- File size reduction is priority
- Images have transparency (WebP supports alpha)

**Use JPEG when:**
- Legacy browser support needed
- Photos without transparency
- Maximum compatibility required

**Use PNG when:**
- Need lossless compression
- Graphics with sharp edges (logos, screenshots)
- Transparency with older browser support

### Quality Settings

**Quality 85 (Default):**
- Best balance of size vs quality
- Virtually indistinguishable from original
- Recommended for most use cases

**Quality 90-95:**
- High quality needed (portfolio, photography)
- Slightly larger files
- Still good compression

**Quality 70-80:**
- Maximum compression
- Small file size priority
- Acceptable for thumbnails, backgrounds

### Dimension Guidelines

**Max 2048px (Default):**
- Good for full-screen hero images
- Retina display support
- Balance of quality and size

**Max 1920px:**
- Standard HD displays
- Slightly smaller files
- Good for content images

**Max 1200px:**
- Content area images
- Faster loading
- Sufficient for most blog posts

## SEO Recommendations

### Alt Text Best Practices
- **Descriptive**: Describe what's in the image
- **Concise**: 10-15 words ideal
- **Context**: How it relates to content
- **Keywords**: Natural inclusion, no stuffing
- **Avoid**: "Image of", "Picture of" prefixes

**Good Example:**
```
"Students collaborating on laptops in modern classroom"
```

**Bad Example:**
```
"image"
"IMG_1234.jpg"
""
```

### File Naming
- Use descriptive names before upload
- Hyphens instead of underscores
- Include relevant keywords
- Lowercase only

**Good:** `modern-classroom-collaboration.jpg`
**Bad:** `IMG_1234.JPG`

### Image Sizing Strategy
1. **Hero Images**: Max 2048px width, WebP, quality 85
2. **Content Images**: Max 1200px width, WebP, quality 85
3. **Thumbnails**: Max 400px width, WebP, quality 80
4. **Icons**: SVG format (vector) when possible

## Monitoring and Maintenance

### Regular Audits
- **Monthly**: Run `image_audit_site` to check new uploads
- **Quarterly**: Full site scan with high limit
- **After imports**: Audit whenever bulk importing images

### Performance Tracking
Monitor these metrics before/after optimization:
- **Page Load Time**: Google PageSpeed Insights
- **LCP**: Largest Contentful Paint
- **Total Page Size**: Browser DevTools
- **Image Requests**: Number and total KB

### Automation Opportunities
Consider automating:
- New upload optimization
- Monthly audit reports
- Alt text reminders
- Large file warnings

## Troubleshooting

### "Image too large" errors
- Reduce max_width/max_height
- Use quality < 85
- Check original dimensions

### "Optimized larger than original"
- Image already optimized
- Try different format
- May not benefit from compression

### "Missing alt text" warnings
- Not an error, just a recommendation
- Update via WordPress Media Library
- Essential for accessibility

### WebP compatibility concerns
- Modern browsers (95%+) support WebP
- WordPress serves fallbacks automatically
- Use image CDN for automatic format selection

## Next Steps

After optimizing images:
1. **Test Performance**: Run PageSpeed Insights
2. **Monitor Analytics**: Check bounce rate, time on page
3. **Track Savings**: Calculate bandwidth reduction
4. **Document**: Keep record of optimizations
5. **Schedule**: Plan regular optimization cycles

## Additional Resources

- WordPress Media Library: `/wp-admin/upload.php`
- PageSpeed Insights: https://pagespeed.web.dev
- WebP Support: https://caniuse.com/webp
- Image SEO Guide: WordPress Codex

## Support

For issues with image optimization:
1. Check image is accessible via URL
2. Verify WordPress API credentials
3. Test with small images first
4. Review error messages carefully
5. Check server disk space
