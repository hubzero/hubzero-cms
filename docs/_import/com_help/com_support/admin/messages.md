<!--
status: imported
source: core/components/com_support/admin/help/en-GB/messages.phtml
imported: 2026-09-09
-->
# Support: Message Templates

## Overview

To aid in responding to support ticket submissions, you may create custom message templates for frequently used responses. Example responses include a "Ticket resolved" message sent upon closing a ticket or a "Request for more information" message.

## Adding/Editing

Messages may also includes placeholders where information that changes can be specified. For instance your message template may include references to the specific ticket ID number or site name. A "Ticket resolved" template could look like this:

> Thank you for using the {sitename}, and for reporting this problem. We believe that your issue (ticket #{ticket#} in our system) has been resolved. If you continue to have problems please let us know. You may reopen a closed issue at any time by following the link at the end of this message and adding a comment to the ticket.
>
> Thank you for helping us to improve {sitename}!
> --the {sitename} team

Here we have two placeholders: `{ticket#}` and `{sitename}`. The placeholders are replaced with their current value at the time of composition. When chosen from the select box under "Comments" of the response form, the comments text box will be filled with the following:

> Thank you for using the YourHUB, and for reporting this problem. We believe that your issue (ticket #123 in our system) has been resolved. If you continue to have problems please let us know. You may reopen a closed issue at any time by following the link at the end of this message and adding a comment to the ticket.
>
> Thank you for helping us to improve YourHUB!
> --the YourHUB team
