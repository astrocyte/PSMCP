# LearnDash & WooCommerce Management Guide

Complete guide to managing your LMS and e-commerce with the WordPress MCP Server.

## 🎓 LearnDash LMS Management

### Course Management

**Create a Course**
```
Create a new course titled "Introduction to Python"
Price it at $99
Status: draft
```

Tool: `ld_create_course`
- Creates course as draft (safe default)
- Sets pricing
- Returns course ID for further operations

**Update a Course**
```
Update course ID 123
Change title to "Advanced Python Programming"
Update price to $149
```

Tool: `ld_update_course`
- Modify existing course details
- Update pricing
- Change content/description

**List All Courses**
```
Show me all published courses
```

Tool: `ld_list_courses`
- View all courses (any status)
- Filter by status (publish, draft, private)
- Get course IDs for further operations

### Lesson Management

**Create a Lesson**
```
Create lesson "Variables and Data Types" for course ID 123
Content: [lesson content here]
Set as lesson #1
```

Tool: `ld_create_lesson`
- Automatically associates with course
- Sets lesson order
- Creates hierarchical structure

**Update a Lesson**
```
Rename lesson ID 456 to "Advanced Variables"
Reorder to position 3
```

Tool: `ld_update_lesson`
- Modify lesson title/content
- Reorder lessons within course
- Update without losing student progress

**Workflow Example: Build Full Course**
```
1. Create course "Python Basics" → Get course ID 100
2. Create lesson "Introduction" for course 100, order 1
3. Create lesson "Variables" for course 100, order 2
4. Create lesson "Functions" for course 100, order 3
5. Create quiz for course 100
6. Publish course when ready
```

### Quiz Management

**Create a Quiz**
```
Create quiz "Python Fundamentals Test" for course ID 123
Set passing score to 80%
Associate with lesson ID 456
```

Tool: `ld_create_quiz`
- Links to course and optional lesson
- Sets passing percentage
- Can attach certificate

**Add Quiz Questions**
```
Add question to quiz ID 789:
"What is a variable in Python?"
Type: single choice
Points: 1
```

Tool: `ld_add_quiz_question`

**Question Types:**
- `single` - Single choice (radio buttons)
- `multiple` - Multiple choice (checkboxes)
- `free_answer` - Short text answer
- `essay` - Long form essay

**Building a Complete Quiz:**
```
1. Create quiz → Get quiz ID 789
2. Add question 1 (single choice)
3. Add question 2 (multiple choice)
4. Add question 3 (essay)
5. Review and test
```

### Student Enrollment

**Enroll a User**
```
Enroll user ID 42 in course ID 123
```

Tool: `ld_enroll_user`
- Grants course access
- Starts progress tracking
- Can enroll manually or via purchase

**Use Cases:**
- Comp accounts for partners/reviewers
- Bulk enrollment from spreadsheet
- Manual enrollment after offline payment
- Transfer from another LMS

### Group Management

**Create a Group**
```
Create group "Corporate Training Batch 2024"
Add courses: [123, 456, 789]
```

Tool: `ld_create_group`
- Organize students by cohort/company
- Assign multiple courses at once
- Manage group leaders

**Add User to Group**
```
Add user ID 42 to group ID 10
```

Automatically enrolls in all group courses!

## 🛒 WooCommerce E-Commerce

### Product Management

**Create a Product**
```
Create product "Python Course Access"
Price: $99
SKU: PYTHON-101
Link to course ID 123
```

Tool: `wc_create_product`
- Creates WooCommerce product
- Links to LearnDash course
- Sets up automatic enrollment on purchase

**Update Product Pricing**
```
Update product ID 456
Set sale price to $79 (was $99)
```

Tool: `wc_update_product`
- Change pricing
- Update descriptions
- Modify SKU/inventory

**List All Products**
```
Show all course products
Search for "Python"
```

Tool: `wc_list_products`
- View all products
- Search by keyword
- Filter by category

### Order Management

**View Orders**
```
Show recent orders
Filter by status: completed
```

Tool: `wc_list_orders`

**Order Statuses:**
- `pending` - Awaiting payment
- `processing` - Payment received
- `completed` - Fulfilled
- `on-hold` - Awaiting confirmation
- `cancelled` - Customer cancelled
- `refunded` - Money returned
- `failed` - Payment failed

**Customer Orders**
```
Show all orders for customer ID 42
```

Great for support tickets!

### Coupon Management

**Create a Discount Coupon**
```
Create coupon code "SUMMER2024"
Discount type: percent
Amount: 20 (20% off)
Usage limit: 50 customers
```

Tool: `wc_create_coupon`

**Coupon Types:**
- `percent` - Percentage discount (e.g., 20%)
- `fixed_cart` - Fixed amount off cart (e.g., $10)
- `fixed_product` - Fixed amount off specific products

**Marketing Campaign Example:**
```
1. Create coupon "LAUNCH50" - 50% off
2. Set expiry date: 7 days from now
3. Limit to 100 uses
4. Apply only to new course products
5. Send email blast with code
```

### Sales Reports

**Get Sales Data**
```
Show sales report for this month
```

Tool: `wc_get_sales_report`

**Available Periods:**
- `week` - Last 7 days
- `month` - Last 30 days
- `year` - Last 12 months

**Metrics Included:**
- Total sales revenue
- Number of orders
- Number of items sold
- Average order value

## 🔗 LearnDash + WooCommerce Integration

### Sell Courses Workflow

**Complete Setup:**

1. **Create the Course**
   ```
   Create course "Advanced WordPress Development"
   Add 10 lessons with content
   Add 2 quizzes
   Set as draft initially
   ```

2. **Create WooCommerce Product**
   ```
   Create product "Advanced WP Course Access"
   Price: $299
   Link to course ID from step 1
   Status: publish
   ```

3. **Test Purchase Flow**
   ```
   Make test purchase
   Verify auto-enrollment works
   Check course appears in student dashboard
   ```

4. **Launch Course**
   ```
   Update course status to publish
   Create launch coupon 25% off
   Monitor sales and enrollments
   ```

### Manage Existing Course Sales

**Update Course + Product Together:**
```
1. Update course content (new lessons)
2. Update product description to mention new content
3. Create "update announcement" coupon for existing students
4. Monitor enrollments
```

### Refund & Access Management

**Handle Refund:**
```
1. Check order ID 789 status
2. Update to "refunded" status
3. Manually unenroll user from course ID 123
```

**Grant Lifetime Access:**
```
1. Create "Lifetime Access VIP" product
2. Price: $999
3. Link to all current + future courses
4. Manual enrollment for existing course IDs
```

## 📋 Common Workflows

### Workflow 1: Launch New Course

```
Step 1: Build Course Structure
- Create course (draft)
- Add all lessons in order
- Create quizzes
- Add quiz questions
- Set passing scores

Step 2: Create Product
- Create WooCommerce product
- Link to course
- Set pricing
- Add product images/descriptions

Step 3: Marketing
- Create launch coupon (early bird)
- Set expiry for urgency
- Prepare email campaign

Step 4: Launch
- Publish course
- Publish product
- Send marketing emails
- Monitor sales dashboard
```

### Workflow 2: Bulk Student Import

```
1. Get spreadsheet of student data
2. For each student:
   - Create WordPress user (if needed)
   - Enroll in appropriate courses
   - Add to relevant groups
3. Send welcome emails
4. Verify all enrollments successful
```

### Workflow 3: Course Update Campaign

```
1. Update course with new lessons
2. Create "Welcome Back" coupon for alumni
3. Update product description
4. Email past students about updates
5. Track re-enrollments
```

### Workflow 4: Corporate Training

```
1. Create group "Acme Corp Training 2024"
2. Create custom course package
3. Create bulk-discount product
4. Add all employees to group
5. Monitor group progress
6. Generate completion reports
```

### Workflow 5: Seasonal Sale

```
1. List all course products
2. Create sale coupons:
   - SPRING25 (25% off)
   - SPRING40 (40% off for 2+ courses)
3. Set expiry dates
4. Update product prices (optional)
5. Track coupon usage
6. Monitor sales increase
```

## 🎯 Best Practices

### Course Management

**Naming Conventions:**
- Courses: "Subject - Level" (e.g., "Python - Beginner")
- Lessons: Number + Topic (e.g., "1. Introduction to Variables")
- Quizzes: "Topic Assessment" (e.g., "Variables Assessment")

**Content Organization:**
- 5-10 lessons per course (optimal)
- Quiz after every 2-3 lessons
- Final comprehensive quiz
- Certificate upon completion

**Pricing Strategy:**
- Tier pricing (Basic, Premium, Pro)
- Bundle discounts for multiple courses
- Subscription options via WooCommerce
- Early bird / launch discounts

### Product Management

**SKU Format:**
- COURSE-LEVEL-VERSION (e.g., PYTHON-BEG-V1)
- Makes inventory tracking easier
- Clear for reporting

**Product Descriptions:**
- What students will learn (outcomes)
- Course duration / lesson count
- Prerequisites
- Certificate included
- Instructor bio

**Pricing Psychology:**
- $97 instead of $100
- Show regular price + sale price
- Display value ($497 value!)
- Urgency (offer ends soon)

### Customer Support

**Common Issues:**

1. **Purchase but no access**
   - Check order status (must be "completed")
   - Verify course linkage in product
   - Manual enrollment if needed

2. **Lost progress**
   - Progress stored in user meta
   - Usually can be recovered
   - Check for duplicate accounts

3. **Certificate not generating**
   - Verify quiz passing scores
   - Check certificate settings
   - May need manual award

## 🔒 Security Considerations

### Safe Operations

**Always Safe:**
- Creating drafts
- Listing/viewing data
- Running reports
- Testing with test accounts

**Use Caution:**
- Publishing courses (visible to all)
- Changing prices (affects active sales)
- Deleting content (may affect students)
- Bulk enrollments (verify list first)

**Best Practices:**
- Test on staging first
- Use drafts before publishing
- Keep backups before bulk operations
- Verify course IDs before linking products
- Double-check pricing before sales

### User Permissions

Make sure WordPress API user has:
- Edit courses capability
- Manage shop (WooCommerce)
- Edit users (for enrollments)
- View orders

## 📊 Reporting & Analytics

### Available Data

**Course Analytics:**
```
- Total students enrolled
- Active students
- Completion rates
- Average quiz scores
- Time to complete
```

**Sales Analytics:**
```
- Total revenue
- Sales by product
- Coupon usage
- Customer lifetime value
- Top-selling courses
```

**Combined Insights:**
```
- Revenue per student
- Course popularity
- Refund rates
- Enrollment trends
- Seasonal patterns
```

## 🚀 Advanced Use Cases

### Membership Site

```
1. Create "Monthly Membership" product (subscription)
2. Create "Members Only" group
3. Add all courses to group
4. Link subscription to group enrollment
5. Automatic access on payment
6. Automatic removal on cancellation
```

### Course Bundles

```
1. Create 3 related courses
2. Create 3 individual products ($99 each)
3. Create bundle product ($249)
4. Add all 3 course links to bundle
5. Highlight $48 savings
```

### Drip Content

```
1. Create course structure
2. Set lesson availability:
   - Lesson 1: Immediately
   - Lesson 2: 7 days after enrollment
   - Lesson 3: 14 days after enrollment
3. Configure via LearnDash settings
4. Maintains engagement over time
```

### Certification Programs

```
1. Create prerequisite chain:
   - Course A → Course B → Course C
2. Award certificate only after Course C
3. Create "Full Certification" product
4. Bundle all 3 courses
5. Market as complete program
```

## 🆘 Troubleshooting

### "Course not found"
- Verify course ID is correct
- Check course hasn't been deleted
- Ensure status isn't "trash"

### "Product not linking to course"
- Check `_related_course` meta field
- Verify course ID is integer, not string
- Ensure course is published

### "User not enrolling on purchase"
- Check WooCommerce order status (must be "completed")
- Verify product has course linkage
- Check for LearnDash + WooCommerce integration plugin

### "Quiz questions not saving"
- LearnDash uses complex serialized format
- May need Pro version of plugin
- Consider using WordPress admin for quiz building

## 📚 Next Steps

After mastering these tools:
1. Automate course creation from templates
2. Build custom enrollment workflows
3. Create advanced reporting dashboards
4. Integrate with email marketing
5. Set up affiliate programs

Your LMS is now fully manageable via AI! 🎉
