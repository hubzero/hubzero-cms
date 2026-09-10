<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/registration
source-id: 3357
modified: 2014-11-20
-->
# Registration

Registration decides who may create an account on the hub, what the form asks
them for, and what has to happen before the account works. Two screens are
involved: the component's **Options**, which holds the switches, and **Users >
Members > Registration**, which holds the field-by-field table.

> **Tip:** Every parameter on the **Options** screen is listed in the generated
> [Members configuration reference](../../reference/configuration/components/members.md).
> [Configuring Registration](../05-configuring/02-registration.md) covers the
> same field table from the configuration side.

## Turning registration off

To stop visitors creating their own accounts, open **Users > Members**, press
**Options** in the toolbar, and set **Allow User Registration** — the first
setting on the **Component** tab — to **No**. Administrators can still create
accounts from the Members list.

## What a new account has to pass

**New User Account Activation**, also on the **Component** tab, sets the gate.

| Setting | What happens on registration |
|---|---|
| **None** | The account is confirmed straight away and can log in. |
| **Self** (the default) | The user is emailed a confirmation link. Until it is followed the account shows as **Unconfirmed**. |
| **Admin** | The user is emailed a confirmation link *and* the account is left unapproved. An administrator has to approve it as well. |

Two related settings sit beside it. **Email On Account Activation** emails the
user when an administrator approves their account, and only applies under
**Admin**. **Notification Mail to Administrators** emails the site's
administrators when an account is created, and only applies under **None** or
**Self**.

Accounts waiting at either gate are held on a holding page by the **System -
Unconfirmed** and **System - Unapproved** plugins. Find them by filtering the
Members list on **- Email confirmed -** or **- Approved -**; see
[Account states](01-members/README.md#account-states) for how to clear each
gate.

## The registration fields table

1. Open **Users > Members**.
2. Click **Registration** in the sub-menu. It opens on **Config**.
3. Set each field's state in each of the four columns.
4. Press **Save**. Changes take effect immediately.

Each row is one field, and each column is one situation in which the hub might
ask for it.

| Column | When it applies |
|---|---|
| **Create Account** | The public registration form. |
| **Proxy Create Account** | Creating an account on someone else's behalf. |
| **Update on Next Login** | The prompt an existing user gets at their next login when something now required was not asked for when they registered. |
| **Edit Profile** | The user editing their own profile. |

Each cell takes one of four values.

| Value | Meaning |
|---|---|
| **Required** | Must be filled in. |
| **Optional** | May be filled in. |
| **Hide** | Not shown. |
| **Read only** | Shown but not editable. |

A cell showing **n/a** instead of a menu is one the field does not support.

The rows are the nine account-level fields: **Username**, **Password**,
**Password Confirmation**, **Full Name**, **Email**, **Email Confirmation**,
**OptIn**, **CAPTCHA** and **TOU** (the terms of use). Everything else the form
asks for comes from the profile builder, where each field carries its own
required flag and access level; see
[Building the profile form](01-members/README.md#building-the-profile-form).

**Update on Next Login** is what makes an existing account fill in a gap. On
every login the session is flagged as incomplete, the **Members - Profile**
plugin re-checks the profile against this column, and the flag clears the
moment the check passes. So setting a field to **Required** here puts it in
front of every logged-in user until they answer it. Setting **TOU** to **Required** in this
column is exactly what the Members list's **Reset terms of use agreements for
all users** button does.

> **Note:** Nothing in this release passes the **Proxy Create Account** column
> to the registration check — no screen creates an account "by proxy". The
> column is stored and displayed, but it currently has no effect.

## Incremental Registration

The second link under **Registration** configures incremental registration:
rather than asking for everything at once, the hub prompts for a package of
profile fields some time after the user registers.

| Setting | Notes |
|---|---|
| **Pop-over text** | The text shown in the prompt. |
| **Award per field completed** | Points awarded for each field the user fills in. |
| **Test group (name or id number)** | Restricts the prompting to one hub group while you try it out. |
| **Field groups** | Each group says *beginning N hours / days / weeks after registration, prompt for* a list of profile fields. |
| **Recurrence** | How long to wait before asking again after each press of *ask me later*. |

## PREMIS Data Import

The third link takes a PREMIS registration dump file and imports it. Choose the
file and press **Import**; the next screen reports how many records were
processed and lists any errors.

> **Note:** This screen's toolbar carries **New**, **Edit** and **Delete**
> buttons that have no matching task. Pressing one just reloads the screen.

## Customising the confirmation email

The registration emails are ordinary component layouts, so they are overridden
the same way as any other view: copy the file into your template's `html`
directory and edit the copy. That keeps your wording out of the core tree,
where an upgrade would overwrite it.

The originals live in
[`core/components/com_members/site/views/emails/tmpl/`](../../../core/components/com_members/site/views/emails/tmpl/).
The ones that matter for registration are:

| Layout | Sent when |
|---|---|
| `confirm.php`, `confirm_html.php` | A new account needs its email address confirmed. |
| `create.php`, `create_html.php` | An account is created on the site. |
| `admincreate_plain.php`, `admincreate_html.php` | An administrator creates an account. |
| `approved_plain.php`, `approved_html.php` | An administrator approves an account. |
| `update.php`, `updateproxy.php`, `adminupdate.php`, `adminupdateproxy.php` | An account's details change. |

Each email has a plain-text and an HTML layout, and both are sent. Override
them at
`app/templates/<your template>/html/com_members/emails/<layout>.php`.

> **Note:** The confirmation email carries the account's username and the
> confirmation link. It does **not** carry a password.

## Logging in through another service

A hub can accept logins from other identity providers as well as its own.
Each provider is an authentication plugin.

| Plugin | Authenticates against |
|---|---|
| **Authentication - HUBzero** | The hub's own accounts. This is the default and should stay enabled. |
| **Authentication - Certificate** | A client-side TLS certificate. |
| **Authentication - CILogon** | CILogon. |
| **Authentication - Email Token** | A one-time token emailed to the user. |
| **Authentication - Facebook** | Facebook. |
| **Authentication - Globus** | Globus. |
| **Authentication - Google** | Google. |
| **Authentication - LinkedIn** | LinkedIn. |
| **Authentication - ORCID** | ORCID. |
| **Authentication - Purdue University CAS** | Purdue's CAS. |
| **Authentication - SciStarter** | SciStarter. |
| **Authentication - Shibboleth** | A Shibboleth federation. |
| **Authentication - Twitter** | Twitter. |

Most of them need credentials from the provider, which you get by registering
your hub as an application on their side and choosing the web application type.
Then:

1. Open **Extensions > Plug-in Manager**.
2. Search for the plugin.
3. Click its name and enter the client key and secret it asks for.
4. Set **Status** to **Enabled** and **Save & Close**.

A user arriving through one of these providers gets a partially built account —
it shows in the Members list as **Incomplete**, with an email address ending in
`@invalid` — and the **System - Incomplete** plugin holds them on the
registration form until they finish it.

## TLS certificate authentication

Certificate login uses two plugins.

**Authentication - Certificate** checks the certificate and ties it to an
account. If the certificate is already linked, the user is logged straight in.
If not, they may create an account, which links the certificate to it, or link
it to an account they already have by supplying that account's password.

**System - Certificate** makes a certificate compulsory. With it enabled a
certificate must be present to browse the site at all, and certificate login
becomes the only option. Enable it the same way as the authentication plugin.

Two things have to be right outside the CMS. Apache needs
`SSLVerifyClient optional`, so that a certificate is accepted but its absence
is handled by the routing plugin rather than by Apache, and `SSLOptions
+StdEnvVars`, so the certificate's fields reach PHP. The site must also be
forced to SSL in the global configuration.

Pairing certificate login with **New User Account Activation** set to **Admin**
gives you a user-initiated but administrator-gated flow: the user links their
own certificate to a new account, and an administrator approves it before it
works.

> **Note:** Every user needs a TLS certificate installed in their browser
> before this is usable.
