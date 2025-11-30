"""Mailchimp email marketing integration."""

import requests
from typing import Optional, Literal
from .config import WordPressConfig


class MailchimpManager:
    """Manage Mailchimp audiences, campaigns, and automation."""

    def __init__(self, api_key: str, server_prefix: str):
        """
        Initialize Mailchimp client.

        Args:
            api_key: Mailchimp API key
            server_prefix: Server prefix from API key (e.g., 'us1', 'us19')
        """
        self.api_key = api_key
        self.server_prefix = server_prefix
        self.base_url = f"https://{server_prefix}.api.mailchimp.com/3.0"
        self.session = requests.Session()
        self.session.auth = ("anystring", api_key)

    def _request(self, method: str, endpoint: str, **kwargs) -> dict:
        """Make API request to Mailchimp."""
        url = f"{self.base_url}/{endpoint.lstrip('/')}"
        response = self.session.request(method, url, **kwargs)
        response.raise_for_status()
        return response.json() if response.content else {}

    # ==================== AUDIENCE MANAGEMENT ====================

    def list_audiences(self) -> list[dict]:
        """
        List all Mailchimp audiences (lists).

        Returns:
            List of audiences with member counts
        """
        result = self._request("GET", "/lists")
        return result.get("lists", [])

    def get_audience(self, list_id: str) -> dict:
        """Get details about a specific audience."""
        return self._request("GET", f"/lists/{list_id}")

    def create_audience(
        self,
        name: str,
        company: str,
        address1: str,
        city: str,
        state: str,
        zip: str,
        country: str,
        from_name: str,
        from_email: str,
        subject: str,
        permission_reminder: str,
    ) -> dict:
        """
        Create a new Mailchimp audience.

        Args:
            name: List name
            company: Company name
            address1: Street address
            city: City
            state: State/province
            zip: Postal code
            country: Country code (e.g., 'US')
            from_name: Default from name for campaigns
            from_email: Default from email
            subject: Default subject line
            permission_reminder: Reminder of how they signed up

        Returns:
            Created audience data
        """
        data = {
            "name": name,
            "contact": {
                "company": company,
                "address1": address1,
                "city": city,
                "state": state,
                "zip": zip,
                "country": country,
            },
            "permission_reminder": permission_reminder,
            "campaign_defaults": {
                "from_name": from_name,
                "from_email": from_email,
                "subject": subject,
                "language": "en",
            },
            "email_type_option": True,
        }
        return self._request("POST", "/lists", json=data)

    # ==================== MEMBER MANAGEMENT ====================

    def add_member(
        self,
        list_id: str,
        email: str,
        status: Literal["subscribed", "pending", "unsubscribed"] = "subscribed",
        merge_fields: Optional[dict] = None,
        tags: Optional[list[str]] = None,
    ) -> dict:
        """
        Add or update a member in an audience.

        Args:
            list_id: Audience ID
            email: Email address
            status: Subscription status
            merge_fields: Custom fields (e.g., {"FNAME": "John", "LNAME": "Doe"})
            tags: List of tags to apply

        Returns:
            Member data
        """
        data = {
            "email_address": email,
            "status": status,
        }

        if merge_fields:
            data["merge_fields"] = merge_fields

        if tags:
            data["tags"] = tags

        return self._request(
            "POST",
            f"/lists/{list_id}/members",
            json=data
        )

    def update_member(
        self,
        list_id: str,
        email: str,
        merge_fields: Optional[dict] = None,
        tags: Optional[list[str]] = None,
    ) -> dict:
        """Update existing member information."""
        import hashlib
        subscriber_hash = hashlib.md5(email.lower().encode()).hexdigest()

        data = {}
        if merge_fields:
            data["merge_fields"] = merge_fields
        if tags:
            data["tags"] = tags

        return self._request(
            "PATCH",
            f"/lists/{list_id}/members/{subscriber_hash}",
            json=data
        )

    def get_member(self, list_id: str, email: str) -> dict:
        """Get member details."""
        import hashlib
        subscriber_hash = hashlib.md5(email.lower().encode()).hexdigest()
        return self._request("GET", f"/lists/{list_id}/members/{subscriber_hash}")

    def list_members(
        self,
        list_id: str,
        status: Optional[str] = None,
        count: int = 50,
        offset: int = 0,
    ) -> list[dict]:
        """
        List members in an audience.

        Args:
            list_id: Audience ID
            status: Filter by status (subscribed, unsubscribed, cleaned, pending)
            count: Number of results
            offset: Offset for pagination

        Returns:
            List of members
        """
        params = {"count": count, "offset": offset}
        if status:
            params["status"] = status

        result = self._request("GET", f"/lists/{list_id}/members", params=params)
        return result.get("members", [])

    def unsubscribe_member(self, list_id: str, email: str) -> dict:
        """Unsubscribe a member from an audience."""
        import hashlib
        subscriber_hash = hashlib.md5(email.lower().encode()).hexdigest()

        return self._request(
            "PATCH",
            f"/lists/{list_id}/members/{subscriber_hash}",
            json={"status": "unsubscribed"}
        )

    # ==================== TAG MANAGEMENT ====================

    def add_tags(self, list_id: str, email: str, tags: list[str]) -> dict:
        """
        Add tags to a member.

        Args:
            list_id: Audience ID
            email: Member email
            tags: List of tag names to add

        Returns:
            Updated member data
        """
        import hashlib
        subscriber_hash = hashlib.md5(email.lower().encode()).hexdigest()

        data = {
            "tags": [{"name": tag, "status": "active"} for tag in tags]
        }

        return self._request(
            "POST",
            f"/lists/{list_id}/members/{subscriber_hash}/tags",
            json=data
        )

    def remove_tags(self, list_id: str, email: str, tags: list[str]) -> dict:
        """Remove tags from a member."""
        import hashlib
        subscriber_hash = hashlib.md5(email.lower().encode()).hexdigest()

        data = {
            "tags": [{"name": tag, "status": "inactive"} for tag in tags]
        }

        return self._request(
            "POST",
            f"/lists/{list_id}/members/{subscriber_hash}/tags",
            json=data
        )

    # ==================== CAMPAIGN MANAGEMENT ====================

    def create_campaign(
        self,
        list_id: str,
        subject: str,
        from_name: str,
        reply_to: str,
        title: Optional[str] = None,
    ) -> dict:
        """
        Create an email campaign.

        Args:
            list_id: Audience to send to
            subject: Email subject line
            from_name: Sender name
            reply_to: Reply-to email
            title: Internal campaign name

        Returns:
            Campaign data with campaign ID
        """
        data = {
            "type": "regular",
            "recipients": {"list_id": list_id},
            "settings": {
                "subject_line": subject,
                "from_name": from_name,
                "reply_to": reply_to,
                "title": title or subject,
            },
        }

        return self._request("POST", "/campaigns", json=data)

    def update_campaign_content(
        self,
        campaign_id: str,
        html_content: Optional[str] = None,
        plain_text: Optional[str] = None,
    ) -> dict:
        """
        Set campaign email content.

        Args:
            campaign_id: Campaign ID
            html_content: HTML email body
            plain_text: Plain text version

        Returns:
            Updated content data
        """
        data = {}
        if html_content:
            data["html"] = html_content
        if plain_text:
            data["plain_text"] = plain_text

        return self._request(
            "PUT",
            f"/campaigns/{campaign_id}/content",
            json=data
        )

    def send_campaign(self, campaign_id: str) -> dict:
        """
        Send a campaign immediately.

        Args:
            campaign_id: Campaign to send

        Returns:
            Send confirmation
        """
        return self._request("POST", f"/campaigns/{campaign_id}/actions/send")

    def schedule_campaign(self, campaign_id: str, schedule_time: str) -> dict:
        """
        Schedule a campaign for later.

        Args:
            campaign_id: Campaign ID
            schedule_time: ISO 8601 datetime (e.g., "2024-12-25T10:00:00Z")

        Returns:
            Schedule confirmation
        """
        data = {"schedule_time": schedule_time}
        return self._request(
            "POST",
            f"/campaigns/{campaign_id}/actions/schedule",
            json=data
        )

    def list_campaigns(
        self,
        status: Optional[str] = None,
        count: int = 50,
    ) -> list[dict]:
        """
        List campaigns.

        Args:
            status: Filter by status (sent, draft, scheduled, etc.)
            count: Number of results

        Returns:
            List of campaigns
        """
        params = {"count": count}
        if status:
            params["status"] = status

        result = self._request("GET", "/campaigns", params=params)
        return result.get("campaigns", [])

    def get_campaign_report(self, campaign_id: str) -> dict:
        """
        Get campaign performance report.

        Args:
            campaign_id: Campaign ID

        Returns:
            Report with opens, clicks, bounces, etc.
        """
        return self._request("GET", f"/reports/{campaign_id}")

    # ==================== AUTOMATION ====================

    def list_automations(self) -> list[dict]:
        """List all automation workflows."""
        result = self._request("GET", "/automations")
        return result.get("automations", [])

    def get_automation(self, workflow_id: str) -> dict:
        """Get details about an automation workflow."""
        return self._request("GET", f"/automations/{workflow_id}")

    def pause_automation(self, workflow_id: str) -> dict:
        """Pause an automation workflow."""
        return self._request("POST", f"/automations/{workflow_id}/actions/pause")

    def start_automation(self, workflow_id: str) -> dict:
        """Start an automation workflow."""
        return self._request("POST", f"/automations/{workflow_id}/actions/start")

    # ==================== SEGMENTS ====================

    def create_segment(
        self,
        list_id: str,
        name: str,
        conditions: list[dict],
    ) -> dict:
        """
        Create a segment (filtered subset of audience).

        Args:
            list_id: Audience ID
            name: Segment name
            conditions: Match conditions (complex structure)

        Returns:
            Segment data

        Example conditions:
            [
                {
                    "condition_type": "TextMerge",
                    "field": "FNAME",
                    "op": "is",
                    "value": "John"
                }
            ]
        """
        data = {
            "name": name,
            "options": {
                "match": "all",
                "conditions": conditions,
            },
        }

        return self._request("POST", f"/lists/{list_id}/segments", json=data)

    def list_segments(self, list_id: str) -> list[dict]:
        """List all segments in an audience."""
        result = self._request("GET", f"/lists/{list_id}/segments")
        return result.get("segments", [])

    # ==================== WORDPRESS INTEGRATION ====================

    def sync_woocommerce_customer(
        self,
        list_id: str,
        customer_data: dict,
        tags: Optional[list[str]] = None,
    ) -> dict:
        """
        Sync WooCommerce customer to Mailchimp.

        Args:
            list_id: Mailchimp audience ID
            customer_data: WooCommerce customer object
            tags: Additional tags to apply

        Returns:
            Mailchimp member data
        """
        email = customer_data.get("email")
        billing = customer_data.get("billing", {})

        merge_fields = {
            "FNAME": billing.get("first_name", ""),
            "LNAME": billing.get("last_name", ""),
        }

        default_tags = ["WooCommerce Customer"]
        if tags:
            default_tags.extend(tags)

        return self.add_member(
            list_id=list_id,
            email=email,
            status="subscribed",
            merge_fields=merge_fields,
            tags=default_tags,
        )

    def sync_course_enrollment(
        self,
        list_id: str,
        user_email: str,
        course_name: str,
        course_id: int,
    ) -> dict:
        """
        Tag user with course enrollment.

        Args:
            list_id: Mailchimp audience
            user_email: Student email
            course_name: Course title
            course_id: Course ID

        Returns:
            Updated member data
        """
        tags = [
            "Course Student",
            f"Course: {course_name}",
            f"Course ID: {course_id}",
        ]

        # Add/update member
        try:
            self.add_member(list_id, user_email, status="subscribed")
        except:
            pass  # May already exist

        # Add tags
        return self.add_tags(list_id, user_email, tags)

    def create_course_launch_campaign(
        self,
        list_id: str,
        course_name: str,
        course_url: str,
        price: float,
        launch_date: str,
    ) -> dict:
        """
        Create a course launch campaign.

        Args:
            list_id: Audience to send to
            course_name: Course title
            course_url: Link to course page
            price: Course price
            launch_date: Launch date string

        Returns:
            Campaign data
        """
        subject = f"New Course Alert: {course_name} Now Available!"

        campaign = self.create_campaign(
            list_id=list_id,
            subject=subject,
            from_name="SST.NYC",
            reply_to="support@sst.nyc",
            title=f"Launch: {course_name}",
        )

        # Create HTML content
        html = f"""
        <html>
        <body>
            <h1>Introducing: {course_name}</h1>
            <p>We're excited to announce our latest course!</p>
            <h2>What You'll Learn</h2>
            <p>This comprehensive course covers everything you need to know.</p>
            <p><strong>Price:</strong> ${price}</p>
            <p><strong>Available:</strong> {launch_date}</p>
            <p><a href="{course_url}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Enroll Now</a></p>
        </body>
        </html>
        """

        self.update_campaign_content(
            campaign_id=campaign["id"],
            html_content=html,
        )

        return campaign
