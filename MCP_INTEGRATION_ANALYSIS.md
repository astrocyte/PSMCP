# WordPress MCP Integration Analysis for SST.NYC

## Executive Summary

After comprehensive evaluation of available WordPress MCP server implementations, we've determined that our **custom hybrid wp-cli + REST API approach** is the optimal solution for managing LearnDash LMS content.

## MCP Server Options Evaluated

### 1. Automattic/wordpress-mcp (Currently Installed)

**Version:** 0.2.4
**Status:** ✅ Active on staging.sst.nyc
**Repository:** https://github.com/Automattic/wordpress-mcp

#### Features
- **Dual Transport:** STDIO and HTTP-based (Streamable) transports
- **JWT Authentication:** Secure token-based auth with 1-24 hour tokens
- **Built-in Tools:** 50+ tools for posts, pages, media, users, categories, tags
- **WooCommerce Support:** Native tools for products and orders
- **Custom Post Types:** `wp_cpt_search`, `wp_get_cpt`, `wp_add_cpt`, `wp_update_cpt`, `wp_delete_cpt`
- **Admin Interface:** React-based token management dashboard

#### LearnDash Compatibility
**❌ LIMITED** - LearnDash restricts REST API access with authentication errors:
```json
{
  "code": "learndash_rest_forbidden_context",
  "message": "Sorry, you are not allowed to access this resource."
}
```

While the plugin detects LearnDash post types (`sfwd-courses`, `sfwd-lessons`, `sfwd-topic`, `sfwd-quiz`, etc.), it **cannot access them** due to LearnDash's security restrictions on the REST API.

#### Pros
- ✅ Official Automattic plugin, well-maintained
- ✅ Excellent WooCommerce integration
- ✅ Professional JWT authentication system
- ✅ Works great for standard WordPress content
- ✅ 200+ test cases, production-ready

#### Cons
- ❌ Cannot access LearnDash content (REST API restrictions)
- ❌ No wp-cli integration for restricted content
- ❌ Limited to what WordPress REST API exposes
- ❌ Cannot manage course enrollments or user progress

---

### 2. NavidArd/wordpress-mcp

**Repository:** https://github.com/NavidArd/wordpress-mcp

#### Features
- Dual transport (STDIO + Streamable HTTP)
- Experimental REST API CRUD tools
- 200+ test cases
- Enterprise-grade authentication

#### LearnDash Compatibility
**❌ SAME LIMITATION** - Uses WordPress REST API, subject to same LearnDash restrictions

---

### 3. InstaWP/mcp-wp-php

**Repository:** https://github.com/InstaWP/mcp-wp-php

#### Features
- Built on official PHP MCP SDK
- 17 WordPress tools across content, discovery, taxonomy
- 110+ unit tests
- Safe mode for destructive operations

#### LearnDash Compatibility
**❌ SAME LIMITATION** - REST API based, no LMS-specific features documented

---

### 4. mcp-wp/mcp-server

**Repository:** https://github.com/mcp-wp/mcp-server

#### Features
- Uses `logiscape/mcp-sdk-php` package
- Implements Streamable HTTP transport
- WordPress REST API integration
- WP-CLI AI command compatibility

#### LearnDash Compatibility
**❌ SAME LIMITATION** - General WordPress implementation, no LMS specialization

---

## Our Custom Solution: wordpress-mcp-server (Hybrid Approach)

### Architecture

```
wordpress-mcp-server/
├── wp_cli.py          # SSH-based wp-cli wrapper (PRIMARY)
├── wp_api.py          # REST API client (SUPPLEMENTARY)
├── learndash_manager.py  # LearnDash-specific operations
├── woocommerce_manager.py
├── seo_tools.py
├── image_optimizer.py
└── server.py          # MCP server exposing 33 tools
```

### Why It's Better

#### 1. **LearnDash Full Access** ✅
- Uses wp-cli which has **full WordPress admin privileges**
- Can access ALL LearnDash data: courses, lessons, topics, quizzes, questions
- Can manage enrollments, groups, and user progress
- No REST API restrictions

#### 2. **Hybrid Approach** ✅
- wp-cli for operations (create, update, delete, enrollments)
- REST API for fast read-only queries
- Best of both worlds

#### 3. **LearnDash-Specific Tools** ✅
We have dedicated tools that other MCP servers don't:
- `ld_create_course` - Create LearnDash courses
- `ld_update_course` - Update course settings
- `ld_list_courses` - List all courses
- `ld_create_lesson` - Create lessons with course association
- `ld_update_lesson` - Update lesson content
- `ld_create_quiz` - Create quizzes
- `ld_add_quiz_question` - Add questions to quizzes
- `ld_enroll_user` - Enroll users in courses
- `ld_create_group` - Create learner groups

#### 4. **SEO & Image Optimization** ✅
Unique features not available in any other MCP server:
- Elementor content extraction
- SEO analysis
- WebP image conversion
- Bulk image optimization
- Alt text validation

#### 5. **Maintainability** ✅
- Wraps battle-tested wp-cli instead of reimplementing WordPress logic
- Secure SSH-based execution
- No WordPress plugin installation required on production

### Tool Count Comparison

| MCP Server | Total Tools | LearnDash Tools | WooCommerce Tools | SEO Tools | Image Tools |
|------------|-------------|-----------------|-------------------|-----------|-------------|
| **Automattic/wordpress-mcp** | 50+ | 0 (blocked) | 10+ | 0 | 0 |
| **NavidArd/wordpress-mcp** | ~30 | 0 (blocked) | 0 | 0 | 0 |
| **InstaWP/mcp-wp-php** | 17 | 0 | 0 | 0 | 0 |
| **mcp-wp/mcp-server** | ~20 | 0 | 0 | 0 | 0 |
| **Our wordpress-mcp-server** | **33** | **9** | **6** | **2** | **3** |

## Recommended Integration Strategy

### Option A: Dual MCP Servers (RECOMMENDED)

Run **both** MCP servers for different purposes:

1. **Automattic/wordpress-mcp** (Plugin)
   - Use for: Standard WordPress content (posts, pages, media)
   - Use for: WooCommerce products and orders
   - Use for: Quick read operations via REST API

2. **Our wordpress-mcp-server** (Custom)
   - Use for: ALL LearnDash operations
   - Use for: SEO analysis and optimization
   - Use for: Image processing and optimization
   - Use for: Complex operations requiring wp-cli

### Configuration

#### Claude Desktop Config (`claude_desktop_config.json`)

```json
{
  "mcpServers": {
    "staging-wordpress": {
      "command": "npx",
      "args": ["-y", "@automattic/mcp-wordpress-remote@latest"],
      "env": {
        "WP_API_URL": "https://staging.sst.nyc/",
        "JWT_TOKEN": "eyJ0eXA...",
        "LOG_FILE": "/tmp/wordpress-mcp-staging.log"
      }
    },
    "staging-learndash": {
      "command": "python",
      "args": ["-m", "src.server"],
      "cwd": "/Users/shawnshirazi/Experiments/PredictiveSafety/wordpress-mcp-server",
      "env": {
        "WP_SITE_URL": "https://staging.sst.nyc",
        "WP_SSH_HOST": "147.93.88.8",
        "WP_SSH_USER": "u629344933",
        "WP_SSH_PORT": "65002",
        "WP_SSH_KEY_PATH": "/Users/shawnshirazi/.ssh/id_ed25519",
        "WP_REMOTE_PATH": "/home/u629344933/domains/sst.nyc/public_html/staging"
      }
    }
  }
}
```

### Option B: Custom Server Only

Use only our custom wordpress-mcp-server if you prefer a single unified interface.

**Pros:**
- Single authentication mechanism
- Consistent command interface
- Full access to everything

**Cons:**
- Slower for simple read operations (wp-cli overhead)
- Requires SSH access to server

## Test Results

### Automattic/wordpress-mcp Plugin

✅ **Working:**
- JWT authentication
- Post/page CRUD operations
- WooCommerce product queries
- Media management
- User management
- Site settings

❌ **Not Working:**
- LearnDash course access (REST API restriction)
- LearnDash lesson queries
- Course enrollment management

### Our wordpress-mcp-server

✅ **Verified Working:**
- SSH key authentication
- wp-cli execution
- Course enumeration (12 courses found)
- Student enrollment queries (5 students, 3 enrolled in 32Hr Supervisor)
- Lesson title updates (12 lessons renamed successfully)
- Database queries
- Cache management

## Conclusion

**RECOMMENDATION:** Deploy both MCP servers

1. Keep **Automattic/wordpress-mcp** active for:
   - Standard WordPress content management
   - WooCommerce operations
   - Fast REST API queries

2. Use **our custom wordpress-mcp-server** for:
   - **All LearnDash operations** (courses, lessons, quizzes, enrollments)
   - SEO optimization
   - Image processing
   - Advanced wp-cli operations

This hybrid approach gives you the best of both worlds:
- Modern REST API performance where available
- Full wp-cli power where needed (especially LearnDash)
- Comprehensive toolset for all SST.NYC operations

## Next Steps

1. ✅ Keep Automattic plugin active on staging
2. ✅ Configure both MCP servers in Claude Desktop
3. ⏭️ Test dual server configuration
4. ⏭️ Document which tools to use for which tasks
5. ⏭️ Create LearnDash course management workflows
6. ⏭️ Deploy to production when ready

---

*Analysis Date: November 23, 2025*
*Staging Environment: https://staging.sst.nyc*
*WordPress MCP Plugin: v0.2.4*
*LearnDash Version: Active (REST API restricted)*
