<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/configuring/extauth
-->
# External authenticators

Single sign-on lets a member reach the hub with an identity someone else
already manages — a campus login, a federation, a research identity service.
The hub never sees or stores their password, they have one fewer to
remember, and your help desk fields fewer password resets.

This is the chapter for a hub that has to admit people it does not employ:
collaborators at another university, a funder's review panel, students on a
course run somewhere else. If everyone who uses your hub is already on your
own staff list, the social providers in
[Authentication](06-authentication.md) are easier and this chapter is not for
you.

## What this costs before it works

None of these is a fifteen-minute job, and the reason is never the hub. Every
one of them needs the hub registered with somebody else's identity service
first, and that step is out of your hands:

- CILogon reviews each registration by hand.
- Shibboleth needs `mod_shib` installed and configured on the web server, and
  the hub added to a federation's metadata.
- Purdue CAS needs the hub registered with Purdue's identity office.
- Globus needs an application registered in Globus Auth.

Start the registration before you plan the rest of the work, and expect the
plugin itself to take minutes once the credentials arrive. When they do,
enable the plugin the same way as any other — see
[Authentication](06-authentication.md).

> **Tip:** Test with a real account from the institution, borrowed for ten
> minutes from someone who has one. Every one of these plugins works by
> handing a visitor to a service you cannot log into yourself, and the
> failures — a scope that was not approved, an attribute that does not
> arrive, a callback URL that does not match — all look identical from your
> side of it. The Shibboleth plugin's **Testing mode key**, described below,
> exists so that you can do this without showing a half-finished provider to
> the whole hub.

Four of the authentication plugins federate an external identity provider
rather than talking to a single social network:

| Plugin | Federates |
|---|---|
| **Authentication - CILogon** | CILogon, which fronts hundreds of campus and laboratory identity providers. |
| **Authentication - Shibboleth** | Shibboleth and InCommon, with the hub choosing which identity providers to offer. |
| **Authentication - Globus** | Globus Auth, which fronts campus logins and Globus identities. |
| **Authentication - Purdue University CAS** | Purdue's Central Authentication Service. |

**Authentication - ORCID** is close to these: it authenticates against a
research identity rather than an institution.

All of them are enabled the same way as any other authentication plugin —
see [Authentication](06-authentication.md) — and their parameters are listed
in the
[authentication plugin reference](../../reference/configuration/plugins/authentication.md).

## CILogon

Register the hub at <https://cilogon.org/oauth2/register>:

1. Enter a **Client Name**, a **Contact Email Address**, and a home URL —
   the hub's own URL.
2. Give the callback URLs, replacing `<hostname>` with the hub's host. Add
   a set for every hub instance that will use CILogon.

   ```
   https://<hostname>/index.php?option=com_users&task=user.login&authenticator=cilogon
   https://<hostname>/index.php?option=com_users&task=user.link&authenticator=cilogon
   https://<hostname>/administrator/index.php?option=com_login&task=login&authenticator=cilogon
   ```

3. Select every scope offered: `email`, `openid`, `profile`,
   `org.cilogon.userinfo`, and the `edu.` scopes.
4. Leave **Refresh Token Lifetime** and **Issuer** blank.

CILogon reviews the registration by hand. When it is approved you are sent
a client ID and a client secret. Put them into **Client ID** and **Client
Secret** on the **Authentication - CILogon** plugin, enable the plugin, and
CILogon appears on the hub's login page.

> **Note:** The hub requests the `openid`, `email`, `profile`, and
> `org.cilogon.userinfo` scopes when it sends someone to CILogon. A
> registration that was approved for fewer scopes fails at sign-in.

## Shibboleth

The Shibboleth plugin needs `mod_shib` in front of it; the hub does not
speak SAML itself. Protect a path and hand the result back to the hub:

```apache
<Location /login/shibboleth>
    AuthType shibboleth
    ShibRequestSetting requireSession 1
    Require valid-user
    RewriteRule (.*) /index.php?option=com_users&authenticator=shibboleth&task=user.login [L]
</Location>
```

The plugin's parameters are in four groups:

| Group | What it holds |
|---|---|
| **Basic** | **Site login**, **Admin login**, **DNS Address** (default `8.8.8.8`, used to resolve visitors' hostnames so they can be matched to a participating institution), and **Auto approve new users**. |
| **Debug** | **Enable debugging**, **Debug Log** (default `/var/log/apache2/php/shibboleth.log`), and **Testing mode key**. |
| **Links** | Extra links shown alongside the institution picker. |
| **Institutions** | The federation metadata file — `/etc/shibboleth/metadata/federation-metadata.xml` by default — and which of its identity providers the hub offers. |

> **Tip:** Set **Testing mode key** while you are setting Shibboleth up.
> The provider is then hidden from the login page unless the key appears in
> the URL, as in `https://yourhub.org/login?yourkey`. Clear the field to
> show it to everyone.

## Globus

Register the hub as a Globus Auth application, then put the values into
**Client ID** and **Client Secret** on the **Authentication - Globus**
plugin. The callback URLs are the same three as CILogon's, with
`authenticator=globus`.

## Purdue CAS

The Purdue plugin needs the hub registered with Purdue's Identity and
Access Management office; see
<https://www.purdue.edu/apps/account/docs/CAS/CAS_information.jsp>. Once the
hub is registered, enable the plugin — there is no key or secret to enter.

Its parameters include **Domain** and **Display name**, which set what the
login page calls it, **End CAS Session Automatically?**, which decides
whether signing out of the hub also ends the Purdue session or asks first,
and **Passive SSO detection (CAS gateway)**.

> **Warning:** Leave **Passive SSO detection (CAS gateway)** off unless the
> identity provider is known to handle gateway and `IsPassive` requests
> correctly. Purdue's Entra ID returns a "Stale Request" error page instead
> of responding silently, which breaks login outright.

> **Note:** Other institutions running CAS can use the Purdue plugin as a
> starting point for their own. For a new OAuth provider, the Google or
> CILogon plugin is the better template.
