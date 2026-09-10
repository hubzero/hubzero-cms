<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: ok
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/overview
source-id: 3536
modified: 2009-10-13
imported: 2026-09-09
merged-from: 2.2
-->
# What you can publish as a tool

The kinds of program a hub can run in a tool session, and what each one costs
you to build. For the pipeline that publishes them, see
[The contribution process](process.md).

> **Note:** What runs in a session is decided by the tool platform, which is
> separate software from the CMS in this repository and could not be checked
> here. The list below is the platform's, carried over from
> help.hubzero.org. The one part checked here is the **Publishing Option** on
> the registration form, which is where you declare which kind of tool you are
> contributing.

## The three publishing options

The registration form offers three:

| Option | Label on screen | Covers |
|---|---|---|
| `standard` | Rappture or Linux-GUI based tool | Anything with an X11 GUI, including Rappture |
| `jupyter` | Web application (Jupyter, Rstudio, ...) | Notebooks, Shiny, Dash, plain web applications |
| `simtool` | Sim2L | A notebook published as a callable simulation |

The last two appear only where the hub has turned the matching options on.
The choice decides the starter invoke script the hub writes for you; see
[Tool repository structure](01-toolrepostructure.md).
*(Verified against `com_tools`.)*

## Linux GUI applications

A tool that already has a graphical interface under Linux/X11 can usually be
published as it stands. Qt, GTK, wxWidgets, Tcl/Tk, Java, and MATLAB
interfaces all work. The X output is rendered on the execution host and shown
in the member's browser.

Two caveats come with that:

- **Graphics-heavy tools do not perform well.** Execution containers run on
  cluster nodes without graphics cards, so OpenGL is emulated in software, and
  everything rendered is then transmitted to the browser as images. Expect a
  few frames per second: enough to view and interact with data, nowhere near a
  desktop machine with a graphics card.
- **The tool sees the hub's file system, not the member's desktop.** A
  *File > Open* dialog inside a tool lists the member's home directory on the
  hub. Files from their own machine have to get there first, by `sftp`,
  WebDAV, or the hub's `importfile` command. See
  [Accessing your home directory](06-accesshomedir.md) and
  [Importing and exporting user files](08-fileinout.md).

## Rappture

> **Warning:** Rappture is deprecated. `rappture.org` and the Rappture wiki no
> longer serve public documentation, and no new Rappture material is being
> written. Hubs still run Rappture tools and the platform still supports them,
> so this section stays for the tools that exist. For a new tool, a Jupyter
> notebook is the current path on most hubs.

Rappture generates a graphical interface from an XML description of a tool's
inputs and outputs. You write `rappture/tool.xml`, and the toolkit builds the
form, the *Simulate* button, and the result plots. It binds to C/C++, Fortran,
MATLAB, Python, Perl, Tcl/Tk, and Ruby, so it suits a legacy solver or a
simple modelling code that has no interface of its own.

![A Rappture-based tool: an input form on the left and a result plot on the right](../media/overview-rappture-01.png)

Rappture was written for the hub environment, so it addresses both caveats
above: its visualisation uses the hub's rendering hosts, and it has
`importfile` and `exportfile` built in for moving files to and from the
member's desktop.

The introductory course
[Developing Scientific Tools for the HUBzero Platform](https://help.hubzero.org/resources/tooldev)
is still online and is the most complete Rappture material left. It spells the
product name the way it was spelled at the time.

## Jupyter notebooks

Notebooks suit teaching, documented workflows, and exploratory work. Hub
notebooks are usually Python, R, or Octave; other kernels can be installed. A
notebook can also drive an external program in any language: prepare input in
Python, run the solver, post-process the output.

Published as a **Jupyter tool**, the notebook runs automatically and only its
graphical output — widgets, plots — is shown. The editing interface is hidden
and cannot be reached, so the member sees an application rather than a
notebook. Python is the easier language for this, because of the widget
libraries, and a notebook written in another language can still have its
interface written in Python.

[Jupyter notebooks](10-jupyter-notebooks/README.md) covers deployment styles
and the invoke script that selects them.

## Sim2L

A Sim2L tool is a notebook published as a callable simulation, with declared
inputs and outputs so that other notebooks can run it and reuse its results.
Register it with the **Sim2L** publishing option, which puts a `simtool`
directory in the repository skeleton and writes an invoke script that starts
the notebook headless.

Sim2L is the platform's own material and is not documented in this repository
beyond the repository layout and the invoke template.

## R Shiny, Dash, and web applications

The `jupyter` publishing option covers web applications generally, not only
notebooks:

- **R and Shiny** — R developers can publish a [Shiny](https://shiny.rstudio.com/)
  application.
- **Plotly Dash** — [Dash](https://plot.ly/products/dash/) builds analytical
  web applications in Python, with no JavaScript, over Plotly.js, React, and
  Flask.
- **Plain web applications** — anything that runs on Linux and serves HTML,
  CSS, and JavaScript can be published as a tool.

All of these are served through the session in the member's browser in the
same way, and all of them are started by the tool's
[invoke script](03-invoke.md).
