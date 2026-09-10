<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Developer

The Developer component is the hub's OAuth 2.0 server and its register of API
applications. Every request to the hub's REST API carries a bearer token, and
every token was issued to an application registered here. It also serves the
hub's developer portal at `/developer`, where members register their own
applications and read the API documentation, and it answers the OAuth
callbacks that project file connectors come back to.

A manager touches it rarely, and for three reasons: to look over the
applications members have registered, to revoke an application's tokens or
reset its secret when something goes wrong, and to keep the one automatic
record it creates for itself from being deleted. It creates no content and
has no front-page presence.

## The administrator screen

**Components → Developer** has a single screen, **Developer: Applications**.
It lists every registered application with **ID**, **Name**, **State**,
**Created**, **Created By** and **Hub Account**. All but **State** and **Hub
Account** sort; there are no filters and no search box.

The toolbar offers:

| Button | What it does |
|---|---|
| **New**, **Edit** | Open the application form |
| **Publish**, **Unpublish** | Set an application's state. An unpublished application still exists but cannot complete an OAuth handshake |
| **Delete** | Remove the selected applications and everything issued to them |
| **Reset Client Secret** | Generate a new client ID and secret for the selected applications |
| **Revoke Application Tokens** | Delete every access token, refresh token and authorization code the selected applications hold |
| **Options** | Component configuration |

**Reset Client Secret** and **Revoke Application Tokens** confirm first, and
both say what they cost:

> Are you sure you want to reset the client secret for the selected
> applications? This action is irreversible and could cause the application to
> not function.

> Are you sure you want to revoke all tokens for the selected applications? It
> will force all users of the application to login again.

That is exactly right. Resetting a secret breaks the application until whoever
wrote it copies the new one out; revoking tokens signs every user of it out.
Neither can be undone.

> **Warning:** **Delete** asks nothing. It removes the selected applications
> and every token, refresh token, authorization code and team row attached to
> them, on one click. Unpublish first if you are not certain.

Reaching the screen needs `core.manage` on `com_developer`. Editing, deleting
and changing state check `core.edit`, `core.delete` and `core.edit.state`
respectively.

### The application form

**New** and **Edit** open the same form.

| Field | Meaning |
|---|---|
| **Name** | Required. What the member sees on the authorisation screen |
| **Description** | Required. Shown beside the name in the member's application list |
| **Redirect URI(s)** | Required. Where the hub sends the browser after authorisation. One per line; each must be a valid URL |
| **State** | **Unpublished**, **Published** or **Trashed** |
| **Add Team Members** | Names, usernames or email addresses of registered members, separated by commas |

**Client Id** and **Client Secret** are shown, not edited; they are generated
on the first save and changed only by **Reset Client Secret**. The team is the
set of members who may manage the application from the front end — the
creator is always on it, whether you type them in or not.

> **Important:** The redirect URI is checked exactly, not by prefix. An
> application whose registered URI is `https://example.org/cb` cannot come
> back to `https://example.org/cb/` or `https://example.org/cb?x=1`.

### The Hub Account record

One application in the list carries a marker in the **Hub Account** column and
is described as *Hub account for internal requests. DO NOT DELETE.* The hub
creates it the first time a part of the CMS needs to call its own API, and
uses its credentials for the `client_credentials`, `session` and `tool` grant
types — including the token that in-page JavaScript trades its session cookie
for. It is protected: **Delete** refuses it with *Unable to delete the hub
account*.

Nothing protects it from **Unpublish**, though: the OAuth server refuses any
client that is not published, so unpublishing this one stops the hub's own
in-page API calls until it is published again. **Reset Client Secret** and
**Revoke Application Tokens** are harmless on it — nothing outside the hub
holds its secret, and a session token is minted fresh on each page — but there
is no reason to use either. Leave the record alone.

## Options

Select **Options**. There is one setting, plus the usual **Permissions** tab.
The full list is in the
[generated reference](../../reference/configuration/components/developer.md).

**Documentation Cache Expiration** is how long, in seconds, the generated API
documentation is cached before it is rebuilt. The default is 14400 — four
hours. The documentation is assembled by reading the docblocks of every
component's API controllers, which is not cheap, so the cache matters on a
busy hub. The cache file is `app/cache/api/documentation.json`; deleting it
forces a rebuild on the next request, and so does turning the site's **Debug**
setting on, which disables the cache entirely.

## The developer portal

`/developer` is a member-facing page, reachable whether or not a menu item
points at it. It shows two panels:

- **API Development**, linking to `/developer/api`.
- **Tool Development**, linking to the [Tools](25-tools.md) component.

A third panel, **Web Development**, is commented out of the template. Its
controller and view still exist at `/developer/web`, and render the literal
text *TODO: Web Development*.

### `/developer/api`

The API home offers **Read the API Docs** and three panels: **My
Applications**, **Authorized Applications** and **New Application**.

### `/developer/api/docs`

The documentation screen, and the most useful thing the component serves. It
is generated from the source tree, not written by hand, so it always describes
the endpoints the hub actually has. The left-hand contents list has three
parts:

- **Using the API** — schema, error messages, HTTP verbs, versioning, rate
  limiting, JSON-P and expanding objects.
- **Authentication (OAuth2)** — one section per grant type the hub supports:
  web application (authorization code), user credentials, refresh token,
  session token and tool session token, then how to use the token.
- **API Endpoints** — one entry per component with an API, each expanding to
  its methods. Selecting one opens `/developer/api/endpoint/<component>`,
  which lists each method's URI, HTTP verb and parameters with their types,
  defaults and accepted values.

A logged-in reader's own active tokens are listed on those pages, so the
examples can be tried as they are read. See
[REST API](../../developers/16-api.md) in the developers book for how the
documentation is produced and how to add to it, and the
[API reference](../../reference/api/README.md) for the same endpoint list in
these pages.

### `/developer/api/applications`

Where a member manages their own applications. **My Applications** lists the
ones they created or are on the team of; **Authorized Applications** lists
other people's applications that hold a token for their account, each with
**Revoke Access**.

Opening one of their own gives a **Details** tab and a **Tokens** tab, plus
**Application Settings** for the same form the administrator screen uses. The
Tokens tab lists every access token issued against the application, by member,
with the date it was authorised and the date it expires, and offers **Revoke
Token**, **Revoke all Tokens** and **Create Personal Access Token**.

> **Warning:** A personal access token is a bearer token for the member's own
> account with a hundred-year lifetime. The confirmation says so — *A personal
> access token can be used to access your account as your username and
> password could* — and the token is displayed once and never again. Members
> who paste one into a script have created a credential that outlives every
> password change. Tell them, and use **Revoke all Tokens** on the
> application when one leaks.

A third tab, **Stats**, is commented out of the tab list; its template renders
*TODO: Application Stats*.

### The authorisation screen

When an application sends a member to `/developer/oauth/authorize`, the hub
checks the request, makes them log in if they are not, and shows one question
— *Would you like to authorize **X** to access your data?* — with **Authorize**
and **No Thanks**. There is no scope list, because the hub's OAuth server
issues no scopes: a token is all-or-nothing against the authorising member's
account.

## The OAuth callback path

Plugins are not directly routable, so this component answers the OAuth
callbacks on their behalf, at `/developer/callback/<name>`:

| Path | Belongs to |
|---|---|
| `/developer/callback/googledriveAuthorize` | Filesystem - Google Drive |
| `/developer/callback/dropboxAuthorize` | Filesystem - Dropbox |
| `/developer/callback/githubAuthorize` | Filesystem - GitHub |
| `/developer/callback/globusAuthorize` | Authentication - Globus |

The first three are the redirect URIs you register with the providers when you
set up [project file connectors](15-projects/projectfileconnect.md#the-oauth-callback);
that chapter has the registration steps. These paths are fixed — they are not
configurable and they do not depend on a menu item.

> **Note:** The Dropbox callback redirects the member to a malformed address
> after a successful authorisation. The connection is saved and works; only
> the page they land on is wrong. It is recorded, with the two plugin-side
> halves of the same fault.

## Command line

The component ships five `muse` commands — three that empty the access token,
refresh token and authorization code tables hub-wide, one that runs all three,
and one that rebuilds the API documentation cache and reports any controller
it could not parse.

None of them can be run. `muse` looks for a component's commands in
`components/com_<name>/commands`, and these are in
`core/components/com_developer/cli/commands` with a matching `Cli` in their
namespace, so every one of them is *Unknown command*. Use the **Revoke
Application Tokens** button on the administrator screen instead, which is
per-application rather than hub-wide, and delete
`app/cache/api/documentation.json` to force the documentation to regenerate.
Recorded with the project.

## What does not work

- `/developer/web` and `/developer/tools` render *TODO: Web Development* and
  *TODO: Tool Development*. Nothing links to either: the Web Development panel
  is commented out, and the Tool Development panel points at `com_tools`
  instead.
- `/developer/api/console` and `/developer/api/status` render *TODO: API
  Console* and *TODO: API Status*. Both are commented out of the API home, so
  nothing links to them.
- The **Stats** tab on an application renders *TODO: Application Stats* and is
  commented out of the tab list.

None of these are configuration mistakes; they are unfinished screens. Do not
promise anyone an API console or a status page.
