<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: stale
source: https://help.hubzero.org/documentation/240/managers/configuring/registration
-->
# Registration

A hub decides for itself what a new member has to supply. One hub asks for
an organisation and a phone number; another asks only for a username, a
password, and an email address. The **Registration** screen in the Members
component sets that, field by field, and separately for each of the four
moments a member's details are collected.

Most hubs open this screen twice: once when they decide the sign-up form is
asking for too much, and once when they decide it is asking for too little.
If you are happy with what the form asks for today, you can stop reading
here — the shipped settings are reasonable, and everything on this screen is
reversible.

## What this screen is not

It is not the switch that turns registration on and off. That is **Allow User
Registration**, on the Members component's **Options**; see
[Turning registration off](#turning-registration-off).

It is not where the *profile* fields live either. The nine rows here are the
fixed set the account itself needs — username, password, name, email, opt-in,
CAPTCHA, terms of use. The organisation, department and interests further
down a member's profile come from the profile builder, which is the
**Profile** button in the Members account-list toolbar; see
[Members](../06-users/01-members.md#building-the-profile-form).

And it decides only whether a field is *asked for*, never who may join. Who
may join is [access groups](../06-users/06-accessgroups.md) and the approval
setting.

## Reaching the screen

1. Sign in to the administrator interface.
2. Choose **Users > Members** from the menu.
3. Select **Registration** in the sub-navigation under the toolbar.

The Registration screen has its own tabs: **Config** (this screen),
**Incremental Registration**, and **PREMIS Data Import**.

## The field table

The table has one row per field and four columns, one per action.

| Column | What it controls |
|---|---|
| **Create Account** | What a visitor sees on the registration form. |
| **Proxy Create Account** | What an administrator sees when creating an account on someone else's behalf. |
| **Update on Next Login** | What an existing member is asked for the next time they sign in. Use it when a field that used to be optional becomes required: set it to **Required** here and members are asked for it once. |
| **Edit Profile** | What a member sees and can change on their own profile. |

Each cell is a drop-down with four choices:

| Choice | Meaning |
|---|---|
| **Required** | The field is shown and must be filled in. |
| **Optional** | The field is shown and may be left blank. |
| **Hide** | The field is not shown. |
| **Read only** | The field is shown but cannot be changed. |

A cell that reads **n/a** means the field does not apply to that action; the
setting is fixed and there is no drop-down.

The rows are the nine fields the Members component declares:

| Row | Default |
|---|---|
| **Username** | Required, Required, Read only, Read only |
| **Password** | Required, Required, Read only, Read only |
| **Password Confirmation** | Required, Required, Read only, Read only |
| **Full Name** | Required, Required, Read only, Read only |
| **Email** | Required, Required, Read only, Read only |
| **Email Confirmation** | Required, Required, Read only, Read only |
| **OptIn** | Hide, Hide, Hide, Optional |
| **CAPTCHA** | Required, Hide, Hide, Hide |
| **TOU** | Required, Hide, Required, Hide |

The four letters are stored as one string per field, in the column order
above: `R` required, `O` optional, `H` hidden, `U` read only. The stored
values are listed in the
[Members component reference](../../reference/configuration/components/members.md#registration).

<!--include: core/components/com_members/config/config.xml:329-340-->

Those shipped defaults are sensible for a hub that anyone may join, and most
hubs never change them. The **Create Account** column is the visible sign-up
form; **Edit Profile** is what a member can change afterwards; **Proxy Create
Account** is the form an administrator fills in for somebody else.

### The column that catches people out

**Update on Next Login** is not a form a member chooses to visit. It is a
gate. Whenever a member's stored details do not satisfy this column, the hub
holds them there until they do — they cannot reach the rest of the hub in
between. That is exactly what you want when a field genuinely has to be
collected from everybody, and exactly what you do not want by accident.

Two kinds of member meet it:

- Someone who signed in through an external provider and has no hub account
  details yet is sent straight to the registration form to finish the
  account.
- An ordinary member whose record is missing something this column marks
  **Required** is held on their own profile page until they fill it in.

The shipped settings put only **TOU** — the terms of use — in that position,
which is why a hub that has never touched this screen still asks every
returning member to accept the terms once.

> **Warning:** Setting any row's **Update on Next Login** to **Required**
> interrupts every member on the hub at their next sign-in, all at once, with
> no way to skip. On a hub with a few thousand members that is a support
> queue. Change it only for something you would be willing to stop people at
> the door for, and tell the hub it is coming first.

## Adding the newsletter opt-in to the sign-up form

Say the hub has started sending an occasional announcement mail and wants new
members to be able to say yes at the moment they join, rather than having to
find the setting in their profile afterwards. **OptIn** ships as **Hide** on
the sign-up form and **Optional** on the profile, so today nobody is offered
it until they go looking.

1. Choose **Users > Members** from the administrator menu.
2. Select **Registration** in the sub-navigation under the toolbar.
3. Find the **OptIn** row.
4. In the **Create Account** column, change **Hide** to **Optional**. Leave
   the other three columns alone — in particular leave **Update on Next
   Login** on **Hide**, or every existing member is stopped at their next
   sign-in.
5. Select **Save & Close**.

The next visitor to open the sign-up form sees the opt-in, and may leave it
unticked. To undo it, set the same cell back to **Hide**; nothing is stored
against the members who answered in the meantime beyond their own preference.

> **Note:** **Required** on this row does not mean "must agree". It means the
> question must be answered one way or the other before the form will submit;
> a plain No passes. **Optional** is still the kinder setting, because it
> lets someone finish signing up without reading the question at all.

## Saving

The toolbar has **Options**, **Save & Close**, and **Cancel**. **Save &
Close** writes the settings and returns you to the members list; there is no
apply-and-stay button on this screen. Changes take effect immediately, and
the site cache is cleared as part of the save.

## Turning registration off

Registration as a whole is switched on the Members component's options, not
on this screen. Select **Options** in the toolbar and set **Allow User
Registration** to **No**. This is the setting a hub reaches for when a spam
wave starts producing junk accounts faster than anyone can delete them: it
closes the door without touching the accounts that already exist, and turning
it back on restores the form exactly as it was.

The same screen carries **New User Registration Group**, **New User Account
Activation**, **Send Password**, and **Simple Registration**, which submits
the registration form on behalf of an account arriving from an external
authentication provider instead of showing it, where the provider supplied
enough to fill it in.

> **Note:** The tabs in the Options pop-up are labelled from language
> strings that the Members component does not define, so three of them
> render as raw keys such as `COM_CONFIG_REGISTRATION_FIELDSET_LABEL`
> instead of a name. The fields inside them are correct.

> **Warning:** **Send Password** on that screen is inert. It is declared, it
> has help text, it defaults to **Yes**, and nothing in the tree reads it.
> Setting it either way changes nothing, so do not treat it as the hub's
> answer to whether passwords are mailed out.

## Confirmation return URL

By default a member who confirms their email address lands on a thank-you
page. To send them somewhere else, select **Options** on any Members screen
and set **Confirmation Return URL**. It sits in the same group as the
registration fields.

> **Note:** This is only the page shown after email confirmation. Where a
> member lands after signing in is set on the login menu item; see
> [Global configuration](01-hub.md#where-members-land-after-signing-in).

## Customising the registration emails

The mails the hub sends during registration are ordinary view layouts, so a
template can override them without anyone editing core files. They live in
`core/components/com_members/site/views/emails/tmpl/`:

| Layout | Sent when |
|---|---|
| `create.php`, `create_html.php` | An account is created. This is the confirmation mail, sent as a plain-text and an HTML part. |
| `confirm.php`, `confirm_html.php` | A member asks for the confirmation mail again. |
| `update.php`, `updateproxy.php` | A member changes their email address. |
| `adminupdate.php`, `adminupdateproxy.php` | Administrators are told about a profile change. |
| `approved_html.php`, `approved_plain.php` | An account is approved. |
| `remind_html.php`, `remind_plain.php` | A member asks for a username reminder. |
| `reset_html.php`, `reset_plain.php` | A member asks for a password reset. |

To override one, copy it to the site's home template under
`html/com_members/emails/` and edit the copy:

```
app/templates/{YourTemplate}/html/com_members/emails/create.php
app/templates/{YourTemplate}/html/com_members/emails/create_html.php
```

Create the directories if they do not exist. An override in one template is
not visible from another. Mail overrides are resolved from the site's home
template rather than the active one, so they also apply to mail sent from
cron.

> **Tip:** See [Output overrides](../../developers/11-templates/09-overrides.md)
> for the general rules on overriding layouts and CSS.

## Older screenshots

> **Note:** The screenshots below came from the imported version of this
> page. The field table and the Save button are still where they show them,
> but the administrator template has been restyled since. The last four
> images show the retired **Register** component and its parameters pop-up,
> which no longer exist; the settings they show are now on the Members
> component's **Options** screen.

![The member field configuration table with its Create, Proxy, Update, and Edit columns](../media/registration-edit-registration-03.png)

![The Save button in the upper right of the Registration screen](../media/registration-edit-registration-04.png)

![The retired Components menu with the Register component selected](../media/registration-edit-registration-01.png)

![The Parameters button in the retired Register component's toolbar](../media/registration-edit-registration-05.png)

![The retired Register parameters pop-up showing the Confirmation Return URL option](../media/registration-edit-registration-06.png)

![The retired Register parameters pop-up with the Confirmation Return URL filled in](../media/registration-edit-registration-07.png)
