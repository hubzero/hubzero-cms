<!--
status: generated
source: core/plugins/user/*/*.xml
-->

# User plugins

Parameters of every plugin in the `user` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## User - Auto-Approve (`plg_user_autoapprove`)

Plugin for auto-approving user accounts based on email address

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `email_pattern` | Email Regex Pattern | text | `.*\.edu$` | Regular expression pattern that user account emails must match to be auto-approved. |

## User - Constant Contact (`plg_user_constantcontact`)

Syncs member email preferences with Constant Contact (V3 API)

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `ccManageEmailPreference` | Manage Email Preferences | list | `0 (No)` | Allow Constant Contact to manage email preferences for hub members. Options: `0` No, `1` Yes. |
| `ccClientId` | Client ID (API Key) | text | — | The API Key (Client ID) from your Constant Contact V3 application |
| `ccClientSecret` | Client Secret | password | — | The Client Secret from your Constant Contact V3 application |
| `ccAccessToken` | Access Token | textarea | — | Initial access token generated from the Constant Contact developer portal. Auto-refreshed by the plugin. |
| `ccRefreshToken` | Refresh Token | textarea | — | Initial refresh token generated from the Constant Contact developer portal. Auto-refreshed by the plugin. |
| `ccListId` | Contact List ID | text | — | UUID of the contact list to use. Leave blank to use the first list in your account. |

## User - D1 (`plg_user_d1`)

Adds users to the d1_nation group on login when their IP resolves to a D1 country group via the ipcountry and countrygroup tables.

This plugin has no parameters.

## User - Domain Restriction (`plg_user_domainrestriction`)

Restrict registration to one or more specific domain names

### Ipsecurity

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `whitelist` | Whitelist | textarea | `0.0.0.0/32` | Any address or netmask listed here will not be subject to the email address/domain/tld tests in the other tabs.  Default 0.0.0.0/32 matches no addresses. |
| `blacklist` | Blacklist | textarea | `0.0.0.0/32` | Any address or netmask listed here will not be allowed to register - regardless of the email address/domain/tld used to register. Default 0.0.0.0/32 matches no addresses. |

### Disallowed

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `badtld` | Disallowed TLDs | textarea | — | Use this field to add individual TLD (Top Level Domains) to the list below.  Example: adding EDU will disallow all email addresses containing the .edu TLD. |
| `baddomain` | Disallowed Domains | textarea | — | Use this field to add individual domains to the list below.  Domains in this list are NOT allowed to register (Beware - this can override the allowed domains settings). |
| `bademail` | Disallowed Addresses | emails | — | Use this field to add individual domains to the list below.  Addresses in this list are NOT allowed to register (regardless of the allowed domains settings). |

### Allowed

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `domain` | Allowed Domains | textarea | — | Use this field to add individual domains to the list below.  Domains in this list are allowed to register. |
| `email` | Allowed Emails | textarea | — | Use this field to add individual domains to the list below.  Addresses in this list are allowed to register (regardless of the domains listed above). |

## User - Geo (`plg_user_geo`)

PLG_USER_GEO_XML_DESCRIPTION

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `group` | Group alias/cn | text | — | The group alias/cn that validated users will be placed into upon login from an IP address within the below location. |
| `location` | Location code | text | — | The geo location code that the user's IP address must be within in order to be placed in the given group. |

## User - HUBzero (`plg_user_hubzero`)

Handles HUBzero's default User synchronisation

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `autoregister` | Auto-create Users | radio | `1 (Yes)` | Automatically create Registered Users where possible. Options: `0` No, `1` Yes. |
| `mail_to_user` | Notification Mail to User | radio | `0 (No)` | When an administrator creates a user account, this determines if an email, which contains their username and password, is sent to the user. Options: `0` No, `1` Yes. |
| `mail_to_admin` | Notification Mail to Administrator | radio | `1 (Yes)` | When a user creates an account, this determines if an email is sent to the site administrator. Options: `0` No, `1` Yes. |

## User - LDAP (`plg_user_ldap`)

Enables LDAP support

This plugin has no parameters.

## User - Middleware (`plg_user_middleware`)

Sets user quoats and (tool) session limits based on assigned access group.

This plugin has no parameters.

## User - US (`plg_user_us`)

Adds users to the location_us group on login when their IP resolves to a US address via the ipcountry table.

This plugin has no parameters.

## User - xHUB (`plg_user_xusers`)

Enables xhub support

This plugin has no parameters.
