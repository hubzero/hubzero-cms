
## Reply functionality: single-click validation

### Use Case

"Reply" functionality allows registered Hub users to submit survey responses or update their preferences without first completing the primary hub authentication procedure.

This feature provides user authentication to a single specially-configured page via a hash of randomly-generated codes that are associated with the user's CMS record, the campaign, and the Hub. 
Each user must access the page using a URL containing their unique code, the page and campaign id, and their username. Compliant URLs can be generated, then sent to users via email.

This functionality is now part of the `com_newsletter` core component. It was originally found in custom component `com_reply`.

Related features:

All management of these features is found under the Hub's administrator interface. 

* Per-user randomly generated codes are managed under Member password management.
* Per-campaign randomly generated codes are managed under the Newsletter component's Campaign tab.
    In order to use the feature, administrators must create at least one Campaign.
* The Hub's randomly generated code is managed under Global Configuration. 

### Install
1. Run the migrations e.g. via `muse migration run ...`

### Access
A registered user providing a URL with correct credentials (page id, campaign id, and proper code) will have access to the specified page.
Pages are not restricted on a by-user basis.

### Pages
One survey page is provided for use with this feature.
To use it, the Hub must have at least one Campaign defined, with a valid expiration date.

To add a new form-based page:
1. Create a view for the page in `site/views/pages`, using the provided view, `page2.php`, as an example.
1. The name of the view should be page<PAGEID>.php
1. Create a record in the table `jos_reply_pages` representing the page, with `jos_reply_pages`.`id` = PAGEID.

### Email Subscription Options

One email subscription option page is provided for use with this feature.
The Hub must also have at least one Campaign configured, with a valid expiration date.

To add a new email subscription option page:
1. Create or confirm name of the corresponding profile field
1. Add a record to `jos_email_subscriptions`
1. Create a view (content to appear beneath label) if desired 
1. Manually add a record to the table `jos_reply_pages` representing the page.
1. Set the email page ID in `secrets/page_code.php`, so that it is equal to the new `jos_reply_pages`.`id`. 

### Gotchas
* `jos_email_subscriptions.profile_field_name` must correspond with a `jos_user_profile_fields.name`
* `jos_user_profile_options` must be populated for the user profile fields. `jos_user_profile_options.field_id` must correspond with id from `jos_user_profile_fields.id`
* `jos_users` user codes are created automatically by the core Hubzero members plugin when users log in.
* Individual access codes are provided by the stored procedure `hash_access_code()`. It takes two arguments, `jos_campaign`.`id` and `jos_users`.`username`

### Component-Specific Tables
* `jos_reply_pages` - specifies the pages which provide prompts for user entry
* `jos_reply_replies` - stores user-submitted responses
* `jos_email_subscriptions` - specifies the email subscription options

### Access-Related Tables
* `jos_users` - contains a `secret` for each user
* `jos_campaign` - contains a `secret` for each campaign, with expiration date
* `jos_config` - the `value` column for `scope`='hub' and `key`='secret' contains the overall secret for the Hub

### Database integrations
The email subscription update functionality integrates with the core user profile data.
* `jos_user_profile_fields` - profile fields
* `jos_user_profile_options` - profile field response options
* `jos_user_profiles` - associates user data with profile fields

### Generating Access Codes

The stored procedure `hash_access_code` is used to generate and verify codes for page access. Codes are used in URLs as shown below. 
To obtain a code for a campaign and user, call the stored procedure from the SQL command line: 

`select hash_access_code(CAMPAIGNID, USERNAME);`

### Example URLs

The example URLs shown here validate Hub user access to the provided survey page and email-subscription option page.

Here:

* USERNAME is the user's `jos_user`.`username`
* CAMPAIGNID is `jos_campaign`.`id`
* HUBNAME is the name of the Hub
* PAGEID is the integer from the name of the page view in site/views/pages. The provided view is page2.php, giving PAGEID=2.

#### Pages URL format
`https://HUBNAME.org/newsletter/pages/PAGEID?campaign=CAMPAIGNID&user=USERNAME&code=CODE_FROM_hash_access_code`

#### Email subscription options URL format
`https://HUBNAME.org/newsletter/email-subscriptions?campaign=CAMPAIGNID&user=USERNAME&code=CODE_FROM_hash_access_code`
