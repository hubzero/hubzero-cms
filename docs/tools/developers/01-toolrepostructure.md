<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolrepostructure
source-id: 3535
modified: 2022-01-27
imported: 2026-09-09
-->
# Tool repository structure

The directory layout a hub expects inside a tool's source repository, and the
starter invoke script that comes with each publishing option.

> **Note:** The layout is the tool platform's convention, and the platform is
> separate software from the CMS in this repository, so it could not be
> checked here. Two things on this page were: that the hub records the tool's
> invoke script as `middleware/invoke` inside the installed version directory,
> and that the starter invoke scripts come from templates on the hub rather
> than from the CMS.

## The layout

A new tool repository starts like this:

```text
toolname/
   bin/
      .keep
   data/
      .keep
   doc/
      .keep
   examples/
      .keep
   middleware/
      invoke
   rappture/
      .keep
   simtool/
      TOOLNAME.ipynb
   src/
      Makefile
```

| Directory | What goes in it |
|---|---|
| `bin` | Executables and scripts the tool runs. Added to `PATH` automatically |
| `data` | Static data files the tool reads |
| `doc` | Documentation |
| `examples` | Example inputs for members to start from |
| `middleware` | The `invoke` script. Required |
| `rappture` | `tool.xml`, for tools that use Rappture. Otherwise leave it empty or drop it |
| `simtool` | The notebook, for tools registered with the Sim2L publishing option only |
| `src` | Source code and a `Makefile` |

The `.keep` files exist only so that Git tracks the empty directories. Delete
one once its directory has real content in it. Extra directories are fine.

`src/Makefile` must have `install`, `clean`, and `distclean` targets. `install`
puts what it builds into `bin`. `distclean` has to undo `install`, so that
running it leaves the working copy as checked out; that is how you avoid
committing build products, which cause compatibility trouble later even when
they claim to be platform independent. Commit source and binary *data*, never
compiled output.

Jupyter notebooks are commonly kept at the top level or in `bin`.

## The invoke script

Every tool needs an executable file at `middleware/invoke`. It is what the
hub runs to start a session: the CMS records the launch command for each
installed version as

```text
<invoke script dir>/<toolname>/<version dir>/middleware/invoke -T <version dir>
```

where the version directory is `dev` for the development version and
`r<revision>` for a published revision, and the invoke script directory is the
component's **Invoke Script Dir** option, `/apps` by default. So the path and
the name are not negotiable, and `-T` always arrives naming the installed
version's root directory. *(Verified against `com_tools`.)*

[Launching tools with invoke scripts](03-invoke.md) explains what goes inside
it.

Make it executable before you commit it:

```console
$ chmod 755 middleware/invoke
```

## Starter invoke scripts

When the hub creates the repository it seeds `middleware/invoke` from a
template chosen by the tool's publishing option, substituting known values
such as the tool name. The templates live with the hub's forge software, not
in the CMS; the CMS only passes the publishing option along when it asks the
host to create the repository, and only when those templates are installed.
*(Verified against `com_tools`; the templates themselves could not be
checked.)*

The three templates, as documented by the platform:

### Sim2L

```sh
#!/bin/sh

#
# Sim2L
#
/usr/bin/invoke_app "$@" -t @TOOLNAME@ \
                         -C "start_jupyter -T @tool -t @TOOLNAME@Example.ipynb" \
                         -u anaconda-X \
                         -r none \
                         -w headless
```

### Jupyter notebook

```sh
#!/bin/sh

#
# jupyter tool
#
/usr/bin/invoke_app "$@" -t @TOOLNAME@ \
                         -C "start_jupyter -T @tool -t @TOOLNAME@.ipynb" \
                         -u anaconda-X \
                         -r none \
                         -w headless
```

### Rappture or Linux GUI

```sh
#!/bin/sh

#
# standard tool
#
/usr/bin/invoke_app "$@" -t @TOOLNAME@ \
                         -C rappture
```

`@TOOLNAME@` is replaced with your tool alias. `anaconda-X` is a placeholder
for whichever Anaconda environment package your hub provides — check the hub's
`/apps/environ` directory, or ask its administrators, rather than copying a
version number from this page.

The `standard` template calls `rappture`, which is only right for a Rappture
tool. A Linux GUI tool replaces that with the command that starts its own
interface. [Rappture is deprecated](02-overview.md#rappture); a new tool is
more likely to use one of the other two templates.
