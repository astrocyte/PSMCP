# LearnDash LMS Best Practices for NYC DOB SST Training
## Predictive Safety - SST.NYC

> **Last Updated**: November 2025
> **Version**: 1.0
> **Applies To**: LearnDash 4.x, NYC DOB SST Training Requirements

---

## Table of Contents

1. [NYC DOB SST Compliance Requirements](#nyc-dob-sst-compliance-requirements)
2. [Course Structure & Design](#course-structure--design)
3. [Quiz & Assessment Strategy](#quiz--assessment-strategy)
4. [Certificate Management](#certificate-management)
5. [Student Enrollment & Progression](#student-enrollment--progression)
6. [MCP Server Integration](#mcp-server-integration)
7. [Reporting & Compliance Tracking](#reporting--compliance-tracking)
8. [Technical Implementation](#technical-implementation)

---

## NYC DOB SST Compliance Requirements

### Overview of Local Law 196

[Local Law 196 of 2017](https://www.oshaeducationcenter.com/new-york/sst/local-law-196/) requires that construction and demolition workers on NYC job sites with a Construction Superintendent, Site Safety Coordinator, or Site Safety Manager must complete DOB-approved Site Safety Training (SST).

### Training Hour Requirements

#### SST Worker Card (40 Hours Total)
- **30-Hour OSHA for Construction** (Core requirement)
- **8-Hour Fall Prevention** (DOB-approved)
- **2-Hour Drug and Alcohol Awareness**

#### SST Supervisor Card (62 Hours Total)
- **All Worker requirements** (40 hours)
- **22 Additional Supervisor-Specific Hours**
- As of December 2019, all supervisors at NYC jobsites with a Site Safety Plan must hold a valid SST Supervisor card

#### SST Refresher Courses
- **8-Hour SST Worker Renewal** (every 5 years)
- **16-Hour SST Supervisor Renewal** (every 5 years)

### Card Validity and Renewal

- SST cards are **valid for 5 years** from date of issuance
- Cards must be renewed within **12 months before expiration**
- **CRITICAL**: SST cards can only be renewed while active - expired cards cannot be renewed
- Renewal requires completion through [DOB-Registered Course Providers](https://www.nyc.gov/site/buildings/safety/sst-worker-information.page)

### Temporary Cards

Workers with 10-Hour or 30-Hour OSHA certification (completed within past 5 years) are eligible for:
- **SST Temporary Card** (valid for 6 months)
- During this period, complete remaining training for permanent SST Worker Card
- If you have OSHA 30, complete 10 additional DOB-approved hours

### Compliance Mandates

- All training must be completed through **NYC DOB-registered course providers**
- Without an SST card, workers **cannot work** at NYC construction sites with a Site Safety Plan
- Training records must be maintained for compliance audits

---

## Course Structure & Design

### LearnDash Hierarchy Model

LearnDash uses a hierarchical content structure with [four possible levels](https://lmscrafter.com/learndash-course-creation-step-by-step-guide/):

```
Course (Container)
  └── Course Sections (Optional - for large courses)
       └── Lessons (Primary content units)
            └── Topics (Optional - sub-lessons)
                 └── Quizzes/Assignments
```

### Best Practice: Keep It Simple

**Recommendation**: Use **2-level structure** for SST courses:

```
Course: "32 Hour SST Supervisor"
  └── Section 1: OSHA 30-Hour Construction
       └── Lesson 1: Introduction to OSHA
       └── Lesson 2: Fall Protection
       └── Lesson 3: Electrical Safety
       └── Quiz 1: OSHA Fundamentals
  └── Section 2: Fall Prevention (8 Hours)
       └── Lesson 4: Fall Hazards Recognition
       └── Lesson 5: Personal Fall Protection
       └── Quiz 2: Fall Prevention Assessment
  └── Section 3: Drug & Alcohol Awareness (2 Hours)
       └── Lesson 6: Workplace Substance Abuse
       └── Quiz 3: Final Assessment
```

**Why Avoid Topics?**
- [LearnDash only reports on course/lesson progress](https://profilelearning.com/implementing-the-learndash-lms-wordpress-plug-in-course-structure/), not topics
- Additional complexity without reporting benefits
- Harder for students to navigate

### Course Sections for Large Courses

Use [Course Sections](https://learndash.com/support/kb/core/courses/course-sections/) to break up content logically:

**Good Section Organization**:
- "Part 1: OSHA Fundamentals (Hours 1-10)"
- "Part 2: Hazard Recognition (Hours 11-20)"
- "Part 3: Safety Management (Hours 21-30)"

**Benefits**:
- Improved navigation for 30+ hour courses
- Clear progress indicators
- Matches DOB hour-tracking requirements

### Content Chunking Strategy

Follow the [cognitive chunking principle](https://eitca.org/e-learning/eitc-el-ldash-learndash-wordpress-lms/first-steps-in-learndash/an-introduction/examination-review-an-introduction/how-does-learndash-define-and-structure-the-hierarchy-of-courses-lessons-and-topics/):

**Lesson Length Guidelines**:
- **Video-based lessons**: 10-15 minutes
- **Reading-based lessons**: 5-10 minutes of reading time
- **Interactive lessons**: 15-20 minutes

**SST Course Example** (30-Hour OSHA):
- Break into **30 lessons** (1 hour each)
- Each lesson contains:
  - Video instruction (15-20 min)
  - Reading material (20-30 min)
  - Practice questions (10 min)
  - Mini-quiz (5-10 min)

### Linear vs. Flexible Progression

**For DOB SST Compliance → Use Linear Progression**:

```php
Course Settings:
├── Course Access Mode: Linear
├── Lesson Progression: Sequential
├── Drip-Feed Lessons: Optional (for pacing)
└── Prerequisites: Required for renewal courses
```

**Why Linear for SST?**
- Ensures students complete all required hours
- Prevents skipping critical safety content
- Satisfies DOB audit requirements
- Tracks time-on-task accurately

### Drip Scheduling for Completion Pacing

Use [drip scheduling](https://www.learndash.com/support/docs/core/courses/course-access/) to prevent rushing:

**Recommended Settings**:
- **10-Hour Course**: Release 2 lessons/day (5-day completion)
- **30-Hour Course**: Release 3 lessons/day (10-day completion)
- **8-Hour Renewal**: All available immediately (can complete in 1 day)

---

## Quiz & Assessment Strategy

### Quiz Types for SST Training

LearnDash supports [multiple question types](https://www.learndash.com/support/docs/core/quizzes/questions/):

**Recommended for OSHA/SST Content**:

1. **Multiple Choice** (Primary assessment method)
   - OSHA standard questions
   - Hazard recognition scenarios
   - Regulation compliance

2. **True/False** (Quick knowledge checks)
   - Safety protocol verification
   - Regulatory requirements

3. **Fill in the Blank** (Technical specifications)
   - OSHA standard numbers
   - Required heights/distances
   - Equipment specifications

4. **Essay Questions** (Supervisor courses only)
   - Incident response scenarios
   - Site safety plan development
   - Leadership situations

**Avoid for SST**:
- Sorting/Ordering (not suitable for safety compliance)
- Matrix (overly complex for certification)

### Quiz Placement Strategy

Following [best practices for retention](https://www.learndash.com/blog/maximize-retention-learndash-quizzes/), use **frequent, low-pressure quizzes**:

**Recommended Pattern**:
```
Lesson 1: Fall Protection Basics → Mini-Quiz (5 questions)
Lesson 2: Personal Fall Arrest → Mini-Quiz (5 questions)
Lesson 3: Ladder Safety → Mini-Quiz (5 questions)
Section 1 Complete → Comprehensive Quiz (15 questions)
```

**Benefits**:
- Immediate feedback helps correct misunderstandings
- Low-pressure environment reduces test anxiety
- Better knowledge retention than single final exam
- Students can retry and learn from mistakes

### Passing Scores & Retakes

**DOB SST Compliance Requirements**:

```php
Quiz Settings:
├── Passing Percentage: 80% minimum (OSHA standard)
├── Passing Required: Yes (enable "Require Passing Score")
├── Quiz Retakes: Unlimited (allow mastery learning)
├── Time Limit: 30 minutes per 10 questions
└── Certificate Threshold: 85% (higher than passing)
```

**Retake Strategy**:
- Allow **unlimited attempts** for lesson quizzes
- Allow **3 attempts** for section quizzes
- Require **wait time between retries** (e.g., 1 hour)
- **Track attempts** for compliance reporting

### Grading and Feedback Best Practices

From [LearnDash grading documentation](https://www.learndash.com/blog/essay-questions-and-assignment-points/):

**Automatic Grading** (Multiple Choice, True/False):
- Use precise decimal scoring for partial credit
- Enable negative points for incorrect answers (discourages guessing)
- Provide **immediate feedback** with answer explanations

**Manual Grading** (Essay Questions - Supervisor courses):

Three grading approaches:
1. **Not Graded, No Points** → Quiz status: "Pending" (most accurate)
2. **Not Graded, Full Points** → Awards points, grade later
3. **Graded, Full Points** → Auto-approve (not recommended for SST)

**Important**: [Certificates won't be awarded](https://eitca.org/e-learning/eitc-el-ldash-learndash-wordpress-lms/first-steps-in-learndash/evaluating-students-with-quizzes/examination-review-evaluating-students-with-quizzes/how-can-instructors-manually-grade-essay-questions-in-learndash-and-where-can-they-access-submitted-essays-for-grading/) if essay questions remain in "NOT GRADED" status

**Feedback Guidelines**:
```
Correct Answer:
"✓ Correct! OSHA requires guardrails at 6 feet for construction sites."

Incorrect Answer:
"✗ Not quite. Review 29 CFR 1926.501(b)(1). The correct height is 6 feet, not 10 feet."
```

### Quiz Timer and Time Tracking

**For DOB Hour Compliance**:
- Enable **timer display** to track lesson time
- Set **minimum time** requirements (e.g., 45 min for 1-hour lesson)
- Use [LearnDash Timers](https://learndash.com/support/kb/core/quizzes/quiz-access-progression/) to prevent rushing
- Track and report **actual time spent** for audits

---

## Certificate Management

### Certificate Requirements for SST

**NYC DOB Requires**:
- Certificate of completion from DOB-approved provider (you!)
- Student name, course title, completion date
- Training provider information
- Course hours completed
- Unique certificate ID (for verification)

### Certificate Setup in LearnDash

[Certificate configuration](https://www.learndash.com/support/docs/core/certificates/create-certificate/) for SST courses:

**Certificate Assignment Rules**:
```php
Certificate Award Criteria:
├── Course Completion: 100% required
├── Quiz Passing Score: 80% minimum
├── Certificate Threshold: 85% (for distinction)
├── All Lessons Completed: Required
└── Time Requirement Met: Required
```

**Important**: [Award different certificates](https://ldx.design/award-certificate-completion-multiple-courses/) based on score:
- **85-100%**: "SST Worker Certificate - Honors"
- **80-84%**: "SST Worker Certificate"
- **Below 80%**: No certificate, must retake

### Certificate Shortcodes for SST

Use [LearnDash certificate shortcodes](https://www.learndash.com/support/docs/core/certificates/certificate-shortcodes/) for compliance:

```html
<h1>NYC DOB Site Safety Training Certificate</h1>

<p><strong>Student Name:</strong> [ld_profile_name]</p>
<p><strong>Course:</strong> [course_title]</p>
<p><strong>Completion Date:</strong> [courseinfo show="completed_on" format="F j, Y"]</p>
<p><strong>Training Hours:</strong> 30 Hours</p>
<p><strong>Final Score:</strong> [quizinfo show="score"]%</p>
<p><strong>Certificate ID:</strong> SST-[user_id]-[course_id]-[timestamp]</p>

<p><strong>Training Provider:</strong><br>
Predictive Safety Training<br>
NYC DOB Provider ID: [Your Provider ID]<br>
Website: https://sst.nyc</p>

<p>This certificate verifies completion of NYC DOB-approved Site Safety Training
in accordance with Local Law 196 of 2017.</p>

<p><em>Valid for 5 years from date of issuance.</em></p>
```

### Certificate Delivery Methods

**Automatic Delivery** (Recommended):
1. Email certificate immediately upon course completion
2. Make available in student dashboard
3. Store in WordPress media library
4. Generate unique, verifiable PDF

**Manual Approval** (For essay-based supervisor courses):
1. Hold certificate until essays graded
2. Notify student when certificate available
3. Send via email with congratulations message

---

## Student Enrollment & Progression

### Enrollment Methods

**WooCommerce Integration** (Current Setup):
```
1. Student purchases course via WooCommerce
2. learndash-woocommerce plugin auto-enrolls
3. Student receives access confirmation email
4. Course appears in student dashboard
```

**Group Enrollment** (For corporate clients):
```php
LearnDash Groups:
├── Create group: "ABC Construction Company"
├── Enroll all employees (bulk)
├── Assign group leader (company admin)
└── Group leader tracks team progress
```

### Prerequisite Configuration

**Renewal Courses Require Prerequisites**:

```php
"8 Hour SST Worker Renewal":
├── Prerequisite: Must have completed original "10 Hour Worker SST" OR
├── Upload previous SST card (manual verification)
└── Card expiration date must be within 12 months
```

**Course Progression Example**:
```
10 Hr Worker SST (Entry)
  ↓
[5 years or SST card expiring soon]
  ↓
8 Hour SST Worker Renewal
  ↓
[Optional upgrade path]
  ↓
22 Hour SST Supervisor Upgrade
  ↓
32 Hour SST Supervisor (Complete certification)
```

### Access Control & Duration

**Course Access Settings**:
```php
Access Settings:
├── Access Mode: Buy Now (WooCommerce)
├── Course Duration: 180 days (6 months to complete)
├── Expiration: Send reminder at 30 days before expiration
└── Re-enrollment: Allow repurchase for refresher
```

---

## MCP Server Integration

### Available MCP Tools for LearnDash Management

Your WordPress MCP server provides these LearnDash-specific tools:

#### Course Management
```python
# Create new SST course
ld_create_course(
    title="10 Hour Online SST",
    description="NYC DOB-approved 10-hour worker course",
    price=150.00,
    course_materials="OSHA construction safety fundamentals"
)

# Update existing course
ld_update_course(
    course_id=4974,
    certificate_threshold=85,
    course_duration=180  # days
)

# List all courses with enrollment counts
ld_list_courses(status="publish")
```

#### Lesson & Quiz Creation
```python
# Create lesson in course
ld_create_lesson(
    course_id=4974,
    title="Fall Protection Systems",
    content="<video>...</video><p>Content...</p>",
    duration="60 minutes"
)

# Add quiz to lesson
ld_create_quiz(
    course_id=4974,
    lesson_id=5100,
    quiz_title="Fall Protection Assessment",
    passing_percentage=80,
    questions=[
        {
            "type": "multiple_choice",
            "question": "At what height are guardrails required?",
            "answers": ["4 feet", "6 feet", "8 feet", "10 feet"],
            "correct": 1,
            "points": 10
        }
    ]
)
```

#### Student Enrollment & Management
```python
# Enroll student in course
ld_enroll_user(
    user_id=32,
    course_id=4974,
    send_email=True
)

# Create group for corporate client
ld_create_group(
    name="ABC Construction - Nov 2025 Cohort",
    course_ids=[4974, 4425],
    leader_id=20  # Company admin
)

# Tag enrolled students in Mailchimp
mc_tag_course_student(
    user_email="student@example.com",
    course_name="32 Hour SST Supervisor",
    tags=["SST-Supervisor", "2025-Cohort"]
)
```

#### Reporting & Compliance
```python
# Get course progress for compliance audit
wp_db_query("""
    SELECT u.user_email, u.display_name,
           um.meta_value as completion_date,
           um2.meta_value as quiz_score
    FROM wp_users u
    JOIN wp_usermeta um ON um.user_id = u.ID
        AND um.meta_key = 'course_4974_completed'
    JOIN wp_usermeta um2 ON um2.user_id = u.ID
        AND um2.meta_key = 'course_4974_final_score'
    WHERE um.meta_value IS NOT NULL
""")

# Export student certificates for records
# (Use wp-cli or custom MCP tool to batch download)
```

### Automated Workflows with MCP

**Example: New Student Onboarding**:
```python
# When student purchases course via WooCommerce:
# 1. Auto-enrollment (handled by plugin)
# 2. Send welcome email with course access
# 3. Add to Mailchimp list
# 4. Tag in CRM
# 5. Schedule reminder emails

mc_add_subscriber(
    email=student_email,
    list_id="sst_students",
    merge_fields={
        "FNAME": first_name,
        "COURSE": "32 Hour Supervisor SST",
        "ENROLLED": enrollment_date
    }
)

mc_create_campaign(
    type="automated",
    trigger="course_enrollment",
    emails=[
        {"day": 0, "subject": "Welcome to SST Training!"},
        {"day": 7, "subject": "How's your progress?"},
        {"day": 30, "subject": "Don't forget to complete!"},
        {"day": 150, "subject": "Final reminder - 30 days left!"}
    ]
)
```

**Example: Certificate Generation & Delivery**:
```python
# When student completes course with 85%+:
# 1. Generate certificate PDF
# 2. Email certificate to student
# 3. Add to student records
# 4. Notify DOB (if required)
# 5. Send to Mailchimp for graduation announcement

wp_update_post(
    post_id=certificate_id,
    post_status="publish",
    meta={
        "certificate_issued_date": datetime.now(),
        "certificate_score": quiz_score,
        "certificate_unique_id": f"SST-{user_id}-{course_id}-{timestamp}"
    }
)
```

---

## Reporting & Compliance Tracking

### Required Reports for DOB Compliance

**Student Completion Report**:
- Student name and contact information
- Course title and completion date
- Total training hours completed
- Quiz scores and passing status
- Certificate ID and issue date

**Access via MCP**:
```bash
# Via wp-cli through MCP server
wp learndash report --course_id=4974 --format=csv > sst_completions.csv
```

### LearnDash Reporting Limitations

From research: [LearnDash only reports on course-level progress](https://profilelearning.com/implementing-the-learndash-lms-wordpress-plug-in-course-structure/), not lesson/topic level by default.

**Solutions**:
1. **Use LearnDash Reports Plugin** (Pro Add-on)
2. **Custom database queries** via MCP server
3. **Export via wp-cli** for Excel analysis
4. **Integrate with Google Sheets** for real-time dashboards

### Compliance Data Retention

**NYC DOB Requirements**:
- Keep training records for **minimum 3 years**
- Store in secure, backed-up location
- Make available for DOB audits upon request

**WordPress Backup Strategy**:
```bash
# Automated daily backups via MCP server
wp db export /backups/sst-$(date +%Y%m%d).sql
wp media export /backups/certificates/$(date +%Y%m%d)/
```

---

## Technical Implementation

### Required Plugins

**Core Stack**:
- [LearnDash LMS](https://www.learndash.com/) v4.25.6+
- [LearnDash WooCommerce](https://www.learndash.com/support/docs/add-ons/woocommerce/) v2.0.2+
- [WooCommerce](https://woocommerce.com/) v10.3.5+
- [Elementor](https://elementor.com/) (for landing pages)
- [WordPress MCP Plugin](https://github.com/Automattic/wordpress-mcp) v0.2.4+

**Recommended Add-ons**:
- LearnDash Reports Pro (detailed analytics)
- LearnDash Notifications (student engagement)
- LearnDash Certificates (advanced customization)
- Mailchimp for WordPress (email marketing)

### Performance Optimization

**For video-heavy SST courses**:
- Use **LiteSpeed Cache** (already installed)
- Enable **lazy loading** for videos
- Store videos on **Vimeo/YouTube** (not WordPress)
- Optimize images with **Hostinger Image Optimization**

**Database Optimization**:
```bash
# Clean up LearnDash transients weekly
wp transient delete --all
wp learndash-data upgrade
wp cache flush
```

### Security Best Practices

**Protect Student Data**:
- Use **SSL/HTTPS** (required)
- Enable **Two-Factor Authentication** for admins
- Regular **security audits** with Wordfence
- Implement **GDPR compliance** for EU students

**Access Control**:
- Limit admin access to trusted staff only
- Use **role-based permissions** (LearnDash Group Leaders)
- Enable **activity logging** (Aryo Activity Log plugin)

### MCP Server Configuration

**For Production Use**:
```json
{
  "mcpServers": {
    "sst-production": {
      "command": "python",
      "args": ["-m", "src.server"],
      "cwd": "/path/to/wordpress-mcp-server",
      "env": {
        "WP_SITE_URL": "https://sst.nyc",
        "WP_REMOTE_PATH": "/var/www/sst.nyc/public_html",
        "WP_SSH_KEY_PATH": "/home/user/.ssh/id_ed25519"
      }
    }
  }
}
```

---

## Quick Reference Checklist

### New SST Course Setup
- [ ] Create course with linear progression
- [ ] Add course sections for hour tracking
- [ ] Create 1-hour lessons with video + quiz
- [ ] Set passing score to 80% minimum
- [ ] Configure certificate with DOB requirements
- [ ] Link to WooCommerce product
- [ ] Set 180-day access duration
- [ ] Enable drip scheduling (optional)
- [ ] Test full student journey
- [ ] Verify certificate generation

### Student Enrollment Process
- [ ] Student purchases via WooCommerce
- [ ] Auto-enrollment triggered
- [ ] Welcome email sent
- [ ] Added to Mailchimp list
- [ ] Access granted to course
- [ ] Reminder emails scheduled
- [ ] Progress tracked in dashboard

### Compliance Audit Preparation
- [ ] Export student completion report
- [ ] Verify all certificates issued
- [ ] Check quiz scores (80%+ required)
- [ ] Confirm all students completed required hours
- [ ] Backup all training records
- [ ] Prepare DOB provider documentation

---

## Resources & References

### Official Documentation
- [LearnDash Documentation](https://www.learndash.com/support/docs/)
- [NYC DOB SST Information](https://www.nyc.gov/site/buildings/safety/sst-worker-information.page)
- [Local Law 196 Requirements](https://www.oshaeducationcenter.com/new-york/sst/local-law-196/)
- [OSHA Training Standards](https://www.osha.gov/training)

### Best Practices Guides
- [LearnDash Course Creation Guide](https://lmscrafter.com/learndash-course-creation-step-by-step-guide/)
- [Maximize Retention with Quizzes](https://www.learndash.com/blog/maximize-retention-learndash-quizzes/)
- [Certificate Best Practices](https://ldx.design/award-certificate-completion-multiple-courses/)
- [Online Course Quiz Best Practices](https://lmsninjas.com/online-course-quizzes/)

### NYC DOB SST Providers
- [360 Training - NYC SST](https://www.360training.com/osha-campus/osha-training/new-york-city-sst)
- [OSHA Education Center](https://www.oshaeducationcenter.com/new-york/sst/)
- [US OSHA Training](https://www.usfosha.com/new-york/local-law-196/)

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Nov 2025 | Initial release - LearnDash best practices for NYC DOB SST training |

---

**Questions or suggestions?** Contact the Predictive Safety tech team at tech@sst.nyc

**MCP Server Issues?** See `wordpress-mcp-server/README.md` or check [WordPress MCP Documentation](https://github.com/Automattic/wordpress-mcp)
