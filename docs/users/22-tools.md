<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/users/tools
source-id: 3326
modified: 2011-11-04
imported: 2026-09-09
-->
# Tools

A tool is a program that runs on the hub's own machines and appears in your
browser. You do not install anything, and it does not matter what operating
system you use. Published tools have a page of their own under `/tools`, which
is a resource page like any other, with a **Launch Tool** button on it.

> **Important:** The execution side of this is separate software. This
> repository holds the CMS: the tool pages, the session screens, the sharing
> form, and the contribution pipeline. The machines that run the tools, the
> middleware that starts and stops sessions, the shared file system, and the
> source code hosting are all installed alongside the hub and are not part of
> the CMS. A hub whose administrators have not set them up shows tool pages
> that cannot launch anything.

## Running a tool

Select **Launch Tool** on the tool's page. The hub allocates a session on an
execution host and shows it in a frame on the session page. What you see
inside the frame is the tool's own interface — the CMS only delivers it.

Across the top of the session page:

| Control | What it does |
|---|---|
| The session title | Select it to rename the session. |
| **Keep for later** | Leaves the session running and returns you to your member area. Come back to it from the **My Sessions** module on your dashboard. |
| **Terminate** | Ends the session and discards it. |
| **Options** | Chooses which viewer renders the session, if your hub has more than one installed. Tick *Use for future sessions.* to make the choice stick. The control only appears when there is more than one viewer to choose from. |

Closing the browser tab does neither of the first two: the session keeps
running until you terminate it, or until the execution platform reclaims it.
Because a session occupies a slot, the hub caps how many you may have open at
once. Beyond that cap, launching reports that your quota is exceeded and asks
you to close one first.

Where the hub shows a storage meter, it sits under the session and reports how
much of your disk quota you have used. Filling it stops the tool writing
files.

Where a hub runs sessions in more than one zone, the session page names the
zone you landed in and offers to relaunch elsewhere.

### Your files

A tool session sees the hub's file system, not your computer's. **File >
Open…** inside a tool looks at your home directory on the hub, so a file on
your desktop has to be transferred there first. How you do that — `sftp`,
WebDAV, or a file-import command from inside the session — is decided by the
execution platform rather than the CMS, so ask your hub's support staff which
of them it offers.

### Sharing a session

Where the hub allows it, the session page carries a **Share session** form:

1. Type the usernames, user IDs, or email addresses to share with, and/or pick
   one of your groups.
2. Tick **Read-Only?** to keep control of the session yourself. Without it,
   everyone you share with can drive the tool.
3. Tick the acknowledgement that shared users can alter and control the
   session.
4. Submit.

The session then appears in those people's **My Sessions** alongside their
own, marked with your username as its owner. They leave it with **Stop sharing** on the session
itself, or **disconnect** from the session list; only you can terminate it.

## Contributing a tool

If you have a program you want others to run, the hub can host it. What is
practical:

- **A Linux/X11 program with a graphical interface** — Java, Qt, MATLAB, or
  anything else — can usually be deployed close to as-is. Two caveats:
  graphics are rendered in software, so anything depending on a GPU will be
  slow; and outbound network connections are blocked by default, so a tool
  that fetches data from elsewhere has to be approved and allowed through.
- **A command-line program** needs an interface built for it. The
  [Rappture toolkit](http://rappture.org) is the usual answer and binds to
  C/C++, Fortran, Java, MATLAB, R, Python, Perl, Ruby, and Tcl/Tk. Work that
  needs a cluster is dispatched with the
  [submit](../tools/developers/grid/submitcmd.md) command.
- **Windows and macOS programs** cannot be hosted. Some Windows programs run
  under [Wine](http://www.winehq.org/), which is worth trying, but everything
  deployed here runs under Linux.

Hubs can also publish tools as Jupyter notebooks or as Sim2Ls, where those
options are switched on.

### The pipeline

Contribution runs through the hub's **Contribtool** pipeline at `/tools`. Your
tool moves through a fixed sequence of states, and the status page shows where
it is and what you have to do next.

| State | Meaning |
|---|---|
| **Registered** | You have filled in the registration form. The hub's staff are setting up your project area and source repository. |
| **Created** | The project area exists. Upload and commit your code, then say so. |
| **Uploaded** | You have told the hub the code is ready. Staff install it for testing. |
| **Installed** | The installed version is ready for you to test. |
| **Approved** | You have approved it for publication. Staff do the final checks. |
| **Published** | Live, with its own tool page. |

Two later states are also possible: **Updated**, when you commit changes to an
already-installed tool and ask for them to be reinstalled, and **Retired**,
when a published tool is withdrawn.

The steps:

1. **Register the tool.** Select **New Tool** on the pipeline page and fill in
   the form. Choose where the source lives: a Subversion repository hosted by
   the hub, a Git repository hosted by the hub, or an external Git repository
   on GitHub or GitLab that the hub pulls from.
2. **Build and test it.** Use the hub's workspace tool — a Linux desktop in
   your browser — to build and test exactly as you would anywhere else.
3. **Commit and say so.** When the code is in the repository, follow the link
   on the status page reading *My code is committed, working, and ready to be
   installed.*
4. **Test the installed copy.** When the hub reports the tool installed, launch
   it and check it. If it is right, follow *I approve it.* If it is not,
   commit a fix and follow *I've committed new code. Please install the latest
   version for testing and approval.*
5. **Choose a licence.** The status page's **change license** link sets the
   tool open or closed source. Open source lets anyone download the code under
   the terms you state; closed source lets people run the tool but not read it.
   Once released as open source it stays open for anyone who took a copy, so
   get agreement from everyone involved first.
6. **Write the tool page.** The **Edit description page** link opens the resource
   page people will see: title, abstract, screenshots, and attachments.
   **Preview** shows it as they will.

While the tool is in the pipeline, the status page carries a **Message** link
for talking to the hub's staff, a **History** link into the support ticket
tracking it, and links into the development site's wiki, source browser, and
timeline. Those last three point at a separate development site, not at the
hub.

### After publication

The published tool page counts the people who have used it, and collects
questions and suggestions from them. Answering those and publishing improved
versions is what grows a tool's user base. Your own contribution figures are
gathered on the **Usage** tab of your member area — see [Usage](usage.md),
which explains why that tab is often empty.

The [Tools](../tools/README.md) book covers the developer side in depth:
repository structure, invoke scripts, tool paths, file transfer, the submit
command, and Jupyter notebooks as tools.
