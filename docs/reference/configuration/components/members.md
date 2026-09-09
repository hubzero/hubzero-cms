<!--
status: generated
source: core/components/com_members/config/config.xml
-->

# Members (com_members)

Manage members

Parameters from [`core/components/com_members/config/config.xml`](../../../../core/components/com_members/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Component

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `allowUserRegistration` | Allow User Registration | radio | `1 (Yes)` | If set to Yes, new Users allowed to self-register. Options: `0` No, `1` Yes. |
| `new_usertype` | New User Registration Group | usergroup | `2` | The default group that will be applied to New Users Registering via the frontend. |
| `guest_usergroup` | Guest Access Group | usergroup | `1` | The default Group that will be applied to guest (not logged-in) Users. |
| `sendpassword` | Send Password | radio | `1 (Yes)` | If set to Yes the user's initial password will be emailed to the user as part of the registration mail. Options: `0` No, `1` Yes. |
| `useractivation` | New User Account Activation | list | `1 (Self)` | If set to None, the user will be registered right away. If set to Self, the User will be emailed a link to activate their account before they can log in. If set to Admin, the user will be emailed a link to verify their email address, and an administrator must manually review and approve the new user account. Options: `0` None, `1` Self, `2` Admin. |
| `useractivation_email` | Email On Account Activation | radio | `0 (No)` | Send an email to the user, notifying them that their account has been activated. Note: This only applies when 'New User Account Activation' is set to 'Admin'. Options: `0` No, `1` Yes. |
| `simple_registration` | Simple Registration | radio | `0 (No)` | Allow simple registration for members coming in through 3rd party authentication providers.  WARNING: this will not allow users to choose their username or prompt them to link to an existing account.  If this is an issue, do not use this option.  This option typically only makes sense for new hubs, or hubs that do not use tools. Options: `0` No, `1` Yes. |
| `allow_duplicate_emails` | Allow Duplicate Emails (DON'T DO IT!) | list | `0 (No)` | If set to Yes, multiple users are allowed to have the same email address (REALLY, DON'T ALLOW THIS, BAD THINGS MAY HAPPEN). If set to grandfathered, existing accounts will not be rejected, but new accounts cannot be created with duplicate emails. Options: `0` No, `1` Yes, `2` Grandfathered. |
| `mail_to_admin` | Notification Mail to Administrators | radio | `1 (Yes)` | If 'New User Account Activation' is set to 'None' or 'Self', allows or not a notification mail to be sent to administrators. Options: `0` No, `1` Yes. |
| `captcha` | Captcha | plugins | — | Select the captcha plugin that will be used in the registration, password and username reminder forms. You may need to enter required information for your captcha plugin in the Plugin Manager.If 'Use Default' is selected, make sure a captcha plugin is selected in Global Configuration. Options: `` - Use Default -, `0` - None Selected -. |
| `frontend_userparams` | Frontend User Parameters | radio | `1 (Show)` | If set to Show, Users will be able to select their language, editor, and Help Site preferences on their details screen when logged-in to the frontend. Options: `0` Hide, `1` Show. |
| `site_language` | Frontend Language | radio | `0 (Hide)` | If 'Frontend User Parameters' is set to 'Show', users will be able to select their frontend language preference when registering. This is specially handy in a multilanguage setting. Options: `0` Hide, `1` Show. |

## Login

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `reset_count` | Maximum Reset Count | integer | `10` | The maximum number of password resets allowed within the time period. Zero indicates no limit. |
| `reset_time` | Time in Hours | integer | `1` | The time period, in hours, for the reset counter. |
| `login_attempts_limit` | Maximum Failed Login Attempts | integer | `10` | The maximum number of failed login attempts allowed within the time period. Zero indicates no limit. |
| `login_attempts_timeframe` | Time in Hours | integer | `1` | The time period, in hours, for the failed login attempts counter. |
| `login_log_timeframe` | Purge Log After | list | — | The time period to retain login attempts in the log. Records older than the selected value will automatically be purged. This can keep the log from becoming so large it affects performance. Options: `` Never, `1 week`, `2 weeks`, `3 weeks`, `1 month` 1 Month, `2 months` 2 Months, `3 months` 3 Months, `6 months` 6 Months, `1 year` 1 Year. |
| `fail2ban` | Fail2Ban | list | `0 (Off)` | Trigger Fail2Ban when the threshold of blocked accounts per IP address is met. Options: `1` On, `0` Off. |
| `blocked_accounts_limit` | Maximum Number Blocked Accounts | integer | `10` | The maximum number of blocked accounts per IP address. |
| `blocked_accounts_timeframe` | Time in Hours | integer | `1` | The time period, in hours, for the failed login attempts counter. |
| `fail2ban-jail` | Fail2Ban Jail | text | `hub-login` | The jail used to control login failure banning. |

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `privacy` | Default Privacy | list | `0 (Private)` | Set the default privacy for new profiles. Options: `0` Private, `1` Public. |
| `bankAccounts` | Bank Accounts | list | `0 (Off)` | Enable Bank Accounts. Options: `1` On, `0` Off. |
| `manage_quotas` | Manage Quotas | list | `0 (No)` | Use the CMS to manage user disk quotas?. Options: `0` No, `1` Yes. |
| `rorApi` | Research Organization Registry API | list | `1 (Yes)` | Uses ROR API to retrieve list of organizations. Options: `0` No, `1` Yes. |
| `rorApiVersion` | Research Organization Registry API Version Number | text | — | The version number of Research Organization Registry API, such as 'v2'. |
| `orcid_service` | ORCID Service | list | `members (Members)` | Select the service to use for ORCID. Sandbox is for testing and debugging purposes. The Public service only allows for searching records, new ID creation is not allowed. Options: `public` Public (search only), `members` Members, `sandbox` Sandbox. |
| `orcid_sandbox_client_id` | ORCID Sandbox Client ID | text | — | Authorization client ID for the sandbox members ORCID service. |
| `orcid_members_client_id` | ORCID Members Client ID | text | — | Authorization client ID for the production members ORCID service. |
| `orcid_sandbox_token` | ORCID Sandbox Token | text | — | Authorization token for the sandbox ORCID service. |
| `orcid_members_token` | ORCID Members Token | text | — | Authorization token for the members ORCID service. |
| `orcid_sandbox_redirect_uri` | ORCID Sandbox Authentication Redirect URI | text | — | Authorization redirect URI for the sandbox ORCID service. |
| `orcid_members_redirect_uri` | ORCID Members Authentication Redirect URI | text | — | Authorization redirect URI for the members ORCID service. |
| `orcid_sandbox_permission_uri` | ORCID Sandbox Grant Permission URI | text | — | Grant ORCID Management Permission URI for the sandbox ORCID service. |
| `orcid_members_permission_uri` | ORCID Members Grant Permission URI | text | — | Grant ORCID Management Permission URI for the members ORCID service. |
| `orcid_institution_field_option` | ORCID Institution Option | list | `0 (No)` | Enable searching by institution name. Options: `0` No, `1` Yes. |
| `orcid_user_institution_name` | Institution name of ORCID user | text | — | The field for setting the institution name. |
| `defaultpic` | Default picture | text | `/core/components/com_members/site/assets/img/profile.gif` | Default placeholder image for user pictures |
| `picture` | Picture Handler | list | — | Select a handler for user pictures. Options: `` Default, `initialcon` User's Initiials, `identicon` Identicon (visual representation of a hash of unique info), `gravatar` Gravatar. |
| `identicon_color` | Random Picture Color | text | — | Specify a color to use when generating random pictures. Otherwise, the color is auto-determined from the string (email) passed to the generator. |
| `gravatar` | Gravatar Picture | list | `0 (Off)` | Enable Gravatar user pictures. Options: `1` On, `0` Off. |
| `webpath` | Upload path | text | `/site/members` | File path for pictures |
| `homedir` | Home directory path | text | — | Hub users' home directory path (typically '/home/{hubname}') |
| `user_messaging` | User-To-User Messaging | list | `1 (Users with common groups)` | Allow/Disallow user-to-user messaging. Options: `0` None, `1` Users with common groups, `2` Any User. |
| `employeraccess` | Allow Employer Access | list | `0 (Disallow)` | Determine whether to show resume on a private profile to subscribed employers (if Jobs Component is active). Options: `0` Disallow, `1` Allow. |
| `gidNumber` | Group ID number | text | `100` |  |
| `gid` | Group ID | text | `users` |  |

## Password

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `passhash_mechanism` | Password Hashing/Encryption Mechanism | list | `sha512` | Options: `CRYPT_SHA512` SHA-512, `MD5`. |
| `shadowMax` | Shadow Maximum | text | — |  |
| `shadowMin` | Shadow Minimum | text | `0` |  |
| `shadowWarning` | Shadow Warning | text | `7` |  |

## Registration

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `ConfirmationReturn` | Confirmation Return URL | text | — | Redirect here after confirming email... |
| `registrationUsername` | Username | text | `RRUU` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationPassword` | Password | text | `RRUU` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationConfirmPassword` | Password Confirmation | text | `RRUU` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationFullname` | Full Name | text | `RRUU` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationEmail` | Email | text | `RRUU` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationConfirmEmail` | Email Confirmation | text | `RRUU` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationOptIn` | OptIn | text | `HHHO` | Receieve Emails. In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationCAPTCHA` | CAPTCHA | text | `RHHH` | In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
| `registrationTOU` | TOU | text | `RHRH` | Terms of Use. In order: Create, Proxy Create, Update, Edit. O = Optional, R = Required, U = Read Only, H = Hidden |
