<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/advancedsetup
source-id: 3338
modified: 2015-09-21
imported: 2026-09-09
summary: Connecting a hub to the outside services it commonly uses — analytics, Google Drive, the rich text editor, and CAPTCHA — and where the sign-in providers are covered instead.
-->
# Integrations

Everything a hub connects to that is not part of the hub. Each of these is a
plugin or a module you enable and hand a pair of credentials to, after
registering the hub with the service.

> **Note:** This page was imported as "Getting Started: Advanced Setup" and
> was mostly a set of step-by-step instructions for registering the hub with
> Facebook, LinkedIn, Google, ORCID, and CILogon. Those instructions were
> stale, and they duplicated
> [Authentication](05-configuring/06-authentication.md) and
> [External authenticators](05-configuring/07-extauth.md), which cover the
> same ground against the current plugins. They have been removed rather than
> maintained in two places. What is left is the material that has nowhere
> else to live.

## Sign-in providers

Every way of signing in — Google, Facebook, LinkedIn, ORCID, CILogon,
Shibboleth, Globus, and the hub's own accounts — is an authentication plugin.
Registering the hub with the provider, the redirect URIs each one needs, and
the plugin parameters are all in
[Authentication](05-configuring/06-authentication.md), with the federated
providers in [External authenticators](05-configuring/07-extauth.md).

## The hub's own pages

A new hub ships with a **Getting Started** article at `/gettingstarted`, and
with **About Us**, **Contact Us**, and **Terms of Use** among the other
sample articles. They are ordinary articles: edit them at **Content →
Article Manager**, as described in
[Article Manager](08-content/articlemanager.md). Rewrite them before the hub
opens to the public — the sample text describes a hub in general, not yours.

## Google Analytics

Two modules can carry a Google tag. Both are site modules with a single
parameter, and both need a position that appears on every page — `footer` in
the templates that ship.

| Module | Parameter | What it loads |
|---|---|---|
| **Google Gtag** | **Tracking ID** | `gtag.js` from `googletagmanager.com`. This is the one to use. |
| **Google Analytics** | **API Key** | `analytics.js`, the Universal Analytics tag. Google retired that property type in 2023, so it collects nothing. |

To add one:

1. Get a measurement ID from Google Analytics for the hub's property.
2. Go to **Extensions → Module Manager** and select **New**.
3. Choose **Google Gtag** from the list of module types.
4. Give it a **Title**, set **Position** to `footer`, set **Status** to
   **Published**, and leave **Module Assignment** at **On all pages**.
5. Paste the measurement ID into **Tracking ID**.
6. **Save & Close**.

> **Warning:** Both modules write the tag into the page unconditionally, with
> no consent gate. If your hub is subject to a cookie consent regime, deal
> with that before you publish the module.

## Google Drive in projects

Projects can mount a Google Drive folder alongside the project's own file
repository. There are two paths through the code, and which one a hub uses
depends on how its projects were set up.

The **Projects - Files** plugin (**Extensions → Plug-in Manager**, search for
`projects`) carries the older, project-wide connection:

| Parameter | Notes |
|---|---|
| **Google Connection Enabled** | Off by default. |
| **Google Client ID**, **Google Client Secret**, **Google API Key** | From a Google Cloud project with the Drive API enabled. |
| **Auto Sync** | No auto sync, or every 10 minutes, half hour, hour, 2 hours, or 6 hours. |
| **Connected Projects** | Comma-separated project aliases allowed to connect. Empty means all of them. |

Register the hub as a web application in the Google Cloud console, with
`https://<yourhub>/projects/auth` as the authorized redirect URI, and
`https://<yourhub>` as the authorized JavaScript origin.

The newer path is the **Filesystem - Google Drive** plugin, which takes an
**App ID** and an **App Secret** and appears in a project's **Files** tab
under **New Connection**. Its redirect URI is
`https://<yourhub>/developer/callback/googledriveAuthorize`.

Whichever you use, the person who creates a connection authorizes it with
their own Google account, and the project's other members work through that
connection.

## The rich text editor

The editor used across the administrator interface and the site is set in
**Site → Global Configuration**, on the **Site** tab, under **Site
Settings**, in **Default Editor**. The choices are the enabled editor
plugins:

| Plugin | Notes |
|---|---|
| **Editor - TinyMCE** | The manifest's fallback, but not what a hub runs: the installer writes `ckeditor`. |
| **Editor - CKEditor** | CKEditor 4. |
| **Editor - CKEditor 5** | CKEditor 5. |
| **Editor - CodeMirror** | A source editor with syntax highlighting. |
| **Editor - PageDown** | Markdown. |
| **Editor - Wikiwyg**, **Editor - Wikitoolbar** | For the hub's wiki markup. |
| **Editor - None** | A plain textarea. |

Enable the plugin in **Extensions → Plug-in Manager** first; the **Default
Editor** list only offers plugins that are enabled.

> **Note:** Older documentation told you to enable **Content - HTML Format
> Handler** as well when switching to CKEditor. That is not needed. Both of
> that plugin's hooks return immediately for the `com_content.article`
> context, so it does nothing at all to articles; it handles HTML in the
> components that also accept wiki markup.

## reCAPTCHA

The **Captcha - ReCaptcha** plugin puts Google reCAPTCHA v2 on the
registration form and anywhere else that asks for a CAPTCHA.

1. Register the hub's domain at
   <https://www.google.com/recaptcha/admin> and copy the site key and the
   secret.
2. Go to **Extensions → Plug-in Manager**, open **Captcha - ReCaptcha**, and
   set **Status** to **Enabled**.
3. Put the site key in **Public Key** and the secret in **Private Key**.
4. Optionally set **Theme** (Light or Dark), **Type** (Image or Audio), and
   **Language Code**.
5. **Save & Close**.

> **Warning:** Every *enabled* captcha plugin renders on the registration
> form, so disable **Captcha - Image** and **Captcha - Math** if you want
> only reCAPTCHA. **Global Configuration → Site → Default Captcha** looks
> like it chooses between them, but no code reads that setting.

The form only asks for a CAPTCHA if the **CAPTCHA** field in the Members
component's registration options is not set to hidden. See
[Registration](05-configuring/02-registration.md).
