<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid/jupyter_submit
source-id: 3543
modified: 2023-05-05
imported: 2026-09-09
source-state: unpublished
-->
# Submitting from a Jupyter notebook

How to send a job to a cluster from a notebook cell, either by calling
`submit` as a shell command or through the `SubmitCommand` class in the
`hubzero.submit` library. This is the current way to reach outside
computing resources from a tool, and the page to read if you are writing a
new one.

> **Note:** `submit` and the `hubzero.submit` library belong to the tool
> execution platform, which is separate software and is **not in this
> repository**, so nothing on this page could be checked against the code
> here. What the CMS does know is that a tool can be a Jupyter application:
> `com_tools` has an **Enable Jupyter** option, and while it is on the tool
> registration form offers **Web application (Jupyter, Rstudio, ...)** as a
> publishing choice, stored on the tool version as `publishType` =
> `jupyter`. See
> [registering a tool](../../../managers/03-maintenance/02-tools.md) for
> that form, and
> [Jupyter notebooks as tools](../10-jupyter-notebooks/README.md) for
> publishing a notebook in the first place.

## Two ways to call it

`submit` is an ordinary command, so a shell escape in a cell works and
behaves exactly as it would in a terminal:

```python
!submit --help venues
!submit -v venueName -w 5 sim.exe input.dat
```

Use that for a quick job. For anything the notebook has to build up,
inspect or repeat, use the `SubmitCommand` class instead: it exposes every
`submit` option as a method, so the arguments come from Python values
rather than from string formatting, and the command can be printed before
it runs and saved for later.

Both routes end up at the same client, so read
[Submit command](01-submitcmd.md) for what the options mean, how a venue
is chosen, and what files a run leaves behind.

## The SubmitCommand class

```python
from hubzero.submit.SubmitCommand import SubmitCommand

submitCommand = SubmitCommand()
help(submitCommand)
```

`help()` prints the authoritative list of methods for the library
installed on your hub. The tables below organise that list; where they
disagree, believe `help()`.

The constructor takes two arguments, both with defaults:
`configurationDirectory` (`/etc/submit`) and `hubLogPath`
(`/tmp/submit/.submit.log`). Most notebooks need neither.

Nearly every option has a **set** method and a matching **reset** method.
`set` applies the option; `reset` returns it to the default, which is
either the system default or whatever the venue and tool configuration
supply.

### Running the command

| Method | What it does |
|---|---|
| `submit(args=None, stdin=None)` | Run the command built up so far. If `args` is given, the previous settings are ignored for this run but not overwritten |
| `show(args=None, textWidth=80)` | Print the command that would run, without running it |
| `saveSubmitCommand(submitCommandJSONFile=None)` | Save the current settings as JSON |
| `loadSubmitCommand(submitCommandJSONFile)` | Load settings from a JSON file |
| `resetSubmitCommand()` | Return every setting to its default |

### The command to run

| Method | `submit` option | Notes |
|---|---|---|
| `setCommand(command)` | — | A string or a list. A string is split; the first element is the command, the rest are its arguments |
| `setCommandArguments(commandArguments)` | — | A string or list of arguments only |
| `setInputFiles(inputFiles)` | `-i` | One filename or a list |
| `setStdin(stdinPath)` | — | Path to a file to feed the command on standard input |
| `setRunName(runName)` | `--runName` | Names the standard output and error files. Defaults to the generated job id |

`resetCommand()` clears the command and its arguments;
`resetCommandArguments()`, `resetInputFiles()` and `resetStdin()` clear
just their own setting.

### Where the run goes

| Method | `submit` option | Notes |
|---|---|---|
| `setVenue(venue)` | `-v` | Names the destination. `submit --help venues` lists the ones you may use |
| `setManager(manager)` | `-m` | Defaults to whatever the venue or tool configuration sets |
| `setRedundancy(redundancy)` | `-r` | Defaults to the value in the submit configuration |
| `setLocal(local=True)` | `-l` | Run in the tool session instead of sending the job away |

### Resources

| Method | `submit` option | Notes |
|---|---|---|
| `setNcores(nCores)` | `-n` | Not needed for a single-core run |
| `setPPN(ppn)` | `-N` | Cores per node. Defaults to a value derived from the venue's node hardware |
| `setWallTime(wallTime)` | `-w` | Minutes, as an integer or a float |
| `setEnvironmentVariables(environmentVariables)` | `-e` | A dictionary keyed by variable name |
| `setQuota(quota)` | `-Q` / `-q` | Uses your hub disk quota to limit what the run may generate on the remote resource. On by default |
| `setWait(wait=True)` | `-W` | Wait for a quieter moment before submitting. Each user has a limited submission rate, measured over time and weighted towards recent submissions; exceed it and a new run is refused |

### Parameter sweeps

| Method | `submit` option | Notes |
|---|---|---|
| `setParameters(parameters, separator=None)` | `-p` | A string or list of strings. Replaces any parameters already set |
| `addParameters(parameters, separator=None)` | `-p` | Adds to them instead |
| `setDataFile(dataFile)` | `-d` | One csv filename |
| `setDefaultSeparator(separatorDefault)` | `-s` | The separator used in parameter value lists |

The sweep syntax itself — `@@name` parameters, `@:file` templates, ranges,
`glob:` — is on the [submit command](01-submitcmd.md) page.

### Watching a run

| Method | `submit` option | Notes |
|---|---|---|
| `setProgress(detail=None)` | `--progress` | `curses`, `submit`, `text`, `pegasus` or `silent`. `None` leaves the default |
| `setTailStdout(tailStdout=True, tailStdoutNlines=None)` | `--tailStdout` | Report the standard output file as the run proceeds |
| `setTailStderr(tailStderr=True, tailStderrNlines=None)` | `--tailStderr` | The same for standard error |
| `setTailFiles(tailFiles)` | `--tail` | Report other files. One filename or a list; append `:#` to a name for the number of lines |
| `setDefaultTailNlines(tailNlinesDefault)` | — | How many lines a tail reports when a call does not say |
| `setReportMetrics(reportMetrics=True)` | `-M` | Report resource usage on exit |
| `setDetach(detach=True)` | `--detach` | Return control as soon as the run is launched. Monitor its status to learn when it finishes; you can reattach later |
| `setAttachId(attachId)` | `--attach` | Reattach to a detached run |

> **Tip:** `silent` progress suits a notebook that is generating its own
> output; the `curses` display is designed for a terminal.

### Status, help and version

| Method | `submit` option | Notes |
|---|---|---|
| `setQueryJobs(queryJobs)` | `--status` | One integer job id or a list of them |
| `setKillJobs(killJobs)` | `-k` | One integer job id or a list of them |
| `setVenueStatus(venueStatus)` | `--venueStatus` | Status of the named venue |
| `setHelp(detail=None)` | `--help` | `managers`, `tools`, `venues` or `examples` |
| `setVersion(detail=None)` | `--version` | `client`, `server` or `distributor` |
| `setDebug(debug=True)` | — | Turn debug reporting on |

Each of these has a matching `reset` method that turns the request off
again — a `SubmitCommand` object is reused across cells, so clear a query
or a kill request before submitting real work with the same object.

> **Note:** `setStdin`, `setDebug` and `setVersion` have no counterpart in
> the `submit --help` transcript on the [submit command](01-submitcmd.md)
> page. The transcript is the older of the two records; which matches your
> hub could not be established here.

## Examples

Pass the arguments exactly as you would on the command line:

```python
submitCommand = SubmitCommand()
result = submitCommand.submit(['-w', '5', applicationCode, '--C', '0.001', '--Vin', '3'])
```

Here `-w 5` is a five minute walltime for `submit` itself, while `--C` and
`--Vin` are arguments of the application and are passed through untouched.

Or build the command one setting at a time, print it, then run it:

```python
submitCommand = SubmitCommand()
submitCommand.setWallTime(5)
submitCommand.setVenue(venueName)
submitCommand.setCommand(applicationCode)
submitCommand.setCommandArguments(['--C', '0.001', '--Vin', '3'])
submitCommand.show()
result = submitCommand.submit()
```

Take `venueName` from `submit --help venues` on your own hub; venue names
differ from hub to hub, and a name that works on one hub means nothing on
another.

A long run is easier to live with detached, since the cell returns as soon
as the job is launched:

```python
submitCommand.setDetach()
result = submitCommand.submit()
```

Then query it later, from another cell, by its job id.
