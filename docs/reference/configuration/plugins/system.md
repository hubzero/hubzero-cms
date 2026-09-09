<!--
status: generated
source: core/plugins/system/*/*.xml
-->

# System plugins

Parameters of every plugin in the `system` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## System - Activity (`plg_system_activity`)

Log activity

This plugin has no parameters.

## System - Authfactors (`plg_system_authfactors`)

Routing plugin to check for necessary authentication factors

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `clients` | Client enforcement | checkboxes | `1 (Administrator)` | Which client types should this factor be enforced on?. Options: `0` Site, `1` Administrator. |

## System - Cache (`plg_system_cache`)

Provides various cache functions such as page caching, cache cleaning, etc.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `pagecache` | Use Page Caching | radio | `0 (No)` | This will cache the entire contents of the page instead of re-rendering on every page hit. This can gain significant performance improvements for high-traffic sites. Options: `0` No, `1` Yes. |
| `browsercache` | Use Browser Caching | radio | `0 (No)` | If yes, use mechanism for storing page cache in the browser. Options: `0` No, `1` Yes. |
| `cacheexempt` | Exempt Pages | textarea | `/about/contact` | Add a line for each URL of the site that should not be cached. |

## System - CAC (`plg_system_certificate`)

Enables client side certificate based route limiting

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `failure_location` | Failure location | text | `/invalidcert.php` | A static page to display when no certificate is present |

## System - HUBzero (`plg_system_content`)

Allows for search indexing once content has been saved.

This plugin has no parameters.

## System - Content Security Policy (`plg_system_csp`)

Content Security Policy (CSP) is an added layer of security that helps to detect and mitigate certain types of attacks, including Cross Site Scripting (XSS) and data injection attacks.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `mode` | Mode | list | `0 (Report only)` | CSP can be deployed in report-only mode. The policy is not enforced, but any violations are reported to a provided URI. Additionally, a report-only header can be used to test a future revision to a policy without actually deploying it. Options: `0` Report only, `1` Enforce policy, `2` Send both enforcement and reporting policy. |
| `report-uri` | base-uri | text | `https://{host}/api/csp/cms` | Policy for base URI. |

### Policy

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `base-uri` | base-uri | textarea | `'self'` | Policy for base URI. |
| `object-src` | object-src | textarea | `'self' https://fpdownload.adobe.com` | Policy for object source. |
| `child-src` | child-src | textarea | `'self' https://*.youtube.com` | Policy for child source. |
| `connect-src` | connect-src | textarea | `'self' wss://*.{host}.org wss://{host}.org https://www.google-analytics.com https://stats.g.doubleclick.net https://www.dropbox.com https://graph.facebook.com` | Policy for connect source. |
| `default-src` | default-src | textarea | `'self' https://*.{host}.org` | Policy for default source. |
| `font-src` | font-src | textarea | `'self' https://fonts.gstatic.com data: safari-extension: chrome-extension: https://maxcdn.bootstrapcdn.com` | Policy for font source. |
| `form-action` | form-action | textarea | `'self' https://platform.twitter.com https://syndication.twitter.com` | Policy for form action. |
| `frame-src` | frame-src | textarea | `'self' https://*.{host}.org https://*.google.com https://*.youtube.com https://content.googleapis.com https://*.facebook.com https://*.twitter.com` | Policy for frame source. |
| `img-src` | img-src | textarea | `* data: image:` | Policy for image source. |
| `script-src` | script-src | textarea | `'self' 'unsafe-eval' 'unsafe-inline' https://*.google-analytics.com https://*.google.com https://connect.facebook.net https://www.linkedin.com https://platform.twitter.com https://cdn.syndication.twimg.com https://www.gstatic.com https://*.googleapis.com https://platform.linkedin.com https://cdnjs.cloudflare.com https://*.cloudfront.net` | Policy for script source. |
| `style-src` | style-src | textarea | `'self' 'unsafe-inline' https://platform.twitter.com https://ton.twimg.com https://*.googleapis.com https://www.google.com https://maxcdn.bootstrapcdn.com https://cdnjs.cloudflare.com` | Policy for style source. |

### Report

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `report-base-uri` | base-uri | textarea | — | Policy for base URI. |
| `report-object-src` | object-src | textarea | — | Policy for object source. |
| `report-child-src` | child-src | textarea | — | Policy for child source. |
| `report-connect-src` | connect-src | textarea | — | Policy for connect source. |
| `report-default-src` | default-src | textarea | — | Policy for default source. |
| `report-font-src` | font-src | textarea | — | Policy for font source. |
| `report-form-action` | form-action | textarea | — | Policy for form action. |
| `report-frame-src` | frame-src | textarea | — | Policy for frame source. |
| `report-img-src` | img-src | textarea | — | Policy for image source. |
| `report-script-src` | script-src | textarea | — | Policy for script source. |
| `report-style-src` | style-src | textarea | — | Policy for style source. |

## System - Debug (`plg_system_debug`)

This plugin provides a variety of system information as well as assistance for the creation of translation files.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `filter_groups` | Allowed Groups | usergroup | — | Optionally restrict users that can see debug information to those in the selected user groups. If none selected, all users see the debug information. |
| `filter_users` | Allowed Users | textarea | — | Optionally restrict users that can see debug information to those in the comma-separated list of usernames. If none entered, all users see the debug information. |
| `profile` | Show Profiling | radio | `1 (Yes)` | Display the profiling waypoints. Options: `1` Yes, `0` No. |
| `queries` | Show Queries | radio | `1 (Yes)` | Display a list the queries executed while displaying the page. Options: `1` Yes, `0` No. |
| `query_types` | Show Query Types | radio | `1 (Yes)` | Display a list of unique query types and their number of occurrences for the current page. Useful for finding out about repeated queries that are either redundant or which can be grouped into a single, more efficient query. Options: `1` Yes, `0` No. |
| `memory` | Show Memory Usage | radio | `1 (Yes)` | Display the total memory usage. Options: `1` Yes, `0` No. |
| `theme` | Color Theme | list | `dark` | Choose between a dark or light color the for the debug output. Options: `dark`, `light`. |

### Language Options

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `language_errorfiles` | Show errors when parsing language files | radio | `1 (Yes)` | Display a list of the language files that are in error according to the HUBzero INI specification. Options: `1` Yes, `0` No. |
| `language_files` | Show Language Files | radio | `1 (Yes)` | Display a list of the language files that the CMS has tried to load. Options: `1` Yes, `0` No. |
| `language_strings` | Show Language String | radio | `1 (Yes)` | Display a list of the untranslated language strings. Options: `1` Yes, `0` No. |
| `strip-first` | Strip First Word | radio | `1 (Yes)` | In multi-word strings, always strip the first word. Options: `1` Yes, `0` No. |
| `strip-prefix` | Strip From Start | textarea | — | Strip words from the beginning of the string. For multiple words, use the format: (word1\|word2) |
| `strip-suffix` | Strip From End | textarea | — | Strip words from the end of the string. For multiple words, use the format: (word1\|word2) |

### Logging

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `log-database-queries` | Log database queries | radio | `0 (No)` | If enabled, database queries will be logged. Only use this setting for short periods of time as it will create LARGE files with sensitive data. Options: `1` Yes, `0` No. |

## System - Highlight (`plg_system_highlight`)

System plugin to highlight specified terms.

This plugin has no parameters.

## System - HUBzero (`plg_system_hubzero`)

Enables HUBzero support

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `search` | Search Component | text | `search` | The name of the component to default searches to. |

## System - Incomplete (`plg_system_incomplete`)

Routing plugin to check user registration status for missing/required fields

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `exceptions` | Routing Exceptions | textarea | — | A list of routes to be allowed even when the user's profile is incomplete. One rule per line, composed of dot notation: component.controller.task |

## System - jQuery (`plg_system_jquery`)

This plugin allows to embed jQuery, jQueryUI and Fancybox into your website.Built-in versions:jQuery: 1.11.1jQuery UI: 1.10.0Fancybox: 2.1.5

### Jquery

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `jquery` | Embed jQuery | list | `4` | Selects, whether the plugin should attach jQuery to page, and what source is to be used. Options: `0` No, `1` Yes. |

### Jquery ui

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `jqueryui` | Embed jQuery UI | list | `0 (No)` | Selects, whether the plugin should attach jQuery UI to page, and what source is to be used. Options: `0` No, `1` Yes. |

### Jquery fb

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `jqueryfb` | Embed fancyBox | list | `0 (No)` | Selects, whether the plugin should attach fancyBox to page, and what source is to be used. Options: `0` No, `1` Yes. |

### Advanced

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `activateSite` | Activate at site (frontend) | radio | `1 (Yes)` | Allows to embed the libraries into the frontend site. Options: `0` No, `1` Yes. |
| `noconflictSite` | jQuery noConflict mode | radio | `0 (No)` | Allows to activate jQuery().noConflict() mode. Make sure you scripts are adapted. Use carefully, together with the option below this mode can disable dropdown menu in adminpanel. Improved in v1.2. Options: `0` No, `1` Yes. |

## System - Language Code (`plg_system_languagecode`)

Provides the ability to change the language code in the generated HTML document to improve SEO.The fields will appear when the plugin is enabled and saved.More information at W3.org 

This plugin has no parameters.

## System - Language Filter (`plg_system_languagefilter`)

This plugin filters the displayed content depending on language.This plugin is to be enabled only when the Language Switcher module is published.If this plugin is activated, it is suggested to publish the administrator multilanguage status module.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `detect_browser` | Language Selection for new Visitors. | list | `1 (Browser Settings)` | Choose Site default language or try to detect the browser settings language. It will default to site language if browser settings can't be found. Options: `0` Site Language, `1` Browser Settings. |
| `automatic_change` | Automatic Language Change | radio | `1 (Yes)` | This option will automatically change the content language used in the frontend when a user site language is changed. Options: `0` No, `1` Yes. |
| `menu_associations` | Menu associations | radio | `0 (No)` | This option will allow menu associations when switching from one language to another. Options: `0` No, `1` Yes. |
| `remove_default_prefix` | Remove URL Language Code | radio | `0 (No)` | Remove the defined URL Language Code of the Content Language that corresponds to the default site language when Search Engine Friendly URLs is set to 'Yes'. Options: `0` No, `1` Yes. |
| `lang_cookie` | Cookie Lifetime | radio | `1 (Year)` | Language cookies can be set to expire at the end of the session or after a year. Default is a year. Options: `0` Session, `1` Year. |
| `alternate_meta` | Add alternate meta tags | radio | `0 (No)` | Add alternate meta tags for menu items with associated menu items in other languages. Options: `0` No, `1` Yes. |

## System - Log (`plg_system_log`)

Provides System Logging

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `log_username` | Log user names | radio | `0 (No)` | This option will log used user names when an authentication failed. Options: `0` No, `1` Yes. |

## System - Logout (`plg_system_logout`)

The system logout plug-in enables the CMS to redirect the user to the home page if he chooses to logout while he is on a protected access page.

This plugin has no parameters.

## System - Member Home Page (`plg_system_memberhome`)

This plugin redirects your members to a pre-defined homepage if they are logged in. Every link on your website that directs to your homepage, will now be redirected to the new member homepage.

### Customize

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `menuId` | Redirect to Menu | menuitem | `0 (--Select a Menu Item--)` | Select the menu item that will be used for the alternate Home Page if user is logged in. (This list only includes Published menu items). Options: `0` --Select a Menu Item--. |

## System - Mobile (`plg_system_mobile`)

System plugin for Mobile template

This plugin has no parameters.

## System - P3P Policy (`plg_system_p3p`)

The system P3P policy plugin allows for sending a customised string of P3P policy tags in the HTTP header. This is required for the sessions to work on certain browsers, i.e. Internet Explorer 6 and 7.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `header` | P3P Tags | text | `NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM` | Enter your P3P policy tags. For more information consult The Platform for Privacy Preferences specification, http://www.w3.org/TR/P3P/ |

## System - Password (`plg_system_password`)

Routing plugin to check user password expiration and validation rules

This plugin has no parameters.

## System - Redirect (`plg_system_redirect`)

The system redirect plug-in enables the Redirect system to catch missing pages and redirect users.

This plugin has no parameters.

## System - Referrer Policy (`plg_system_referrerpolicy`)

The system Referrer Policy plugin allows for sending a referrer policy in the HTTP header. This is recommended to better guard against Cross-Site Scripting.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `policy` | Referrer Policy | list | `same-origin` | PLG_SYSTEM_REFERRERPOLICY_POLICY_DESC. Options: `` (none), `no-referrer`, `no-referrer-when-downgrade`, `same-origin`, `origin`, `strict-origin`, `origin-when-cross-origin`, `strict-origin-when-cross-origin`, `unsafe-url`. |

## System - Remember Me (`plg_system_remember`)

Provides remember me functionality

This plugin has no parameters.

## System - SEF (`plg_system_sef`)

Adds Search Engine Friendly (SEF) support to links in the document. It operates directly on the HTML.

This plugin has no parameters.

## System - Spamjail (`plg_system_spamjail`)

Routing plugin to check spam violators and throw them in spam jail

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `session_count` | Session count | text | `5` | The number of allowed spam incidents per session. |
| `user_count` | User count | text | `10` | The number of allowed spam incidents per user, over all time. |
| `spam_video` | Jail video | text | — | ID of Youtube video to be displayed on spam jail page. |

## System - Super Group (`plg_system_supergroup`)

Super group support

This plugin has no parameters.

## System - Unapproved (`plg_system_unapproved`)

Routing plugin to check user approval status

This plugin has no parameters.

## System - Unconfirmed (`plg_system_unconfirmed`)

Routing plugin to check user email confirmation status

This plugin has no parameters.

## System - Userconsent (`plg_system_userconsent`)

Routing plugin to get user consent to monitor

This plugin has no parameters.

## System - xFeed (`plg_system_xfeed`)

This will force links that end in .rss or .atom to raw document mode, allowing the respective component to output XML.

This plugin has no parameters.
