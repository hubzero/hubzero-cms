<!--
status: imported
source: core/components/com_support/admin/help/en-GB/ticket.phtml
imported: 2026-09-09
-->
# Support: Ticket

## Overview

Here you will find a submitted ticket and all associated data, including comments, change and notification logs, and attachments.

## Changes

The original ticket data may not be modified in any way. Only associated or non-destructive changes may occur, such as changing status, severity, adding/removing tags, and assigning owner. All changes are tracked in a log.

## Status

- **Open**  
  An open issue. The ticket needs to be addressed in some fashion.
- **Awaiting user action**  
  This is for when you need more information from or the ticket submitter to perform a specific task (such as test that a fix is working for them). In short, further progress on the ticket is dependant upon the some course of action from the ticket submitter.
- **Closed**  
  The ticket is considered recolved. Here, a list of resolutions can be specified to better filter and generate stats by.

## Owner

The owner of a ticket is the *current* user assigned to perform some action or address the the issue presented by the ticket in some way. Ownership may change several times during the life of the ticket and is tracked in the change log. When assigned to a ticket as an owner, the ticket will appear in the user's "My assigned tickets" list.

## Group

Sometimes, a ticket may belong to a group of users rather than a specific person.

## Severity

- **Low**  
  Minor loss of application functionality, product feature requests, how-to questions. The issue consists of "how-to" questions including issues related to one or multiple modules and integration, installation and configuration inquiries, enhancement requests, or documentation questions.
  
  ### Qualifying Conditions
  
  - Problem does not have significant impact to the Customer or occurs in functionality that is not critical or frequently used (Example: Customer is having trouble with configuration in the Administrative interface).
    - There are no extenuating circumstances that would require this issue to be resolved outside of the normal software. (Example: Customer has general questions about functionality of a particular item. Customer does not understand X in documentation and would like clarification).
    - The problem causes little impact on your operations or a reasonable workaround for the problem has been implemented.
    - The problem results in minimal or no interruptions to normal operations.
    - Non-critical, Minor loss of application functionality or product Feature Request question (Example: Customer wants to know if a particular function is possible).
    - Minor infractions including documentation or cosmetic error not impacting production.
    - Functionality does not match documented specifications.
    - Service enhancements requests.
- **Normal**  
  Moderate loss of application functionality or performance resulting in multiple users impacted in their normal functions. Minor feature/product failure, convenient workaround exists/minor performance degradation/not impacting production.
  
  ### Qualifying Conditions
  
  - System is up and running, but the problem causes non-negligible impact. Workaround exists, but it is only temporary (Example: A cron job can be triggered manually but does not run when scheduled).
  - The software still functions in the Customer's business environment, but there are functional limitations that are not critical in the daily operation (Example: Customer is getting Resources created correctly but wants them to behave in a different manner.)
  - Does not prevent operation of a production system or there is some degradation in performance (Example: The issue affects only users from a specific location).
  - Moderate loss of application functionality or performance resulting in multiple users impacted in their normal functions (Examples: Some Super Groups experience performance issues due to size and complexity of the group).
  - Important to long-term productivity (Example: Customer cannot set up a particular group).
- **High**  
  Critical loss of application functionality or performance resulting in high number of users unable to perform their normal functions. Major feature/product failure; inconvenient workaround or no workaround exists. The program is usable but severely limited.
  
  ### Qualifying Conditions
  
  - Service interruptions to some, but not all, components (Examples: components not functional for some users but functional for others).
  - Time sensitive issue reported by Customer, which may adversely affect business impact, monitoring or productivity (Examples: Upcoming conferences, demos and training).
  - In a production system, important tasks cannot be performed, but the error does not impair essential operations (Example: Group approval and Registration Confirmation).
- **Critical**  
  Production application down or major malfunction resulting in a product inoperative condition. Users unable to reasonably perform their normal functions.
  
  ### Qualifying Conditions
  
  - Hub completely unusable
  - Upgrade to a newer version of Hubzero has failed to install.
  - No user can log into the web interface.

## Commenting

Comments are visible to anyone who has access to read this ticket, including the ticket submitter, unless the comment is marked **private**.

## Notifying others

You may send the comment to others by filling in username, user ID, or email addresses in the "Send message to" field. Any value entered here is *sticky*--that is, the value will be retained and the field pre-filled for the next comment. This makes it easy to keep the same set of people notified. All notifications are tracked in a log.

## Watching

When watching a ticket, you will be notified of any comments added or changes made. You may stop watching at any time.
