#!/usr/bin/env python3
"""Rename all LearnDash lessons to include their parent course name as prefix."""

import sys
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

from src.config import WordPressConfig
from src.wp_cli import WPCLIClient


def get_all_courses_with_lessons(wp_cli):
    """Get all courses and their associated lessons."""
    print("Fetching all courses and lessons...")

    # Get all published courses
    courses_json = wp_cli.execute(
        "post list --post_type=sfwd-courses --post_status=publish --fields=ID,post_title",
        format="json"
    )

    courses_with_lessons = []

    for course in courses_json:
        course_id = course['ID']
        course_title = course['post_title']

        # Get lessons for this course using wp-cli
        try:
            lessons_json = wp_cli.execute(
                f'post list --post_type=sfwd-lessons --meta_key=course_id --meta_value={course_id} --post_status=publish,draft --fields=ID,post_title --orderby=menu_order',
                format="json"
            )

            lessons = []
            for lesson in lessons_json:
                lessons.append({
                    'id': lesson['ID'],
                    'title': lesson['post_title']
                })

            if lessons:
                courses_with_lessons.append({
                    'id': course_id,
                    'title': course_title,
                    'lessons': lessons
                })

        except Exception as e:
            print(f"Warning: Could not get lessons for course {course_id}: {e}")
            continue

    return courses_with_lessons


def rename_lesson(wp_cli, lesson_id, new_title, dry_run=False):
    """Rename a lesson."""
    if dry_run:
        print(f"  [DRY RUN] Would rename lesson {lesson_id} to: {new_title}")
        return True

    try:
        wp_cli.execute(
            f'post update {lesson_id} --post_title="{new_title}"',
            format=None
        )
        return True
    except Exception as e:
        print(f"  ❌ Error renaming lesson {lesson_id}: {e}")
        return False


def main():
    """Main execution."""
    print("=" * 70)
    print("LearnDash Lesson Renamer - Add Course Prefix")
    print("=" * 70)
    print()

    # Check for dry-run mode
    dry_run = '--dry-run' in sys.argv
    if dry_run:
        print("🔍 DRY RUN MODE - No changes will be made")
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

    # Get all courses and lessons
    courses_data = get_all_courses_with_lessons(wp_cli)

    if not courses_data:
        print("⚠️  No courses with lessons found")
        return 0

    print(f"Found {len(courses_data)} courses with lessons")
    print()

    # Process each course
    total_renamed = 0
    total_skipped = 0
    total_errors = 0

    for course in courses_data:
        course_title = course['title']
        course_id = course['id']
        lessons = course['lessons']

        print(f"📚 Course: {course_title} (ID: {course_id})")
        print(f"   Lessons: {len(lessons)}")
        print()

        for lesson in lessons:
            lesson_id = lesson['id']
            current_title = lesson['title']

            # Check if lesson already has course prefix
            if current_title.startswith(f"{course_title} - "):
                print(f"  ⏭️  Skipped (already prefixed): {current_title}")
                total_skipped += 1
                continue

            # Create new title with course prefix
            new_title = f"{course_title} - {current_title}"

            print(f"  🔄 Renaming:")
            print(f"     Old: {current_title}")
            print(f"     New: {new_title}")

            # Rename the lesson
            if rename_lesson(wp_cli, lesson_id, new_title, dry_run):
                if not dry_run:
                    print(f"  ✅ Renamed successfully")
                total_renamed += 1
            else:
                total_errors += 1

            print()

        print("-" * 70)
        print()

    # Summary
    print("=" * 70)
    print("Summary")
    print("=" * 70)
    print(f"✅ Renamed: {total_renamed} lessons")
    print(f"⏭️  Skipped: {total_skipped} lessons (already prefixed)")
    print(f"❌ Errors: {total_errors} lessons")
    print()

    if dry_run:
        print("This was a DRY RUN - no actual changes were made")
        print("Run without --dry-run to apply changes")
    else:
        print("All lessons have been renamed!")

    # Disconnect
    wp_cli.disconnect()

    return 0 if total_errors == 0 else 1


if __name__ == "__main__":
    if '--help' in sys.argv or '-h' in sys.argv:
        print("Usage: python rename_lessons_with_course_prefix.py [--dry-run]")
        print()
        print("Options:")
        print("  --dry-run    Preview changes without applying them")
        print("  --help, -h   Show this help message")
        sys.exit(0)

    sys.exit(main())
