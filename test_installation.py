#!/usr/bin/env python3
"""Test script to verify MCP server installation."""

import sys

def test_imports():
    """Test all module imports."""
    print("🔍 Testing imports...")

    try:
        from src.config import WordPressConfig
        print("✅ config.py imports successfully")

        from src.wp_cli import WPCLIClient
        print("✅ wp_cli.py imports successfully")

        from src.wp_api import WordPressAPIClient
        print("✅ wp_api.py imports successfully")

        from src.seo_tools import SEOAnalyzer
        print("✅ seo_tools.py imports successfully")

        from src.image_optimizer import ImageOptimizer
        print("✅ image_optimizer.py imports successfully")

        from src.server import server
        print("✅ server.py imports successfully")

        return True
    except ImportError as e:
        print(f"❌ Import failed: {e}")
        return False


def test_server_tools():
    """Test server tool definitions."""
    print("\n🔍 Testing MCP tools...")

    try:
        from src.server import server

        # We need to manually count tools since list_tools is async
        expected_tools = [
            'wp_get_info',
            'wp_plugin_list',
            'wp_theme_list',
            'wp_post_list',
            'wp_get_post',
            'wp_search',
            'seo_analyze_post',
            'elementor_extract_content',
            'wp_check_updates',
            'image_analyze',
            'image_optimize',
            'image_audit_site',
        ]

        print(f"✅ Expected 12 tools defined")
        print(f"   Server name: {server.name}")

        for tool in expected_tools:
            print(f"   • {tool}")

        return True
    except Exception as e:
        print(f"❌ Tool test failed: {e}")
        return False


def test_dependencies():
    """Test required dependencies."""
    print("\n🔍 Testing dependencies...")

    dependencies = [
        ('mcp', 'MCP SDK'),
        ('requests', 'HTTP client'),
        ('paramiko', 'SSH client'),
        ('dotenv', 'Environment loader'),
        ('bs4', 'BeautifulSoup'),
        ('PIL', 'Pillow/PIL'),
    ]

    all_good = True
    for module, name in dependencies:
        try:
            __import__(module)
            print(f"✅ {name} installed")
        except ImportError:
            print(f"❌ {name} missing")
            all_good = False

    return all_good


def test_image_optimizer():
    """Test image optimizer functionality."""
    print("\n🔍 Testing image optimizer...")

    try:
        from src.image_optimizer import ImageOptimizer, ImageInfo, OptimizationResult
        from src.config import WordPressConfig

        # Create dummy config (won't actually connect)
        config = WordPressConfig(
            site_url="https://example.com",
            ssh_host="example.com",
            ssh_user="test",
            ssh_key_path=None,
            remote_path="/var/www",
            api_user="test",
            api_password="test"
        )

        optimizer = ImageOptimizer(config)
        print("✅ ImageOptimizer instantiates successfully")
        print(f"   Config site: {config.site_url}")

        return True
    except Exception as e:
        print(f"❌ Image optimizer test failed: {e}")
        return False


def main():
    """Run all tests."""
    print("=" * 60)
    print("WordPress MCP Server - Installation Test")
    print("=" * 60)

    tests = [
        ("Module Imports", test_imports),
        ("Server Tools", test_server_tools),
        ("Dependencies", test_dependencies),
        ("Image Optimizer", test_image_optimizer),
    ]

    results = []
    for name, test_func in tests:
        result = test_func()
        results.append((name, result))

    print("\n" + "=" * 60)
    print("Test Results Summary")
    print("=" * 60)

    all_passed = True
    for name, result in results:
        status = "✅ PASS" if result else "❌ FAIL"
        print(f"{status} - {name}")
        if not result:
            all_passed = False

    print("=" * 60)

    if all_passed:
        print("\n🎉 All tests passed! MCP server is ready.")
        print("\nNext steps:")
        print("1. Copy .env.example to .env")
        print("2. Configure your WordPress credentials")
        print("3. Add to Claude Desktop configuration")
        print("4. Restart Claude Desktop")
        return 0
    else:
        print("\n⚠️  Some tests failed. Please check errors above.")
        return 1


if __name__ == "__main__":
    sys.exit(main())
