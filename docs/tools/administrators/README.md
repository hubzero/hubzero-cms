<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tool-administrators
source-id: 3563
modified: 2025-01-31
imported: 2026-09-09
-->
# Tool administrators

Maintaining and updating the environments that tools run in. Three of these
jobs are done in three different places, and each needs its own access.

| Task | Where it is done | What you need |
|---|---|---|
| [Directory parameter whitelist](whitelistdirectories.md) | The hub's `/administrator` interface | `core.manage` on `com_tools` |
| [Installing tool dependencies](installing-tool-dependencies.md) | A workspace session, as the `apps` account | Membership of the host's `apps` group; the Docker parts also need a login on an execution host and membership of its `docker` group |
| [Jupyter notebooks](jupyter-notebooks.md) | A workspace or Jupyter session, as the `apps` account | Membership of the host's `apps` group |

## What these pages can be relied on for

The tool execution platform — the execution hosts, the middleware, the
container images, the shared `/apps` filesystem, and the scripts the pipeline
runs — is separate software and is **not in this repository**. Only the CMS
side is: `com_tools`, `com_developer`, and the `novnc` tool plugin.

So the pages in this section are two different kinds of material:

- **Checked against the code.** The directory parameter whitelist, and the
  CMS-side notes marked as such on the Jupyter page. These describe options
  and behaviour in `com_tools` and were verified.
- **Platform material, not verifiable here.** Everything about Docker images,
  the `use` infrastructure, Hapi scripts, `/apps/share64`, and Anaconda
  environments. It is kept because it is the only written record of these
  procedures, but nothing in it could be confirmed against this repository.
  Each page says which of its sections are in this category.

## Two things called `apps`

The name turns up on both sides of the split and means something different on
each.

- On the hub, `com_tools`' **Admin Group** option names a Hubzero group whose
  members get the pipeline's administrator controls. It defaults to `apps`.
  See [Tools](../../managers/03-maintenance/02-tools.md) in the hub managers
  book.
- On an execution host, `apps` is the operating system account that owns
  installed tool dependencies, and the `apps` group is what lets you become
  it with `sudo su - apps`.

Being in one does not put you in the other.
