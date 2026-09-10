<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: stale
source: https://help.hubzero.org/documentation/240/users/registration
source-id: 3324
modified: 2014-11-14
imported: 2026-09-09
-->
# Registration

Registering creates a member account: a username, a password, an email
address the hub can reach you at, and whatever else your hub asks for. Once
the account is confirmed you can contribute content, join groups, and run
tools.

Read this page if the form asked you something you did not expect, or if you
registered and the account still will not work. If the form is short and
obvious on your hub, fill it in and go to
[Getting started](30-gettingstarted.md) instead.

The form is longer than a sign-up form usually is, and for a reason. A hub is
a place where work is attributed: your name goes on the datasets you release,
the questions you answer, and the tool sessions the hub bills to a quota. The
extra questions — organisation, discipline, position — are what let a group
manager recognise the right person before letting them at the group's files.

No two hubs ask exactly the same questions. The fieldsets below are the ones
built into the software; the **Personal Information** fieldset in the middle
is entirely the hub's own, and any of the standard fields can be made
required, optional, read-only, or hidden. Administrators set that up on the
**Users > Members > Registration** screen, described in the
[Registration chapter](../managers/06-users/02-registration.md) of the Hub managers
book.

> **Note:** The images on this page were captured from an older release. The
> first one shows a member dashboard rather than the registration form and is
> misleading; ignore it until the screenshots are recaptured.

## Creating an account

![A member area, captured for an older release](media/registration-210registration.png)

1. Go to the hub's front page and select **Register**, or go straight to
   `https://yourhub.org/register`. The login form also carries a
   **Create an account** link, which goes to the same place.
2. If the hub accepts logins from other services — Google, ORCID, Globus,
   Shibboleth and others are all possible — a **Connect With** panel appears
   at the top of the form. Using one fills in part of the form for you and
   links that account to your new hub account.

   Signing in this way does not skip the rest of the form. It saves typing and
   gives you one fewer password to keep; see
   [Third-party logins](#third-party-logins) below for what it leaves
   unfinished.
3. Under **Login Information**, type a **Username**. The hint under the box
   says what is allowed: *Combination of lowercase letters and numbers. No
   spaces or punctuation.* When you leave the box, the hub checks the name
   against the accounts that already exist and reports underneath whether it
   is available. You do not have to submit the form to find out.

   > **Note:** Usernames cannot be changed afterwards.

   Choose one you would not mind a co-author seeing. On hubs that offer SSH or
   SFTP access for tool development, the same username is your login there;
   the **Account** tab shows it as your local services username.
4. Type a **Password**, then repeat it in **Confirm Password**. The list of
   rules under the boxes is your hub's own — it is a table an administrator
   edits, so its length and content vary. The list updates as you type, marking
   each rule as it is satisfied. Work down the list until nothing is left
   flagged; the form rejects the password otherwise, and it will tell you which
   rule failed.
5. Under **Contact Information**, fill in **First Name**, **Middle Name**, and
   **Last Name**. Middle name is never required.

   This is the name that appears on anything you contribute, so type it as you
   would want it cited.
6. Type your address in **Valid E-mail** and again in **Confirm E-mail**. A
   warning under the boxes names the address the confirmation message will
   come from.

   > **Important:** The address has to work. Unless the hub has switched
   > confirmation off, the account cannot be used until you follow a link sent
   > to it.

   ![The Contact Information fieldset of the registration form](media/registration-210registration2.png)

   If the address already belongs to an account, the form says so and offers
   two buttons instead: one that emails you the existing account's details, and
   one that opens a support ticket asking for that account's resource limits to
   be raised. That second button is there because the usual reason to register
   twice is running out of disk or session quota, and a second account is not
   the fix.
7. Answer the questions in **Personal Information**. These are your hub's own
   fields — organisation, discipline, position, and so on. Fields marked
   *required* have to be answered; the rest can be left blank and filled in
   later from your profile. Some fields reveal further questions depending on
   the answer you give.
8. Under **Receive Email Updates**, tick or clear the box beside *Would you
   like to receive email updates (newsletters, etc.)?* It is ticked by
   default. It governs newsletters only — it does not stop the confirmation
   message, or the notifications a group or project sends you.
9. Complete the **Human Check**. Its contents depend on which CAPTCHA plugin
   the hub runs: an image of distorted characters to retype (with a
   *Letters not clear? Refresh CAPTCHA.* link), an arithmetic question, or
   Google reCAPTCHA.

   > **Note:** This fieldset also contains a box labelled *Please leave this
   > field blank.* That is a trap for automated form fillers. Leave it empty,
   > as it says. If a password manager or browser autofill has put something in
   > it, clear it, or the form is rejected as a bot.
10. Under **Terms & Conditions**, open the **Terms of Use** link, read it, and
    tick *Yes, I have read and agree to the Terms of Use.*
11. Select **Create Account**.

## What happens next

In order, and depending on how the hub is set up:

1. The account is created and the form is replaced by *Account Created!*, with
   a four-step reminder — find the email, follow the activation link, log in,
   done — and the address the message went to.
2. **If email confirmation is on**, which is the usual setting, the account
   cannot be used until you follow the link. This is the step most people are
   stuck at when they say the hub is not letting them in.
3. **If the hub also requires administrator approval**, confirming the address
   is not the end. Until someone approves the account, logging in reaches a
   page headed *Pending Approval*. Where the hub has the approval notice
   switched on, it emails you when the account is approved. Nothing you can do
   speeds it up; the page carries a link to raise a support ticket if you think
   it is wrong.
4. **If the hub has switched confirmation off**, the account works as soon as
   it is created and you can log in immediately.

## Activating the account

Follow the link in the message and log in. That confirms the address and, on a
hub that does not also require administrator approval, the account is ready.

If nothing arrives:

- The message is generated automatically and some filters treat it as spam.
  Check the spam folder first.
- Log in anyway. An unconfirmed account reaches a page headed *Email Address
  Unconfirmed*, which offers to send a new confirmation message to the address
  on the account. Failing that, contact the hub's support.
- If you mistyped the address, correct it from your profile. Changing an email
  address un-confirms the account and sends a fresh confirmation link, so you
  will have to confirm again.

> **Warning:** That last point applies whenever you change the address, not
> just while fixing a typo. Changing your email at any later date puts the
> account back into the unconfirmed state until you follow the new link, so do
> not change it to an address you cannot read.

## Third-party logins

An account created by logging in through another service starts out
incomplete: the hub does not get a usable email address from every provider,
so it holds you on the registration form until you supply the missing pieces.
Until you do, you cannot be messaged by other members and the account shows as
incomplete.

You can link further services to an existing account, and set a local
password, from the **Account** tab of your member area. A local password is
worth setting even if you always sign in through a provider: SSH, SFTP, and
tool development access use it, and a linked account cannot be removed if it
is the only way you can get in.
