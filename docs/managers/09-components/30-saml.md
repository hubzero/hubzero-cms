<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# SAML

The SAML component makes the hub a SAML **identity provider**. Other
services — a wiki, a survey tool, a departmental application — can be
registered with it and then let people sign in using their hub account,
without ever seeing a hub password. It is single sign-on pointing outward
from the hub.

It is not how members sign in to the hub. Signing people in from an outside
identity is an authentication plugin's job; see
[Authentication](../05-configuring/06-authentication.md) and
[External authenticators](../05-configuring/07-extauth.md). Those chapters
cover CILogon, Shibboleth, Globus and the rest, and a hub that wants campus
logins configures one of them, not this component. The two are independent:
a hub can do both, either, or neither.

Do not enable this component because a service says it wants "SAML login".
That phrase usually means the service wants to *be* an SP against your
campus IdP, which has nothing to do with the hub. This component is only for
the case where the hub itself is the account authority.

The hubs that want this have a service sitting next to them and no account
system of their own. A group runs a small survey application on a departmental
server and wants the hub's four hundred members to use it without a second
password, and without the group keeping a password list. Registering that
application here is the answer. A hub with no such service needs nothing on
these screens.

## What a manager sets up

1. Put an X.509 signing certificate and private key on the web server and
   point **Options** at them.
2. Register each service that will use hub accounts, either by hand or by
   importing its metadata.
3. Give the service the hub's metadata URL so it can trust the hub back.

**Enable IdP** ships switched on, so a fresh hub nominally has an identity
provider. Nothing is exposed by that. The endpoints return a 404 until a
readable certificate and key are in place, and the trust store starts empty, so
there is no service the hub would answer for. Putting the certificate in place
is the point at which this component starts doing anything.

### Registering a service

Following the example above, with the survey application at
`https://survey.example.edu`:

1. Deal with the certificate first. Open **Components → SAML** and read the
   **Overview** screen: the **Signing certificate** row must say the files are
   readable, and the expiry must be in the future. Nothing below works until
   it does.
2. Ask whoever runs the service for its SAML metadata — a URL or a file.
   Almost every service can produce one, and it saves you transcribing an
   entity ID and a certificate by hand.
3. Go to **Service Providers** and press **Import from metadata**. Paste the
   XML or give the `https` URL. The form fills in with what the metadata
   claims; nothing is saved yet, so it is safe to do this just to look.
4. Check the **Assertion Consumer Service URL**. This is the address signed
   responses are posted to, it must be `https`, and it is the one field where
   a mistake matters — the hub will only ever deliver an assertion there.
5. Under **NameID & attribute policy**, choose the **NameID value** the
   service expects to identify people by. Ask; do not guess. Changing it later
   makes the service treat returning members as new people.
6. Decide **Allowed groups**. Left empty, every member of the hub can sign in
   to that service. If the survey is for one group, put that group's `cn` here.
7. Save, with the state set to enabled.
8. Give the service the hub's metadata URL from the **Overview** screen, and
   ask them to try a sign-on.
9. If it fails, go to **Sessions**. A row means the hub answered and the
   problem is at the far end. No row means the hub rejected the request, and
   the error the member saw says why.

Registering a service is reversible: disabling or deleting the registration
stops that service working and changes nothing else on the hub. The **NameID
value** is the one field to get right first time.

## The screens

**Components → SAML**, gated on `core.manage` for `com_saml`. Three
sub-navigation entries.

### Overview

A status page, with nothing to edit. It reports:

- **Identity Provider** — whether the IdP is **Enabled**, the **Entity ID**
  the hub asserts as, and the three published endpoint URLs: **Single
  sign-on URL**, **Single logout URL**, and **Metadata**. The metadata URL
  is a link; open it to see exactly what a service provider will fetch. If
  the endpoint is switched off, the row says *Metadata endpoint is
  disabled* instead.
- **Signing certificate** — the certificate and key file paths, whether the
  web server can read them, the certificate's subject, and when it expires.
  It warns thirty days ahead and shouts once the certificate has expired.
  This is the row worth checking on a schedule; an expired signing
  certificate breaks every registered service at once.
- **Trust store** — a count of enabled and registered service providers, and
  a count of active SSO sessions. Both are links into the other two screens.

If neither the **IdP Entity ID** option nor the hub's **Site URL** is set,
the Entity ID row carries a warning: the identity is being derived from the
request's `Host` header, so it can vary per request. Set one of them.

### Service Providers

The trust store: the list of services allowed to ask the hub for an
assertion. Search matches the name and entity ID; the status filter is
**All States**, **Enabled**, or **Disabled**. The list shows **Name**,
**Entity ID**, **ACS host**, and **Cert expires**, with the certificate
column flagging one that has expired or is close to it.

Toolbar: **Enable**, **Disable**, **New**, **Import from metadata**,
**Edit**, **Delete**, and **Options**. Deleting asks for confirmation,
because SSO for that service stops immediately.

The edit form has four fieldsets.

**Details** — **Name**, **Entity ID** (the Issuer value the service sends),
**Description**, **Assertion Consumer Service URL** (where signed responses
are posted), and **Single Logout URL**. Both URLs must be `https`; the form
refuses plain `http`, because an assertion is a bearer credential. The
registered ACS URL is authoritative — a request that asks for delivery
somewhere else is refused.

**Trust** — **Require signed requests**, **Sign the assertion**, and the
service's **Signing certificate (PEM)**. The response signature already
covers the assertion, so only turn on assertion-level signing for a service
that asks for it. A certificate is mandatory when signed requests are
required.

**NameID & attribute policy** — **NameID value** picks what identifies the
member to that service: **Username**, **Email address**, or **Numeric user
ID**. **NameID format** declares which of those it is. **Attribute map
(JSON)** overrides the attributes released; leaving it blank releases the
default set (email, common name, given name, surname) and entering `[]`
releases none. **Allowed groups** takes a comma-separated list of group
`cn`s; leave it empty to let every hub member in.

**Publishing** — the enabled/disabled state, plus the ID, creation date, and
the metadata URL an imported registration came from.

**Import from metadata** takes the service's `EntityDescriptor` XML, pasted
in or fetched from an `https` URL, and prefills the edit form with the entity
ID, endpoints and signing certificate it finds. Nothing is saved until you
save the form, so it is safe to use just to see what a service is claiming.
If a registration with that entity ID already exists, the import opens it for
editing rather than creating a second one.

### Sessions

The audit trail: one row per sign-on the hub has issued, filterable by
service provider and by **Active** or **Ended**. It shows the member, the
service, the state, and when the session started and ended.

**Delete expired** clears the rows that can never resolve — those marked
ended, plus those still marked active whose hub session no longer exists.
Sessions backed by a live hub session are left alone. On a hub that does not
keep sessions in the database, the screen says so and **Delete expired**
falls back to removing only the rows already marked ended.

> **Warning:** A session row is also what records that a particular sign-on
> request has already been answered. Deleting rows removes that replay
> protection along with the audit entry. Use **Delete expired** rather than
> selecting rows by hand.

## What the endpoints do

Three site addresses, all under `/saml/idp/`:

| Address | Purpose |
|---|---|
| `/saml/idp/metadata` | The IdP metadata document. This is the URL you give a service provider. |
| `/saml/idp/login` | SP-initiated single sign-on. |
| `/saml/idp/logout` | SP-initiated single logout. |

There is nothing at `/saml` itself; it returns a 404, as does every endpoint
when **Enable IdP** is off or the signing certificate and key are unreadable.

A sign-on runs like this. The service posts or redirects a request to
`/saml/idp/login`. The hub checks the whole request first — that the issuer
is a registered, enabled service provider, that the signature verifies, that
the request has not already been answered, and that it asks for delivery to
the registered ACS URL — and rejects anything that fails with a plain error
page. Only then, if the visitor is a guest, are they sent to the hub's login
form and returned here afterwards. If the service has **Allowed groups** set
and the member is not in one, the hub shows its own *Access not available*
page rather than a protocol error. Otherwise it records the session and posts
a signed response back to the ACS URL.

The errors a member might see, all rendered on the hub's ordinary error page,
are listed in
[`site/language/en-GB/en-GB.com_saml.ini`](../../../core/components/com_saml/site/language/en-GB/en-GB.com_saml.ini).

## Options

**Options** on any of the three screens; visible with `core.admin`. Full
parameter list in the
[generated reference](../../reference/configuration/components/saml.md).

**Enable IdP** is the master switch for all three endpoints, and it ships on.
Turning it off is the fastest way to stop every registered service at once,
which is what you want if a signing key is thought to have leaked. **IdP
Entity ID** is what the hub asserts as, defaulting to the hub root URL.
**Enable metadata endpoint** serves the metadata document and also ships on;
turn it off only if you intend to hand service providers a metadata file out
of band.

> **Warning:** Changing **IdP Entity ID** on a hub with registered services
> breaks all of them at once. The entity ID is the name every service provider
> has recorded as the identity it trusts, so a hub that changes it has to have
> every service re-import the metadata. Set it before you register anything,
> or leave it alone.

**Signing certificate file** and **Private key file** are absolute paths on
the web server — `/etc/saml/cert/saml.crt` and `/etc/saml/cert/saml.pem` by
default — with **Private key passphrase** if the key has one. The web server
user must be able to read both, and only it.

**Assertion lifetime (seconds)** and **Clock skew (seconds)** set how long an
issued assertion is valid and how much clock difference is tolerated;
180 and 60 by default. **Signature algorithm** offers RSA-SHA256, RSA-SHA384,
and RSA-SHA512.

## Permissions

The component's `access.xml` declares `core.admin`, `core.manage`,
`core.create`, `core.delete`, `core.edit`, and `core.edit.state`, and all six
are genuinely used — `core.manage` to open the component, `core.create` to
add or import a service provider, `core.edit` and `core.edit.state` to change
one, `core.delete` to remove one or to clear sessions, `core.admin` for
**Options**.

What is missing is anywhere to set them. `config.xml` has no permissions
fieldset, so **Options** shows no **Permissions** tab, and none of the six
can be granted or denied on this component. They fall back to whatever the
group inherits from the global configuration, which in practice means the
screens are open to any group with global `core.manage` and closed to
everyone else. `com_system` and `com_search` have the same gap; it is
Recorded with the project.
