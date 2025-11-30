#!/usr/bin/env python3
"""Quick MCP functionality test."""

import sys
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

from src.config import WordPressConfig
from src.wp_cli import WPCLIClient
from src.wp_api import WordPressAPIClient


def test_wp_cli():
    """Test wp-cli functionality."""
    print("=" * 60)
    print("Testing WP-CLI")
    print("=" * 60)

    try:
        config = WordPressConfig.from_env()
        wp_cli = WPCLIClient(config)

        # Test 1: Get WordPress version
        version = wp_cli.execute("core version", format=None)
        print(f"✅ WordPress Version: {version}")

        # Test 2: Get site URL
        url = wp_cli.execute("option get siteurl", format=None)
        print(f"✅ Site URL: {url}")

        # Test 3: Get active theme
        theme = wp_cli.execute("theme list --status=active --field=name", format=None)
        print(f"✅ Active Theme: {theme}")

        # Test 4: List active plugins (JSON format)
        plugins = wp_cli.execute("plugin list --status=active --fields=name,version", format="json")
        print(f"✅ Active Plugins: {len(plugins)} found")
        for plugin in plugins[:5]:  # Show first 5
            print(f"   • {plugin['name']} (v{plugin['version']})")
        if len(plugins) > 5:
            print(f"   ... and {len(plugins) - 5} more")

        wp_cli.disconnect()
        return True

    except Exception as e:
        print(f"❌ WP-CLI test failed: {e}")
        import traceback
        traceback.print_exc()
        return False


def test_wp_api():
    """Test WordPress REST API."""
    print("\n" + "=" * 60)
    print("Testing WordPress REST API")
    print("=" * 60)

    try:
        config = WordPressConfig.from_env()
        wp_api = WordPressAPIClient(config)

        # Test: Get recent posts
        posts = wp_api.get_posts(per_page=5)
        print(f"✅ Recent Posts: {len(posts)} found")
        for post in posts:
            title = post.get('title', {}).get('rendered', 'Untitled')
            print(f"   • {title}")

        return True

    except Exception as e:
        print(f"❌ API test failed: {e}")
        print(f"   Note: This might require valid API credentials")
        return False


def test_learndash_check():
    """Check if LearnDash is installed."""
    print("\n" + "=" * 60)
    print("Checking LearnDash Installation")
    print("=" * 60)

    try:
        config = WordPressConfig.from_env()
        wp_cli = WPCLIClient(config)

        # Check if LearnDash plugin is active
        plugins = wp_cli.execute("plugin list --status=active --format=json")
        ld_plugin = next((p for p in plugins if 'learndash' in p['name'].lower()), None)

        if ld_plugin:
            print(f"✅ LearnDash is installed: {ld_plugin['name']} v{ld_plugin['version']}")
        else:
            print("⚠️  LearnDash plugin not found in active plugins")

        wp_cli.disconnect()
        return ld_plugin is not None

    except Exception as e:
        print(f"❌ LearnDash check failed: {e}")
        return False


def test_woocommerce_check():
    """Check if WooCommerce is installed."""
    print("\n" + "=" * 60)
    print("Checking WooCommerce Installation")
    print("=" * 60)

    try:
        config = WordPressConfig.from_env()
        wp_cli = WPCLIClient(config)

        # Check if WooCommerce plugin is active
        plugins = wp_cli.execute("plugin list --status=active --format=json")
        wc_plugin = next((p for p in plugins if 'woocommerce' in p['name'].lower()), None)

        if wc_plugin:
            print(f"✅ WooCommerce is installed: {wc_plugin['name']} v{wc_plugin['version']}")
        else:
            print("⚠️  WooCommerce plugin not found in active plugins")

        wp_cli.disconnect()
        return wc_plugin is not None

    except Exception as e:
        print(f"❌ WooCommerce check failed: {e}")
        return False


def main():
    """Run all tests."""
    print("\n🧪 WordPress MCP Server - Quick Test\n")

    # Configuration check
    try:
        config = WordPressConfig.from_env()
        errors = config.validate()
        if errors:
            print(f"⚠️  Configuration warnings: {', '.join(errors)}")
            print("   Some tests may fail\n")
    except Exception as e:
        print(f"❌ Configuration failed: {e}\n")
        return 1

    # Run tests
    cli_ok = test_wp_cli()
    api_ok = test_wp_api()
    ld_ok = test_learndash_check()
    wc_ok = test_woocommerce_check()

    # Summary
    print("\n" + "=" * 60)
    print("Test Summary")
    print("=" * 60)
    print(f"{'✅' if cli_ok else '❌'} WP-CLI Connection")
    print(f"{'✅' if api_ok else '❌'} REST API Connection")
    print(f"{'✅' if ld_ok else '⚠️ '} LearnDash Available")
    print(f"{'✅' if wc_ok else '⚠️ '} WooCommerce Available")

    if cli_ok:
        print("\n🎉 Core functionality is working!")
        print("\nYour MCP server is ready to use with Claude Desktop.")
        print("\nNext steps:")
        print("1. Add this server to your Claude Desktop config")
        print("2. Restart Claude Desktop")
        print("3. Start using WordPress management tools!")
        return 0
    else:
        print("\n⚠️  Some issues detected - check errors above")
        return 1


if __name__ == "__main__":
    sys.exit(main())
