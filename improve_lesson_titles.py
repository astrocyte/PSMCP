#!/usr/bin/env python3
"""Improve LearnDash lesson titles for better organization."""

import sys
import re
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

from src.config import WordPressConfig
from src.wp_cli import WPCLIClient


# Define improved title mappings
TITLE_IMPROVEMENTS = {
    # 10 Hr Worker SST
    4397: {
        4400: "Module 01: Introduction and Construction Statistics"
    },

    # 4 hour Supported Scaffold User
    979: {
        1011: "Module 01: Introduction to Supported Scaffolds",
        1023: "Module 02: Types and Components of Supported Scaffolds",
        1050: "Module 03: Scaffold Accident Prevention",
        1064: "Module 04: OSHA Regulations and Standards",
        1081: "Module 05: NYC Building Codes Overview",
        1095: "Module 06: DOB Requirements and Permits",
        1110: "Module 07: Fall Protection Fundamentals",
        1121: "Module 08: PPE and Fall Arrest Systems",
        1140: "Module 09: Safe Scaffold Use and Procedures",
        1157: "Module 10: Scaffold Inspection Requirements",
        1172: "Module 11: Emergency Response and Resources"
    }
}


def get_course_prefix(course_id, course_title):
    """Generate clean course prefix for lesson titles."""
    # Shorter, cleaner prefixes
    prefixes = {
        4397: "10Hr Worker",
        4425: "8Hr Renewal",
        4380: "16Hr Supervisor Renewal",
        4378: "22Hr Supervisor Upgrade",
        4974: "32Hr Supervisor",
        979: "4Hr Scaffold"
    }

    return prefixes.get(int(course_id), course_title[:20])


def improve_lesson_title(course_id, lesson_id, current_title):
    """Generate improved lesson title."""
    # Check if we have a custom mapping
    if int(course_id) in TITLE_IMPROVEMENTS:
        if int(lesson_id) in TITLE_IMPROVEMENTS[int(course_id)]:
            base_title = TITLE_IMPROVEMENTS[int(course_id)][int(lesson_id)]
            prefix = get_course_prefix(course_id, "")
            return f"{prefix} - {base_title}"

    # Otherwise, clean up the existing title
    # Remove the long course prefix if present
    cleaned = current_title

    # Remove course prefix variations
    patterns = [
        r'^.*?\s*-\s*SCA-\d+\s*',  # Remove "Course - SCA-212"
        r'^.*?\s*-\s*Module\s*',   # Remove "Course - Module"
        r'^SCA-\d+\s*',            # Remove "SCA-212"
    ]

    for pattern in patterns:
        cleaned = re.sub(pattern, '', cleaned, flags=re.IGNORECASE)

    # Normalize module numbers to "Module 01" format
    cleaned = re.sub(r'Module\s*(\d+)\s*[:-]?\s*', lambda m: f"Module {int(m.group(1)):02d}: ", cleaned, flags=re.IGNORECASE)

    # Remove "Video 1" type labels (confusing)
    cleaned = re.sub(r'\s*Video\s*\d+:\s*', ' ', cleaned)

    # Clean up whitespace
    cleaned = ' '.join(cleaned.split())

    # Add course prefix
    prefix = get_course_prefix(course_id, "")
    return f"{prefix} - {cleaned}"


def rename_lesson(wp_cli, lesson_id, new_title, dry_run=False):
    """Rename a lesson."""
    if dry_run:
        return True

    try:
        # Escape quotes in title
        escaped_title = new_title.replace('"', '\\"')
        wp_cli.execute(
            f'post update {lesson_id} --post_title="{escaped_title}"',
            format=None
        )
        return True
    except Exception as e:
        print(f"  ❌ Error: {e}")
        return False


def main():
    """Main execution."""
    print("=" * 80)
    print("LearnDash Lesson Title Improvement Tool")
    print("=" * 80)
    print()

    # Check for dry-run mode
    dry_run = '--dry-run' in sys.argv
    if dry_run:
        print("🔍 DRY RUN MODE - Preview changes only")
        print()

    # Initialize
    try:
        config = WordPressConfig.from_env()
        wp_cli = WPCLIClient(config)
        print(f"✅ Connected to: {config.site_url}")
        print()
    except Exception as e:
        print(f"❌ Configuration error: {e}")
        return 1

    # Get all published courses
    print("Fetching courses and lessons...")
    courses_json = wp_cli.execute(
        "post list --post_type=sfwd-courses --post_status=publish --fields=ID,post_title",
        format="json"
    )

    total_updated = 0
    total_unchanged = 0
    total_errors = 0

    for course in courses_json:
        course_id = course['ID']
        course_title = course['post_title']

        # Get lessons for this course
        try:
            lessons_json = wp_cli.execute(
                f'post list --post_type=sfwd-lessons --meta_key=course_id --meta_value={course_id} --post_status=publish,draft --fields=ID,post_title --orderby=menu_order',
                format="json"
            )

            if not lessons_json:
                continue

            print(f"\n{'=' * 80}")
            print(f"📚 Course: {course_title} (ID: {course_id})")
            print(f"   Lessons: {len(lessons_json)}")
            print('=' * 80)

            for lesson in lessons_json:
                lesson_id = lesson['ID']
                current_title = lesson['post_title']

                # Generate improved title
                new_title = improve_lesson_title(course_id, lesson_id, current_title)

                # Check if change is needed
                if current_title == new_title:
                    print(f"\n⏭️  Lesson {lesson_id}: No change needed")
                    print(f"   {current_title}")
                    total_unchanged += 1
                    continue

                print(f"\n🔄 Lesson {lesson_id}:")
                print(f"   OLD: {current_title}")
                print(f"   NEW: {new_title}")

                if dry_run:
                    print(f"   [DRY RUN] Would update")
                    total_updated += 1
                else:
                    if rename_lesson(wp_cli, lesson_id, new_title, dry_run):
                        print(f"   ✅ Updated successfully")
                        total_updated += 1
                    else:
                        total_errors += 1

        except Exception as e:
            print(f"Warning: Error processing course {course_id}: {e}")
            continue

    # Summary
    print("\n" + "=" * 80)
    print("Summary")
    print("=" * 80)
    print(f"✅ Updated: {total_updated} lessons")
    print(f"⏭️  Unchanged: {total_unchanged} lessons")
    print(f"❌ Errors: {total_errors} lessons")

    if dry_run:
        print("\n🔍 This was a DRY RUN - no changes made")
        print("   Run without --dry-run to apply changes")
    else:
        print("\n🎉 Lesson titles have been improved!")

    wp_cli.disconnect()
    return 0 if total_errors == 0 else 1


if __name__ == "__main__":
    if '--help' in sys.argv or '-h' in sys.argv:
        print("Usage: python improve_lesson_titles.py [--dry-run]")
        print()
        print("Improves lesson titles for better organization:")
        print("  - Shorter course prefixes (10Hr Worker vs full name)")
        print("  - Consistent module numbering (Module 01, 02, etc.)")
        print("  - Clearer topic descriptions")
        print("  - Removes redundant text")
        print()
        print("Options:")
        print("  --dry-run    Preview changes without applying")
        print("  --help, -h   Show this help message")
        sys.exit(0)

    sys.exit(main())
