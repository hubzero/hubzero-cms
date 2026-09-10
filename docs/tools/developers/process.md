<!--
status: rewritten
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/22/toolsnewdocs/process
source-id: 2853
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# The contribution process

How a tool gets from a registration form to a published page, told from the
developer's side. This page is about the pipeline;
[What you can publish as a tool](02-overview.md) is about the program you put
through it.

> **Note:** The pipeline itself is CMS-side and was checked against
> `com_tools` in this repository. The work you do between the states — writing
> code in a workspace, building it on the execution host, launching a
> session — happens on the tool platform, which is separate software and could
> not be checked here.

## The nine states

Every tool contribution sits in one of nine states. The state decides who the
pipeline is waiting on: you, or a hub administrator.

| # | State | Waiting on | What happens next |
|---|---|---|---|
| 1 | Registered | Administrator | The administrator creates the project area and the repository |
| 2 | Created | You | Commit your code, then flip the state to Uploaded |
| 3 | Uploaded | Administrator | The administrator installs the code on the hub |
| 4 | Installed | You | Test the tool, write its information page, pick a licence, then approve |
| 5 | Updated | Administrator | You committed new code; the administrator reinstalls it |
| 6 | Approved | Administrator | The administrator publishes the tool |
| 7 | Published | — | The tool is live. Committing new code moves it back to Updated |
| 8 | Retired | — | The tool page stays as a record; the tool can no longer be run |
| 9 | Abandoned | — | You cancelled the contribution before it was published |

*(Verified: the numbers are the `state` column on `#__tool` and the names come
from `getStatusName()` in `com_tools`.)*

The administrator's half of each step — what the buttons do, what the host
scripts are called, and how the output is reported — is in
[Tools](../../managers/03-maintenance/02-tools.md) in the hub managers book.
That page also lists every field on the registration form. Read it alongside
this one; between them they cover both sides of the same pipeline.

## Registering

Start the contribution flow on the hub and choose the tool contribution type,
or go straight to `/tools/create`. The form asks for a tool alias of 3 to 15
alphanumeric characters, which becomes the tool's directory name and cannot be
changed afterwards. Everything else on the form can be changed later.

Two of the choices decide how the rest of your work looks:

**Repository Host** — where the source lives:

| Option | Label on screen |
|---|---|
| `gitExternal` | Host Git repository on GitHub, GitLab, etc. |
| `gitLocal` | Host Git repository here |
| `svnLocal` | Host subversion repository here |

`gitExternal` is the default where the hub has the component's **External
GitHub Repo** option turned on, and it adds a field for the repository URL.
Subversion is still supported but is no longer the only choice, and it is not
the default on a current hub.

**Publishing Option** — what kind of tool it is:

| Option | Label on screen |
|---|---|
| `standard` | Rappture or Linux-GUI based tool |
| `jupyter` | Web application (Jupyter, Rstudio, ...) |
| `simtool` | Sim2L |

The Jupyter and Sim2L choices appear only where the hub has turned the
matching options on. Which one you pick decides the starter invoke script the
hub writes for you; see
[Tool repository structure](01-toolrepostructure.md).

*(Verified: both option sets are read from the registration form and the
`com_tools` configuration.)*

**Register Tool** puts the contribution in the **Registered** state and opens
a support ticket for it. From then on the tool has a status page under
`/tools/pipeline`, which is where every remaining step starts.

## Writing the code

Once an administrator has moved the tool to **Created**, the project area and
its repository exist. Check the repository out — in a workspace on the hub, or
on your own machine for an external Git repository — and lay the tool out the
way the hub expects it. [Tool repository structure](01-toolrepostructure.md)
covers the directory layout and the `middleware/invoke` script every tool
needs.

The commands for the checkout, the build, and the commit are in the
[hub managers' walkthrough](../../managers/03-maintenance/02-tools.md), which
gives the Subversion form and notes the Git equivalents. Test the tool in a
workspace before you flip the state:

```console
$ cd src
$ make all install
$ cd ..
$ ./middleware/invoke -T $PWD
```

That is the same sequence the administrator runs when installing the tool, so
anything that fails here fails there too.

When the code is committed, use the **What's next?** panel on the tool's
status page to say that it is ready to install. The state becomes
**Uploaded**.

## Testing and approving

After the administrator installs the tool, the status page offers a **Launch
tool** button. Use it and run the tool the way a member would. Each time you
fix something, commit and use the status page link to move the state to
**Updated**, which asks for another install.

Before the tool can be approved it needs:

- a tool information page — the resource page with authors, credits,
  publications, and screenshots, built by a wizard the status page links to;
- a licence, chosen from the same page. Closed source needs a reason.

Then **Approve this tool**. An administrator publishes it, and it becomes a
resource page with a **Launch Tool** button like any other. See
[Tools](../../users/22-tools.md) in the users book for what members get.

## After publishing

A published tool stays published while you work on the next version. Commit
the changes and move the state to **Updated**: the published version keeps
serving members, and the install–approve–publish cycle runs again on the
development version.

You can cancel a contribution that has not been published yet, with the
**Cancel** link under **Developer Tools**. It unpublishes the draft page and
sets the state to **Abandoned**. Cancelling a published tool is refused.

## Background material

These recorded seminars are hosted on nanoHUB and are the original source for
this material. They predate the Git and Jupyter options above.

- [Bootcamp course for new developers](https://nanohub.org/resources/14671)
- [Overview of the tool development process](https://nanohub.org/resources/14668)
- [Using workspaces](https://nanohub.org/resources/3081)
- [Using Subversion for source code control](https://nanohub.org/resources/14669)
