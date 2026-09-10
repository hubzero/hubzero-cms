<!--
status: reviewed
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tool-administrators/installing-tool-dependencies
source-id: 3564
modified: 2018-09-06
imported: 2026-09-09
-->
# Installing tool dependencies

Tools often need software that is not in the base image. This page describes
the two places it can go: the container image the tool session runs in, and
the shared `/apps` filesystem managed with the `use` command.

> **Important:** Everything on this page happens on the tool execution
> platform — the container images, the execution hosts, the `apps` account,
> and the `/apps` filesystem. That platform is separate software and is **not
> in this repository**, so none of these procedures could be checked against
> code. They are kept as the written record of how hubs have done this. The
> CMS side of tool administration is the
> [directory parameter whitelist](whitelistdirectories.md) and the pipeline
> described in [Tools](../../managers/03-maintenance/02-tools.md).

An environment is usually shared by several tools. Anything installed here
affects all of them, so work through the options in the order below and
prefer the one with the smallest blast radius.

## Installing an operating system package in the container

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

## Installing software in the `use` infrastructure

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

## The `use` command

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
