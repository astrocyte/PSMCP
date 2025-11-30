#!/usr/bin/env python3
"""Test MCP tools manually."""

import asyncio
import sys
import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

# Add src to path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'src'))

from src.config import WordPressConfig
from src.wp_cli import WPCLIClient
from src.wp_api import WordPressAPIClient


async def test_config():
    """Test configuration loading."""
    print("=" * 60)
    print("Testing Configuration")
    print("=" * 60)

    try:
        config = WordPressConfig.from_env()
        print(f"✅ Configuration loaded successfully")
        print(f"   Site URL: {config.site_url}")
        print(f"   SSH Host: {config.ssh_host}:{config.ssh_port}")
        print(f"   SSH User: {config.ssh_user}")
        print(f"   Remote Path: {config.remote_path}")
        print(f"   API User: {config.api_user}")

        # Validate config
        errors = config.validate()
        if errors:
            print(f"⚠️  Configuration warnings: {', '.join(errors)}")
        else:
            print("✅ Configuration is valid")

        return config
    except Exception as e:
        print(f"❌ Configuration failed: {e}")
        return None


async def test_ssh_connection(config):
    """Test SSH connection to staging server."""
    print("\n" + "=" * 60)
    print("Testing SSH Connection")
    print("=" * 60)

    try:
        wp_cli = WPCLIClient(config)

        # Test a simple wp-cli command
        result = wp_cli.run_command("core version")
        print(f"✅ SSH connection successful")
        print(f"   WordPress version: {result.strip()}")
        return True
    except Exception as e:
        print(f"❌ SSH connection failed: {e}")
        print(f"   This might be due to SSH authentication issues")
        print(f"   Check your SSH password or key configuration in .env")
        return False


async def test_api_connection(config):
    """Test WordPress REST API connection."""
    print("\n" + "=" * 60)
    print("Testing WordPress REST API")
    print("=" * 60)

    try:
        wp_api = WordPressAPIClient(config)

        # Test API with a simple request
        site_info = wp_api.get("/")
        print(f"✅ API connection successful")
        print(f"   Site name: {site_info.get('name', 'Unknown')}")
        print(f"   Description: {site_info.get('description', 'Unknown')}")
        return True
    except Exception as e:
        print(f"❌ API connection failed: {e}")
        print(f"   Check your JWT token or API credentials in .env")
        return False


async def test_wp_info(config):
    """Test getting WordPress info via wp-cli."""
    print("\n" + "=" * 60)
    print("Testing wp_get_info Tool")
    print("=" * 60)

    try:
        wp_cli = WPCLIClient(config)

        # Simulate the wp_get_info tool
        version = wp_cli.run_command("core version")
        url = wp_cli.run_command("option get siteurl")
        theme = wp_cli.run_command("theme list --status=active --field=name")

        print(f"✅ WordPress Info:")
        print(f"   Version: {version.strip()}")
        print(f"   URL: {url.strip()}")
        print(f"   Active Theme: {theme.strip()}")
        return True
    except Exception as e:
        print(f"❌ Failed to get WordPress info: {e}")
        return False


async def test_plugin_list(config):
    """Test listing WordPress plugins."""
    print("\n" + "=" * 60)
    print("Testing wp_plugin_list Tool")
    print("=" * 60)

    try:
        wp_cli = WPCLIClient(config)

        # Get active plugins
        plugins = wp_cli.run_command("plugin list --status=active --format=json")
        print(f"✅ Plugin list retrieved")
        print(f"   Found active plugins (showing first 200 chars):")
        print(f"   {plugins[:200]}...")
        return True
    except Exception as e:
        print(f"❌ Failed to list plugins: {e}")
        return False


async def main():
    """Run all tests."""
    print("\n")
    print("🧪 WordPress MCP Server - Functional Test Suite")
    print("\n")

    # Test 1: Configuration
    config = await test_config()
    if not config:
        print("\n❌ Cannot proceed without valid configuration")
        return 1

    # Test 2: SSH Connection
    ssh_ok = await test_ssh_connection(config)

    # Test 3: API Connection
    api_ok = await test_api_connection(config)

    # Test 4: WordPress Info (only if SSH works)
    if ssh_ok:
        await test_wp_info(config)
        await test_plugin_list(config)

    # Summary
    print("\n" + "=" * 60)
    print("Test Summary")
    print("=" * 60)
    print(f"{'✅' if config else '❌'} Configuration")
    print(f"{'✅' if ssh_ok else '❌'} SSH Connection")
    print(f"{'✅' if api_ok else '❌'} REST API Connection")

    if ssh_ok and api_ok:
        print("\n🎉 All core systems operational!")
        return 0
    elif ssh_ok or api_ok:
        print("\n⚠️  Partial functionality - some systems need attention")
        return 0
    else:
        print("\n❌ Connection issues detected - check your credentials")
        return 1


if __name__ == "__main__":
    sys.exit(asyncio.run(main()))
