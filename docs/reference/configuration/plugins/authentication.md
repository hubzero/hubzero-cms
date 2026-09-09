<!--
status: generated
source: core/plugins/authentication/*/*.xml
-->

# Authentication plugins

Parameters of every plugin in the `authentication` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Authentication - Certificate (`plg_authentication_certificate`)

Handles user authentication against client side SSL certificates

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_name` | Display name | text | `Client Certificate` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
| `auto_approve` | Auto approve new users | radio | `0 (No)` | Automatically approve new users that register through this plugin.  This will override the setting in the users component concerning new user approval status. Options: `0` No, `1` Yes. |

## Authentication - CILogon (`plg_authentication_cilogon`)

Handles user authentication against CILogon

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Client ID | text | — | Your Hub's CILogon App ID |
| `app_secret` | Client Secret | text | — | Client Secret provided when your hub is registered on CILogon |
| `display_name` | Display name | text | `CILogon` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |

## Authentication - Email Token (`plg_authentication_emailtoken`)

Handles user authentication from email tokens

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `remember_me_default` | Remember me default state | list | `0 (Unchecked)` | Select whether or not remember me option is checked by default. Options: `0` Unchecked, `1` Checked. |
| `display_name` | Display name | text | — | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `1 (Yes)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |

## Authentication - Facebook (`plg_authentication_facebook`)

Handles user authentication against Facebook

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Consumer Key | text | — | Your Hub's Facebook App consumer key |
| `app_secret` | Consumer Secret | text | — | Consumer Secret provided when your hub is registered on Facebook |
| `graph_version` | Facebook Graph Version | text | `v2.9` | PLG_AUTHENTICATION_FACEBOOK_GRAPH_VERSION_DESC |
| `display_name` | Display name | text | `Facebook` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |

## Authentication - Globus (`plg_authentication_globus`)

Handles user authentication against Globus

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Client ID | text | — | Your Hub's Globus App ID |
| `app_secret` | Client Secret | text | — | Client Secret provided when your hub is registered on Globus |
| `display_name` | Display name | text | `Globus` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |

## Authentication - Google (`plg_authentication_google`)

Handles user authentication against Google

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Consumer Key | text | — | Your Hub's Google App consumer key |
| `app_secret` | Consumer Secret | text | — | Consumer Secret provided when your hub is registered on Google |
| `display_name` | Display name | text | `Google` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
| `auto_approve` | Auto approve new users | radio | `0 (No)` | Automatically approve new users that register through this plugin.  This will override the setting in the users component concerning new user approval status. Options: `0` No, `1` Yes. |

## Authentication - HUBzero (`plg_authentication_hubzero`)

Handles HUBzero default user authentication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `remember_me_default` | Remember me default state | list | `0 (Unchecked)` | Select whether or not remember me option is checked by default. Options: `0` Unchecked, `1` Checked. |
| `display_name` | Display name | text | — | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `1 (Yes)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |

## Authentication - LinkedIn (`plg_authentication_linkedin`)

Handles user authentication against LinkedIn

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `api_key` | Client ID | text | — | Your LinkedIn App client ID |
| `app_secret` | Client Secret | text | — | Client secret from your LinkedIn App settings |
| `display_name` | Display name | text | `LinkedIn` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |

## Authentication - ORCID (`plg_authentication_orcid`)

Handles user authentication against ORCID

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `authoritative` | PLG_AUTHENTICATION_ORCID_PARAM_AUTHORITATIVE_LABEL | radio | `0 (No)` | PLG_AUTHENTICATION_ORCID_PARAM_AUTHORITATIVE_DESC. Options: `0` No, `1` Yes. |
| `use_sandbox` | PLG_AUTHENTICATION_ORCID_PARAM_USE_SANDBOX_LABEL | radio | `0 (No)` | PLG_AUTHENTICATION_ORCID_PARAM_USE_SANDBOX_DESC. Options: `0` No, `1` Yes. |
| `use_member_api` | PLG_AUTHENTICATION_ORCID_PARAM_USE_MEMBER_API_LABEL | radio | `0 (No)` | PLG_AUTHENTICATION_ORCID_PARAM_USE_MEMBER_API_DESC. Options: `0` No, `1` Yes. |
| `client_id` | Client ID | text | — | Your Hub's ORCID Client ID |
| `client_secret` | Client Secret | text | — | Client Secret provided when your hub is registered on ORCID |
| `display_name` | Display name | text | `ORCID` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
| `auto_approve` | Auto approve new users | radio | `0 (No)` | Automatically approve new users that register through this plugin.  This will override the setting in the users component concerning new user approval status. Options: `0` No, `1` Yes. |
| `name_required` | PLG_AUTHENTICATION_ORCID_PARAM_NAME_REQUIRED_LABEL | radio | `0 (No)` | PLG_AUTHENTICATION_ORCID_PARAM_NAME_REQUIRED_DESC. Options: `0` No, `1` Yes. |
| `email_required` | PLG_AUTHENTICATION_ORCID_PARAM_EMAIL_REQUIRED_LABEL | radio | `0 (No)` | PLG_AUTHENTICATION_ORCID_PARAM_EMAIL_REQUIRED_DESC. Options: `0` No, `1` Yes. |

## Authentication - Purdue University CAS (`plg_authentication_pucas`)

Handles user authentication against Purdue's CAS

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `domain` | Domain | text | `Purdue Career Account (CAS)` | Domain name |
| `display_name` | Display name | text | `Purdue Career` | Text to display on the site when referencing this plugin |
| `auto_logoff` | End CAS Session Automatically? | radio | `0 (No)` | Prompt user with choice to end Purdue CAS session, or perform this action automatically. Options: `0` No, `1` Yes. |
| `debug_location` | Debug location | text | `/var/log/apache2/php/phpCAS.log` | Location where debugging log will be sent (only applicable when site debug is enabled). |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
| `auto_approve` | Auto approve new users | radio | `0 (No)` | Automatically approve new users that register through this plugin.  This will override the setting in the users component concerning new user approval status. Options: `0` No, `1` Yes. |
| `passive_sso` | Passive SSO detection (CAS gateway) | radio | `0 (No)` | When enabled, transparently checks for an existing Purdue SSO session on the login page using a CAS gateway (SAML IsPassive) request, logging the user in automatically if they already have a session. Leave disabled if the identity provider mishandles passive requests: Purdue's Entra ID returns a 'Stale Request' error page instead of silently responding, which breaks login. Only enable once the IdP handles gateway/IsPassive correctly. Options: `0` No, `1` Yes. |

### Profile

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `profile_i2a2` | I2A2 Characteristics | radio | `0 (No)` | Choose whether or not to collect this data from the user's Purdue profile. Options: `0` No, `1` Yes. |

## Authentication - SciStarter (`plg_authentication_scistarter`)

Handles user authentication against SciStarter. See https://scistarter.com/api.html or contact info@scistarter.com for more info.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Consumer Key | text | — | Your Hub's SciStarter App consumer key. Contct info@scistarter.com to acquire a key. |
| `app_secret` | Consumer Secret | text | — | Consumer Secret provided when your hub is registered on SciStarter. This will most likely be your SciStarter API key. Contact info@scistarter.com for more info. |
| `display_name` | Display name | text | `SciStarter` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
| `environment` | Environment | list | `production (Production)` | Environment to use. The sandbox environment should be used for testing only. Options: `production` Production, `sandbox` Sandbox. |

## Authentication - Shibboleth (`plg_authentication_shibboleth`)

Handles user authentication with Shibboleth/InCommon

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
| `dns` | DNS Address | text | `8.8.8.8` | IP address of DNS to use to get user hostnames to potentially match hosts with authentication participants |
| `auto_approve` | Auto approve new users | radio | `0 (No)` | Automatically approve new users that register through this plugin.  This will override the setting in the users component concerning new user approval status. Options: `0` No, `1` Yes. |

### Debug

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `debug_enabled` | Enable debugging | radio | `0 (No)` | Enable to log debugging info to the log file specified directly below. Options: `0` No, `1` Yes. |
| `debug_location` | Debug Log | text | `/var/log/apache2/php/shibboleth.log` | The location where debug info should be written. |
| `testkey` | Testing mode key | text | — | Enter a key here to hide the plugin unless that key is set in the URL parameters (for example, at https://yourhub.org/login?yourkey). Useful if you would like to test your installation before opening it up to all your users. Clear this field to show the form for everyone. |

### Links

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `links` |  | links | `[]` |  |

### Institutions

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `institutions` |  | institutions | `{"xmlPath": "/etc/shibboleth/metadata/federation-metadata.xml", "activeIdps": []}` |  |

## Authentication - Twitter (`plg_authentication_twitter`)

Handles user authentication against Twitter/X

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Client ID | text | — | Your X/Twitter App client ID (OAuth 2.0) |
| `app_secret` | Client Secret | text | — | Client secret from your X/Twitter App settings (OAuth 2.0) |
| `display_name` | Display name | text | `Twitter` | Text to display on the site when referencing this plugin |
| `site_login` | Site login | radio | `1 (Yes)` | Enable this plugin for frontend authentication. Options: `0` No, `1` Yes. |
| `admin_login` | Admin login | radio | `0 (No)` | Enable this plugin for backend authentication. Options: `0` No, `1` Yes. |
