
## Reply Component

### Use Case
This component allows users to submit responses and update their preferences without first completing the primary hub authentication procedure.

This component authenticates users via randomly-generated codes that are associated with their CMS record.

### Install
1. Get this code to `app/components` e.g. via `git clone ...` 
1. Run the migrations e.g. via `muse migration run ...`
1. Populate the codes table - `jos_reply_access_codes`

### Adding a Page
To add a form-based page:
1.  Add a record to `jos_reply_pages`
1. Create a view for the page in `site/views/pages` 

### Adding an Email Subscription Option
1. Create or confirm name of the corresponding profile field
1.  Add a record to `jos_email_subscriptions`
1. Create a view (content to appear beneath label) if desired 

### Gotchas
* Set the email page ID in `secrets/code.php`
* `jos_email_subscriptions.profile_field_name` must correspond with a `jos_user_profile_fields.name`
* `jos_user_profile_options` must be populated for the user profile fields. `jos_user_profile_options.field_id` must correspond with id from `jos_user_profile_fields.id`
* Populate the codes table

### Component-Specific Tables
* `jos_reply_access_codes` - user-specific access codes
* `jos_reply_pages` - pages for collecting data
* `jos_reply_replies` - user-submitted responses
* `jos_email_subscriptions` - email subscription options

### Integrations
The email subscription update functionality integrates with the core user profile data.
* `jos_user_profile_fields` - profile fields
* `jos_user_profile_options` - profile field response options
* `jos_user_profiles` - associates user data with profile fields

### Manually Generating Access Codes

See the [record generator project repo](https://github.com/hubzero/nano-sf-record-generator) for instructions on how to manually generate access codes.
