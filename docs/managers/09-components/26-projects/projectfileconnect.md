<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/projects/projectfileconnect
source-id: 3386
modified: 2017-11-03
imported: 2026-09-09
-->
# Project file connectors

A project's Files tab normally shows the hub's own storage: a git repository
under `/srv/projects`, listed under a name built from the Projects - Files
plugin's **Default Connection Name** pattern, `%s Master Repository`. A **file
connector** lets a project point at storage the hub does not own — a Google
Drive folder, a Dropbox account, a GitHub repository, an S3 bucket — and
browse it in the same interface. The files stay with the provider. The hub
stores only a name, a provider, and a credential.

Setting one up takes two people. You, as hub manager, register an application
with the provider and put its credentials into the hub. A project manager then
creates the connection inside their own project and authorises it with their
own account.

## What ships

Four external providers ship in Hubzero 2.4, each as a plugin in the
`filesystem` group. A fifth, **Filesystem - Local**, backs the hub's own
repository; it has no parameters and nothing to configure.

| Provider | Plugin | Credentials you set on the hub | Fields the project manager fills in | How it authorises |
|---|---|---|---|---|
| Google Drive | Filesystem - Google Drive | **Client ID**, **Client Secret** | — | Redirects to Google the first time the connection is opened |
| Dropbox | Filesystem - Dropbox | **App key**, **App secret** | — | Redirects to Dropbox the first time the connection is opened |
| GitHub | Filesystem - GitHub | **Client ID**, **Client Secret** | **Repository** (`vendor/repository`) | Public repositories are read anonymously; a private one needs an explicit, manager-initiated OAuth step |
| AWS S3 | Filesystem - AWS S3 | — | **Access Key ID**, **Secret Access Key**, **Endpoint Region**, **Bucket Name**, **Directory to connect** | No OAuth; the IAM key is entered per connection |

All four work: the client libraries they need are installed with the CMS. The
parameters are also in the generated
[filesystem plugin reference](../../../reference/configuration/plugins/filesystem.md).

> **Important:** The GitHub connector is read-only, and it deliberately does
> not start an OAuth handshake just to read a public repository. Only a
> private repository triggers one, and only when a project manager asks for it
> — because the token GitHub mints covers the authorising user's whole
> account, including write access.

## The OAuth callback

Google Drive, Dropbox and GitHub each need a redirect URI registered with the
provider. The hub answers them at:

| Provider | Redirect URI |
|---|---|
| Google Drive | `https://yourhub.org/developer/callback/googledriveAuthorize` |
| Dropbox | `https://yourhub.org/developer/callback/dropboxAuthorize` |
| GitHub | `https://yourhub.org/developer/callback/githubAuthorize` |

Substitute the hub's own hostname, and use `https`. The hub builds these from
its own base URL, so a provider that rejects the callback is almost always a
sign that the registered URI and the hub's actual address disagree.

## Setting up a connector

1. **Register an application with the provider.** See the per-provider notes
   below. Register the callback URI from the table above. You end up with a
   pair of values, called a client ID and secret, an app key and secret, or an
   access key and secret depending on the provider.
2. **Configure the plugin.** Go to **Extensions → Plug-in Manager**, search
   for the plugin — for example `Filesystem - Google Drive` — and open it. Set
   **Status** to **Enabled**, put the two values in the credentials panel —
   labelled **Credentials**, or **Google Drive Web Application Credentials** on
   that plugin — and select **Save & Close**. The AWS S3 plugin has nothing to
   set at this level; enabling it is enough.
3. **Optionally make Files open on the connections view.** Open the **Projects
   - Files** plugin and set **Default Action** to **Connections (view
   available connections)**. The Files tab then lists the connections instead
   of opening the local repository straight away. Leave it on **Browse (browse
   local files)** if you would rather members reach connections through the
   file browser.
4. **Create the connection in a project.** This part is done by a project
   manager, on the front end, in the project's **Files** tab: pick the
   provider from the **New Connection** drop-down, give the connection a name,
   fill in any per-connection fields, decide whether to share it with the
   project, and save. Opening it for the first time sends them to the provider
   to grant access.

> **Note:** The **New Connection** drop-down lists every provider the hub
> knows about, whether or not that provider's plugin is enabled and
> configured. A connection to a provider you have not set up is created
> happily and then fails when someone opens it. If you do not intend to offer
> a provider, say so to your project managers.

> **Note:** Only a genuine project manager can authorise a connection. A hub
> administrator viewing the project cannot do it for them, by design — the
> handshake attaches the authorising person's own provider account to a
> connection the whole project may use.

Each connection is either private to the member who created it or shared with
everyone in the project; the checkbox is on the connection form, and shared
connections are the ones without the private marker in the connections list.
The connections list also has **Refresh Connection Credentials**, for when a
stored token has expired, and **Refresh Connection Path**.

## Registering the application

The providers change their developer consoles regularly. What follows is
accurate in outline; if a screen has moved, the values you need have not.

### Dropbox

1. Go to <https://www.dropbox.com/developers> and sign in as the account that
   should own the application — usually one belonging to the group or
   institution, not to a person.
2. Select **My Apps**, then **Create app**.
3. Choose the access level to grant, and give the application a name. That
   name is what users see when they are asked to authorise it.
4. Add `https://yourhub.org/developer/callback/dropboxAuthorize` under
   **Redirect URIs**.
5. Select **Enable additional users**.
6. Copy the **App key**, reveal and copy the **App secret**.
7. Optionally use the branding tab to add your hub's icon and links; they
   appear on the authorisation screen.

> **Note:** Dropbox caps development applications at a small number of linked
> users. Apply for production status with Dropbox before you reach it, or
> members will start being refused.

### Google Drive

1. Sign in to the [Google Cloud console](https://console.cloud.google.com/)
   and select or create a project.
2. Enable the **Google Drive API** for it.
3. Under **APIs & Services → Credentials**, select **Create credentials →
   OAuth client ID**, and choose **Web application**.
4. Set **Authorized JavaScript origins** to `https://yourhub.org`, and
   **Authorized redirect URIs** to
   `https://yourhub.org/developer/callback/googledriveAuthorize`.
5. Select **Create**, and copy the **Client ID** and **Client Secret**.

### GitHub

1. In GitHub, go to **Settings → Developer settings → OAuth Apps** for the
   account or organisation that should own the application, and select **New
   OAuth App**.
2. Set the homepage URL to the hub, and the authorization callback URL to
   `https://yourhub.org/developer/callback/githubAuthorize`.
3. Copy the **Client ID**, generate and copy a **Client Secret**.

The hub asks GitHub for the `repo` scope, which is the only classic OAuth
scope that grants read access to private repositories. Tell members that, so
they understand what they are agreeing to. Public repositories never reach
this step.

### AWS S3

There is no application to register and nothing to configure on the plugin.
Create an IAM user with read access to the bucket, generate an access key for
it, and give the key, the secret, the bucket's region code and the bucket name
to the project manager, who enters them on the connection form. **Directory to
connect** confines the connection to one prefix inside the bucket; leave it
empty for the whole bucket.

> **Warning:** Those S3 credentials are stored with the connection and are
> usable by everyone the connection is shared with. Issue a key that is scoped
> to the one bucket, or the one prefix, and nothing else.
