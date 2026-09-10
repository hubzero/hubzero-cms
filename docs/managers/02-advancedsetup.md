<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/advancedsetup
source-id: 3338
modified: 2015-09-21
imported: 2026-09-09
summary: Connecting a hub to the outside services it commonly uses — analytics, external file storage for projects, the rich text editor, and CAPTCHA — and where the sign-in providers are covered instead.
-->
# Integrations

Everything a hub connects to that is not part of the hub. Each of these is a
plugin or a module you enable and hand a pair of credentials to, after
registering the hub with the service.

None of it is required. A hub runs perfectly well with no analytics, no
external storage, the editor it was installed with, and no CAPTCHA. Come here
when something has made one of them necessary — a funder asking for usage
figures, a project with more data than the hub should hold, a wave of
registrations by script. Each section below opens by saying which hubs that
is, so you can stop reading after a paragraph.

Every integration on this page has a cost the first paragraph will not tell
you: it is an account at somebody else's service, held by a person who will
eventually leave. Write down which account, in whose name, before you set one
up. A dead API key is the commonest way an integration stops working, and it
is the hardest to diagnose because nothing on the hub says so.

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

For hubs that have to report usage to somebody — a funder, a department, a
steering committee. This is usually the only way a hub gets page-level
figures. The Usage component publishes a `/usage` page, but it computes
nothing: it draws every figure from tables filled in by metrics tooling that
is not part of this repository, so on a hub installed from this repository
alone it has nothing to show. See [Usage](09-components/37-usage.md).

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

## External file storage in projects

For hubs whose projects hold more data than the hub should, or data that
already lives somewhere else and should not be copied. A project's own file
repository is the default and is the right answer for most projects: it is
versioned, it is backed up with the hub, and it needs no account anywhere. A
connection is for the case where that is not true — a group with a terabyte
in an S3 bucket, a modelling team whose code is a GitHub repository, a lab
that has standardised on Drive.

It is not a backup, and it is not a migration. A connection mounts the remote
store *alongside* the project's own files; it does not copy anything into the
hub, and it does not make the hub responsible for the data. If the account
behind a connection is closed, the files are gone from the project.

### What ships

Five plugins in the `filesystem` group. **Filesystem - Local** is the
project's own repository and needs nothing. The other four appear in a
project's **Files** tab as connection types:

| Plugin | Parameters you set hub-wide | What the project supplies |
|---|---|---|
| **Filesystem - Google Drive** | **Client ID**, **Client Secret** | Nothing but a name; a project manager authorizes with their own Google account. |
| **Filesystem - Dropbox** | **App key**, **App secret** | Nothing but a name; a project manager authorizes with their own Dropbox account. |
| **Filesystem - Github** | **Client ID**, **Client Secret** | **Repository**. A public repository is read anonymously and needs no authorization at all. |
| **Filesystem - AWS S3** | None it reads | Everything: **Access Key ID**, **Secret Access Key**, **Endpoint Region**, **Bucket Name**, and **Directory to connect**, per connection. |

The split matters. Google Drive and Dropbox are one registration you make
once, on behalf of the whole hub, and then every project authorizes against
it with its own account. AWS S3 is the opposite — the credentials are part of
the connection, so each project brings its own bucket and its own key, and
you as manager only have to enable the plugin. GitHub sits between the two:
the hub's registration is only needed for private repositories.

### Turning one on

Using Google Drive as the example.

1. Register the hub as a web application with the provider. For Google that
   is a Google Cloud project with the Drive API enabled; the authorized
   redirect URI is
   `https://<yourhub>/developer/callback/googledriveAuthorize`.
2. **Extensions → Plug-in Manager**, open **Filesystem - Google Drive**, set
   **Status** to **Enabled**, and paste the credentials into **Client ID** and
   **Client Secret**.
3. **Save & Close**.
4. Tell the project managers. Nothing appears to members until somebody in a
   project creates a connection.

In the project, a manager opens the **Files** tab, picks the provider from
the **New Connection** drop-down, gives the connection a **Name**, decides
whether to tick **Share connection with everyone in the project?**, saves,
and then authorizes it against their own account. The connection then sits
beside the project's own repository, with **Edit**, **Delete**, **Refresh
Connection Credentials** and **Refresh Connection Path** against it.

> **Warning:** Only a project manager can authorize a connection, and the
> authorization binds *their* account to it. When that person leaves the
> project, or revokes the hub's access at the provider, the connection stops
> working for everyone in the project until another manager re-authorizes it.
> That is the failure managers are asked about, and **Refresh Connection
> Credentials** is the fix.

> **Warning:** The **New Connection** drop-down lists every provider
> registered on the hub, whether or not its plugin is enabled and whether or
> not you have given it credentials. A project can therefore create a
> connection to a service that cannot work. Disabling a plugin you are not
> using does not take it out of that list.

### The older project-wide Google connection

A second, earlier path exists and some hubs still run on it. The
**Projects - Files** plugin (**Extensions → Plug-in Manager**, search for
`projects`) carries a project-wide Google connection of its own:

| Parameter | Notes |
|---|---|
| **Google Connection Enabled** | Off by default. |
| **Google Client ID**, **Google Client Secret**, **Google API Key** | From a Google Cloud project with the Drive API enabled. |
| **Auto Sync** | No auto sync, or every 10 minutes, half hour, hour, 2 hours, or 6 hours. |
| **Connected Projects** | Comma-separated project aliases allowed to connect. Empty means all of them. |

Its redirect URI is different: register `https://<yourhub>/projects/auth` as
the authorized redirect URI and `https://<yourhub>` as the authorized
JavaScript origin. It surfaces as a **Connect** link on the project's **Files**
tab rather than as an entry in **New Connection**, and only the project's
creator sees that link on a project with no connection yet.

The two are independent, and a hub can have both on at once, which is
confusing for everyone. On a new hub use the **Filesystem - Google Drive**
plugin and leave **Google Connection Enabled** off.

## The rich text editor

Every hub already has one, and most managers never touch this. The reason to
come here is a complaint: the editor mangles pasted markup, or it will not
let somebody insert the tag they need, or a member who writes in Markdown
wants a plain box instead. Changing it is a global change — the setting
applies to every editor field on the hub, for everyone.

The editor is set in **Site → Global Configuration**, on the **Site** tab,
under **Site Settings**, in **Default Editor**. The list offers exactly the
editor plugins that are enabled in **Extensions → Plug-in Manager**, so
enable the one you want first, then choose it.

| Plugin | Enabled on a fresh install | Notes |
|---|---|---|
| **Editor - CKEditor** | Yes | CKEditor 4. This is what a hub runs — the installer writes `ckeditor` into the configuration. |
| **Editor - CKEditor 5** | Yes | CKEditor 5. Enabled, so it is offered in the list, but not what the installer selects. |
| **Editor - CodeMirror** | Yes | A source editor with syntax highlighting. |
| **Editor - PageDown** | Yes | Markdown. |
| **Editor - TinyMCE** | Yes | The form's declared fallback, but not the installed value. |
| **Editor - Wikiwyg**, **Editor - Wikitoolbar** | Yes | For the hub's wiki markup. |
| **Editor - None** | Yes | A plain textarea. |

> **Note:** The manifest's declared default is `tinymce` and the installer
> writes `ckeditor`. Where a manifest default and the shipped install data
> disagree, the stored value is what the hub runs — so a fresh hub is on
> CKEditor 4, whatever the form's declaration says.

Changing the setting takes effect on the next page load and is reversible,
but content already saved keeps whatever markup the previous editor produced.
Switching a hub with years of articles from a WYSIWYG editor to a Markdown
one does not convert anything.

> **Note:** Older documentation told you to enable **Content - HTML Format
> Handler** as well when switching to CKEditor. That is not needed. Both of
> that plugin's hooks return immediately for the `com_content.article`
> context, so it does nothing at all to articles; it handles HTML in the
> components that also accept wiki markup.

## reCAPTCHA

For hubs with open registration that are getting accounts created by script.
The symptom is unmistakable: a run of registrations minutes apart, plausible
names, addresses at throwaway domains, and profiles that fill up with
advertising a day later. A CAPTCHA is the cheapest thing that stops it.

It is not a spam filter. It stops a script *creating an account*; it does
nothing about a person who registers by hand and then posts advertising, and
nothing about the accounts already created. Those are [Spam](11-spam.md).

Hubzero ships three CAPTCHA plugins. Check what is already on before you
register anything with Google — the installer leaves **Captcha - Image**
enabled, **Captcha - Math** disabled, and **Captcha - ReCaptcha** disabled.
So a fresh hub already has a CAPTCHA, and if accounts are still being created
by script it is because that one is being solved. **Captcha - Image** and
**Captcha - Math** need no account anywhere; **Captcha - ReCaptcha** puts
Google reCAPTCHA v2 on the registration form and anywhere else that asks for
a CAPTCHA, and needs a Google account and a registered domain.

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

> **Note:** Enabling **Captcha - ReCaptcha** with either key blank renders
> nothing and reports nothing. If you turn it on, disable the others, and the
> registration form comes up with no CAPTCHA at all, that is the reason —
> check the keys rather than the form.

The form only asks for a CAPTCHA if the **CAPTCHA** field in the Members
component's registration options is not set to hidden. See
[Registration](05-configuring/02-registration.md).
