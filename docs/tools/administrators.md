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
| [Directory parameter whitelist](#directory-parameter-whitelist) | The hub's `/administrator` interface | `core.manage` on `com_tools` |
| [Installing tool dependencies](#installing-tool-dependencies) | A workspace session, as the `apps` account | Membership of the host's `apps` group; the Docker parts also need a login on an execution host and membership of its `docker` group |
| [Jupyter notebooks](#jupyter-notebooks) | A workspace or Jupyter session, as the `apps` account | Membership of the host's `apps` group |

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
  See [Tools](../managers/03-maintenance/02-tools.md) in the hub managers
  book.
- On an execution host, `apps` is the operating system account that owns
  installed tool dependencies, and the `apps` group is what lets you become
  it with `sudo su - apps`.

Being in one does not put you in the other.
## Installing tool dependencies

Tools often need software that is not in the base image. This page describes
the two places it can go: the container image the tool session runs in, and
the shared `/apps` filesystem managed with the `use` command.

> **Important:** Everything on this page happens on the tool execution
> platform — the container images, the execution hosts, the `apps` account,
> and the `/apps` filesystem. That platform is separate software and is **not
> in this repository**, so none of these procedures could be checked against
> code. They are kept as the written record of how hubs have done this. The
> CMS side of tool administration is the
> [directory parameter whitelist](#directory-parameter-whitelist) and the pipeline
> described in [Tools](../managers/03-maintenance/02-tools.md).

An environment is usually shared by several tools. Anything installed here
affects all of them, so work through the options in the order below and
prefer the one with the smallest blast radius.

### Installing an operating system package in the container

Tool execution containers are built from Docker images. A hub ships one or
more base images; the operating system is not fixed, and Debian, CentOS, and
Ubuntu images have all been used to meet tool requirements. Whichever is
chosen, the image has to carry the minimum set of scripts and configuration
files the middleware needs to manage container processes. Ask the hub's
platform administrators which base images that hub currently provides.

The image used for a tool is chosen by this order:

1. An image tag matching the tool name and revision.
2. An image tag matching the tool name.
3. The default image named in the middleware.

Several tags can point at one image, so covering many tools does not mean
storing many images. Images are built and modified with ordinary Dockerfiles.
If an image is not present on every execution host, the tool's **Required
hosts** setting can be used to steer sessions to the hosts that have it.

Building images needs a login on an execution host and membership of that
host's `docker` group.

### Installing software in the `use` infrastructure

The alternative is to install into `/apps` and expose it through the `use`
command. This is what to do when several versions of the same software have
to coexist — one tool needing R 3.6.3 while another needs R 4.2.1, for
example. `use` loads a named environment into the shell, so each tool's
invoke script can select the versions it wants.

> **Important:** Install dependencies as the `apps` user, from a workspace
> terminal. Not as yourself, and not from outside a workspace.

Hapi — Hubzero Apps Program Installers — is a collection of shell scripts
that download, configure, compile, and install this software, and write the
`use` `environ.d` file that goes with it. Clone it into the `apps` home
directory:

```bash
git clone https://github.com/hubzero/hapi.git
```

`hapi/scripts` holds the scripts and their CSV files. If one exists for the
software you need, run it. If not, copy the closest existing script and
change it; new scripts are welcome as pull requests to the repository, and
scripts are added there from time to time, so pull before deciding one is
missing.

Dependencies are installed under an operating-system-specific directory such
as `/apps/share64/<os>`. The rules for what ends up there:

- Owned by the `apps` user and group.
- Readable by everyone; directories searchable by everyone.
- **Never writable by everyone.**

### The `use` command

Run `man use` in a tool session terminal for the authoritative version. The
manual page reads:

```text
USE(1)                        User Commands                       USE(1)

NAME
     use, unuse - adjust the shell environment

SYNOPSIS
     use [options]... [ENVIRONMENT]
     unuse [options]... [ENVIRONMENT]

DESCRIPTION
     The use command incorporates the specified ENVIRONMENT to the current
     shell. The unuse command removes it. It optionally records the selec-
     tion persistently so that subsequent shells will use the ENVIRONMENT.
     These commands are independent of the shell being run.

     An ENVIRONMENT is specified by a configuration file of the same name as
     found in one of the configuration directories. The ENVIRON_CONFIG_DIRS
     environment variable specifies a list of directories in which to search
     for configurations. Each configured ENVIRONMENT specifies a environ-
     ment variables to set or prepend, shell variables to set, and shell
     aliases to set.

     Some environments are configured to conflict with others. The use com-
     mand will ask if conflicting ENVIRONMENT should be replaced.

     With no arguments, the use and unuse commands will print a synopsys of
     options and lists all available environments.

     -h   print available help for a named ENVIRONMENT.

     -e   environment only. Do not ask about preserving the selection.

     -p   modify the environment and preserve selection. Do not ask about
          preserving the selection.

     -k   keep any conflicting environment. Do not ask about replacing it.

     -r   replace any conflicting environment without asking.

     -x   quietly ignore the command if the named ENVIRONMENT cannot be
          found.

MAKING IT WORK
     The following command will describe an environment named xyz:

          use -h xyz

     The following command will incorporate the xyz environment preserving
     the environment for future shell invocations. It will also not over-
     ride any conflicting environments:

          use -p -k xyz

     The following command will remove the xyz environment but retain its
     use for future sessions:

          unuse -e xyz

INTERNAL OPERATION
     use and unuse are actually implemented as shell functions (or as
     aliases in the case of csh derivatives). The functions pass their
     arguments to the /etc/environ script which determines the commands that
     the shell should execute to satisfy the new environment configuration.
     The script prints these commands, the shell function receives them and
     evals them.

ENVIRONMENT CONFIGURATION FILES
     Configuration files are interpreted shell scripts. Several predefined
     functions are available to make the process automatic.

     alias NAME "Replacement"
          Set a command alias in the shell.

     conflict VARNAME
          Define an environment variable to indicate that a type of an
          ENVIRONMENT is in use. All conflicting ENVIRONMENT configura-
          tions should specify the same conflict. An ENVIRONMENT configu-
          ration may specify multiple conflicts.

     desc "A short description..."
          A short description of the ENVIRONMENT.

     help "A lengthy description..."
          A long description of the ENVIRONMENT and how to use it. This
          description will be formatted when printed.

     prepend VARNAME ADDITION
          Prepend ADDITION to the environment variable VARNAME separated
          with a colon.

     setenv VARNAME REPLACEMENT
          Set or replace the environment variable VARNAME with REPLACE-
          MENT.

     shellset VARNAME REPLACEMENT
          Set or replace the shell variable VARNAME with REPLACEMENT.
```
## Jupyter notebooks

Administering the Anaconda environments that Jupyter tools run in, and the
one setting on the CMS side that lets a tool be published as one.

The first section below was checked against `com_tools` in this repository.
Everything after it happens on the tool execution platform, which is separate
software and is **not in this repository**; those procedures are kept as the
written record but could not be verified.

### The CMS side

`com_tools` has an **Enable Jupyter** option, on by default. While it is on,
the tool registration form offers a third publishing choice, **Web
application (Jupyter, Rstudio, ...)**, alongside the Rappture or Linux-GUI
default and Sim2L. Turning the option off removes the choice from the form;
tools already registered with it keep their setting.

The choice is stored on the tool version as `publishType`, with the value
`jupyter`. Hubs upgraded from older releases may still hold the historical
value `weber=`; the component translates that to `jupyter` when it reads it,
so both behave the same. `publishType` is passed straight through to the
platform's install and publish scripts as `--publishOption`.

At launch the difference shows up in what the middleware returns. A standard
tool comes back as a VNC session that the `novnc` plugin renders in the page.
A Jupyter tool comes back with a proxy URL, and the CMS redirects the browser
to it instead. If the middleware also returns an authentication token, the
CMS sets a `weber-auth-<hub domain>` cookie — secure, HttpOnly, thirty days —
before redirecting.

For the option itself see the
[generated `com_tools` parameter reference](../reference/configuration/components/tools.md);
for the pipeline the tool travels through, see
[Tools](../managers/03-maintenance/02-tools.md) in the hub managers book.

Nothing else about Jupyter is configured in the CMS. Which Anaconda
environments exist, which packages they carry, and which kernels a notebook
can select are all decided on the execution hosts, by the procedures below.

### Adding packages to an Anaconda environment

> **Warning:** An Anaconda environment is shared by every tool that uses it.
> Adding a package can force other packages to be downgraded, and a downgrade
> can break a tool that depends on a feature the older version lacks. Read
> what conda proposes before agreeing to it. A minor downgrade is usually
> safe; a long list of downgrades, or a downgrade of anything central, is
> not — stop and create a separate environment instead.

Check the package's own installation instructions first. Many recommend a
channel other than the default.

1. Start a workspace tool. If the hub offers workspaces on more than one
   container image, pick the one matching the environment you are changing.
   Starting the Jupyter tool that uses the environment and opening a terminal
   in it works too.

2. Become the `apps` user. Your account has to be in the host's `apps` group.

   ```bash
   sudo su - apps
   ```

3. List the Anaconda environments available.

   ```bash
   use |& grep anaconda
   ```

4. Load the one you want, where `X` is the version.

   ```bash
   use -e -r anaconda-X
   ```

5. Install the package. Use `mamba` in place of `conda` if it is present — it
   is faster and says more when it fails. `pip` is the third option; mixing
   pip and conda installations in one environment needs care. Installation
   can take several minutes.

   ```bash
   conda install -c conda-forge -c defaults <pkgname>
   ```

   ```bash
   pip install -U --upgrade-strategy only-if-needed <pkgname>
   ```

6. Remove any world-writable bits the installer left behind. Do not skip
   this.

   ```bash
   chmod -R o-w /apps/share64/<os>/anaconda/anaconda-X
   ```

### Creating a separate Anaconda environment

Sometimes it is better to spawn a new named environment from the base one
than to disturb the base. Jupyter calls named environments *kernels*. A
notebook records its kernel in its own metadata, so a developer picks it once
and it sticks — which means the administrator does not have to create a
separate Jupyter tool for every kernel.

1. Load the base environment.

   ```bash
   use -e -r anaconda-X
   ```

2. Create the named environment.

   ```bash
   conda create -n <name>
   ```

3. Activate it. On current conda this is `conda activate <name>`; older
   installations need `source activate <name>`.

   ```bash
   conda activate <name>
   ```

4. Install packages as above.

   ```bash
   conda install -c conda-forge -c defaults <pkgname>
   ```

   ```bash
   pip install -U --upgrade-strategy only-if-needed <pkgname>
   ```

5. Register it as a kernel, either into the active environment's prefix or
   explicitly into the base environment's.

   ```bash
   python -m ipykernel install --sys-prefix --name <name> --display-name "Python3 (<name>)"
   ```

   ```bash
   python -m ipykernel install --prefix /apps/share64/<os>/anaconda/anaconda-X --name <name> --display-name "Python3 (<name>)"
   ```

6. Deactivate.

   ```bash
   conda deactivate
   ```

Fix world-writable files here too, as in the previous section.

Further reading:

- [Managing conda environments](https://docs.conda.io/projects/conda/en/latest/user-guide/tasks/manage-environments.html)
- [Installing the IPython kernel](https://ipython.readthedocs.io/en/stable/install/kernel_install.html#kernels-for-different-environments)

### Updating hublib

`hublib` is the Hubzero utility library for notebooks. It belongs in every
base Anaconda environment and in every named environment spawned from one. It
is distributed through pip only.

```bash
use -e -r anaconda-X
pip install -U hublib
```
## Directory parameter whitelist

A tool session can be started with file and directory names supplied in the
launch URL. The **Directory Parameter Whitelist** decides which directories
those names may point at. This page is CMS-side and was checked against
`com_tools` in this repository.

### What parameter passing does

A tool is launched at `/tools/<alias>/invoke`. A `params` query argument on
that URL carries a list of values to hand to the tool, one per line:

```text
directory:/home/hubname/username/mycase
file(input):/home/hubname/username/mycase/run.xml
int(runs):20
```

Each line is `type:value` or `type(name):value`. The three types are
`directory`, `file`, and `int`. A name in parentheses is optional; without
one, the tool receives an unnamed parameter of that type.

`com_tools` validates every line before it starts a session. Validation is
all or nothing: one bad line rejects the whole launch. If every line passes,
the component forwards the `params` text exactly as it arrived — the middleware
gets the original values, not the expanded and normalised ones the check
worked on.

### What the whitelist does

For `directory` and `file` parameters the component:

1. Expands a leading `~/` to the member's home directory. If the account has
   no absolute home directory, the launch fails.
2. Rejects any value that is not an absolute path.
3. Normalises the path, resolving `.` and `..` and collapsing repeated
   separators. A `file` value may not end in a separator, `.`, or `..`.
4. Rejects any value containing a control character or invalid UTF-8.
5. Rejects everything if the whitelist is empty.
6. Requires the normalised value to begin with one of the whitelisted
   directories.

Step 6 compares whole path elements: each entry is trimmed and given a
trailing `/` before the comparison, so `/home` matches `/home/hubname/...`
but not `/homework`. Because the check runs after normalisation, `..` cannot
be used to climb out of a whitelisted directory.

`int` parameters are not affected by the whitelist. They are checked for
control characters and then against `^[-+]?[0-9]+$`.

### Setting the whitelist

Any account with `core.manage` on `com_tools` can change it.

1. Sign in to `/administrator`.
2. Go to **Components → Tools**. The pipeline list opens.
3. Select **Options** in the toolbar.
4. On the **Defaults** tab, edit **Directory Parameter Whitelist**. It is a
   comma-separated list of absolute directories. The shipped default is
   `/home`.
5. Select **Save & Close**.
6. Repeat on every hub that needs it — production, stage, development, and so
   on. The setting is per hub, not shared.

The option is `params_whitelist`; see the
[generated `com_tools` parameter reference](../reference/configuration/components/tools.md)
for the rest of the component's settings.

> **Warning:** Clearing the field does not disable the check, it fails every
> `file` and `directory` parameter. Leaving parameter passing usable means
> keeping at least one directory in the list.

### When validation fails

A launch with an unusable parameter does not start a session. The member gets
the **Bad Parameters** page, which shows the rejected `params` text and a link
to open a support ticket. Nothing is written to the session tables.

### What is not checked here

The CMS checks the shape of the parameters and the whitelist prefix. It does
not check that the paths exist, that the member can read them, or that the
tool can do anything with them. Those are the tool platform's business, and
the platform validates the parameters again on the execution host. The
platform is separate software and is not in this repository, so its side of
the check could not be verified.

Two consequences of forwarding the original text are worth knowing:

- The middleware receives `~/` and `..` unresolved, and has to expand and
  normalise them itself. The whitelist decision was already made on the
  resolved form, so the two must agree.
- Resuming an existing session re-sends the parameters stored with it, and
  they are not checked again. Tightening the whitelist does not retract
  sessions started under the old one.

For the pipeline that gets a tool to the point of being launchable, see
[Tools](../managers/03-maintenance/02-tools.md) in the hub managers book.
