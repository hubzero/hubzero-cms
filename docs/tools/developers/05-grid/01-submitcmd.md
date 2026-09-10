<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid/submitcmd
source-id: 3540
modified: 2009-12-01
imported: 2026-09-09
-->
# Submit command

`submit` takes a command you would run in a tool session and runs it
somewhere else — a cluster, a campus resource, a national one — then brings
the results back. You write the command the way you always would and put
`submit` and its options in front of it.

> **Note:** `submit` belongs to the tool execution platform, which is
> separate software and is **not in this repository**. None of this page
> could be checked against the code here. The option list below is a
> transcript of one hub's client, reorganised and made internally
> consistent; treat `submit --help` on your own hub as the authority on
> what your client accepts and which venues and tools you may use.

## What a run does

Each submission goes through the same steps.

1. A destination venue is selected.
2. A wrapper script is generated for remote execution.
3. A batch system description file is generated, if the venue needs one.
4. Input files are gathered into a tarball and transferred to the remote
   site, along with the wrapper and batch scripts.
5. The wrapper script runs remotely.
6. Progress is monitored until the run completes.
7. Output files are returned.

## Finding out what your hub offers

`submit --help` prints the option list. It also takes an argument, and the
lists it prints are generated for you from your hub's configuration and
your own permissions, so they differ from hub to hub and from user to user.

| Command | Prints |
|---|---|
| `submit --help` | The option list |
| `submit --help tools` | Pre-staged applications you may run |
| `submit --help venues` | Destinations you may send a run to |
| `submit --help managers` | Multiprocessor job managers you may ask for |
| `submit --help examples` | The parameter sweep examples, reproduced below |

The lists look like this. The names are examples only; nothing here is
guaranteed to exist on your hub.

```text
$ submit --help venues

Currently available VENUES are:
   OSG
   brown
   datalimited@brown
   ncn-hub@brown
   standby@brown

$ submit --help managers

Currently available MANAGERs are:
   mpi
   mpich
   mpirun
   parallel
   serial
   lammps-03Mar20_mpi
   lammps-03Mar20_serial
```

## Options

### Running and controlling runs

| Option | Meaning |
|---|---|
| `-h`, `--help [tools\|venues\|managers\|examples]` | Report command usage, or one of the lists above |
| `-l`, `--local` | Execute the command in the tool session instead of sending it away |
| `--status` | Report status for runs executing remotely |
| `-k`, `--kill` | Kill runs executing remotely |
| `--venueStatus` | Report venue status |

### Choosing where the run goes

| Option | Meaning |
|---|---|
| `-v`, `--venue` | Remote job destination |
| `-m`, `--manager` | Multiprocessor job manager |
| `-r N`, `--redundancy=N` | Number of identical simulations to execute in parallel |

### Asking for resources

| Option | Meaning |
|---|---|
| `-n N`, `--nCpus=N` | Number of processors for MPI execution |
| `-N N`, `--ppn=N` | Number of processors per node for MPI execution |
| `-w`, `--wallTime` | Estimated walltime, `hh:mm:ss` or minutes |
| `-e`, `--env` | Set an environment variable, `Variable=value` |
| `-W`, `--wait` | Wait for reduced job load before submitting |
| `-Q`, `--quota` | Enforce local user quota on the remote execution host |
| `-q`, `--noquota` | Do not enforce local user quota on the remote execution host |

### Files

| Option | Meaning |
|---|---|
| `-i`, `--inputfile` | Send an extra input file or directory tree with the run |
| `--runName=NAME` | Name used for directories and files created during the run. Alphanumeric characters only |

### Sweep options

| Option | Meaning |
|---|---|
| `-p`, `--parameters` | Parameter sweep variables, or a file of them |
| `-d`, `--data` | Parametric variable data, in csv format |
| `-s SEP`, `--separator=SEP` | Separator for parameter value lists |
| `--stripes=N` | Number of parallel local jobs when doing a parametric sweep |

### Watching a run

| Option | Meaning |
|---|---|
| `--progress` | Progress display: `auto`, `curses`, `submit`, `text`, `pegasus` or `silent` |
| `--tailStdout` | Periodically report the tail of the standard output file |
| `--tailStderr` | Periodically report the tail of the standard error file |
| `--tail` | Periodically report the tail of an application file |
| `-M`, `--metrics` | Report resource usage on exit |
| `--detach` | Detach the client after launching the run |
| `--attach=ID` | Attach to a previously detached run |
| `--asynchronous` | Asynchronous simulation; results are not returned |

> **Note:** The Python interface described in
> [Submitting from a Jupyter notebook](04-jupyter_submit.md) also offers
> `--stdin`, `--debug` and `--version`, which do not appear in the option
> transcript above. The transcript is older than the Python class, and
> which of the two matches your hub could not be established here. Check
> `submit --help` before relying on any of the three.

## Running a command remotely

```text
$ submit -v clusterA echo Hello world!
Hello world!
```

Here `echo` runs on the venue `clusterA`, which executes runs directly on
the host, so the output comes straight back. On a venue that puts the run
through a batch scheduler, `submit` reports the job through the queue and
leaves the output in files instead.

```text
$ submit -v clusterB echo Hello world!
(00577296) Simulation Queued Wed Oct  7 14:45:21 2020
(00577296) Simulation Done Wed Oct  7 14:54:36 2020
$ cat 00577296.stdout
Hello world!
```

The number in parentheses is the run id, and it names the output files.

## Parameter sweeps

A sweep declares substitutable parameters named `@@something`. `submit`
substitutes their values into template files and into command arguments,
and runs one job per combination. A file argument written `@:name` is
treated as a template rather than copied as it is.

| Written as | Means |
|---|---|
| `@@cap=10pf,100pf,1uf` | The three values listed |
| `@@num=1:1000` | 1, 2, 3, … 1000 |
| `@@vth=0:0.2:5` | 0 to 5 in steps of 0.2, so 26 values |
| `@@doping=1e15-1e17 in 30 log` | 30 points from 1e15 to 1e17 on a log scale |
| `@@file=glob:indeck*` | The names of the files matching the pattern, in natural order |
| `-p params` | Parameter definitions read from the file `params` |
| `-p "params;@@num=1-10;@@color=blue"` | The file `params`, plus two more parameters |

Values are separated by commas unless `-s` changes the separator.

> **Note:** `globnat:` is deprecated. `glob:` now sorts in natural order —
> `indeck1`, `indeck2`, `indeck10` rather than `indeck1`, `indeck10`,
> `indeck2` — so use `glob:` for both.

### Examples

These are the examples `submit --help examples` prints.

```text
submit -p @@cap=10pf,100pf,1uf sim.exe @:indeck
```

Submit 3 jobs. `@:indeck` means "use the file indeck as a template file".
Substitute the values 10pf, 100pf and 1uf in place of `@@cap` in the file,
send off one job for each value, and bring back the results.

```text
submit -p @@vth=0:0.2:5 -p @@cap=10pf,100pf,1uf sim.exe @:indeck
```

Submit 78 jobs. `@@vth` goes from 0 to 5 in steps of 0.2, so there are 26
values. For each of those, `@@cap` takes its three values: 26 x 3 = 78 jobs.
Both parameters are substituted into the template.

```text
submit -p params sim.exe @:indeck
```

Take the parameter definitions from a file named `params` instead of the
command line. The file might read:

```text
# parameters for my job submission
parameter @@vth=0:0.2:5
parameter @@cap = 10pf,100pf,1uf
```

```text
submit -p "params;@@num=1-10;@@color=blue" job.sh @:job.data
```

The semicolons split the value into three parts. The first loads parameters
from the file `params`. The second adds a parameter `@@num` running from 1
to 10. The third adds `@@color` with the single value `blue`. `@@num` and
`@@color` must be new names; they cannot override anything defined in
`params`.

```text
submit -d input.csv sim.exe @:indeck
```

Take the parameters from the comma-separated file `input.csv`. Each line
after the first is one job's parameter values, so 100 lines means 100 jobs.
The first line may name the columns with `@@param` names; if it does not,
the columns are called `@@1`, `@@2`, `@@3` and so on. Whitespace is
significant in every value in the file. For example:

```text
@@vth,@@cap
1.1,1pf
2.2,1pf
1.1,10pf
2.2,10pf
```

Parameters are substituted into template files such as `@:indeck` as before.

```text
submit -d input.csv -p "@@doping=1e15-1e17 in 30 log" sim.exe @:infile
```

Take the parameters from `input.csv` and add `@@doping`, which runs from
1e15 to 1e17 in 30 points on a log scale. Every value in the data file runs
at each of those points, so a data file of 50 jobs gives 30 x 50 = 1500 jobs.

```text
submit -d input.csv -i @:extra/data.txt sim.exe @:indeck
```

Besides the template indeck file, send `extra/data.txt` with each job and
treat it as a template too.

```text
submit -s / -p @@address=23 Main St.,Hometown,Indiana/42 Broadway,Hometown,Indiana \
       -s , -p @@color=red,green,blue job.sh @:job.data
```

Change the separator to a slash while defining the addresses, then change it
back to a comma for `@@color` and everything after it. This is worth doing
when the values themselves contain commas.

```text
submit -p @@num=1:1000 sim.exe input@@num
```

Submit jobs 1, 2, 3, … 1000. Parameter names are recognised in command line
arguments as well as in template files, so here the numbers are substituted
into the file name and each job reads `input1`, `input2`, … `input1000`.

```text
submit -p @@file=glob:indeck* sim.exe @@file
```

Use the names of the files matching `indeck*` as the values of `@@file`.
If the directory holds `indeck1`, `indeck10` and `indeck2`, this launches
three jobs, one per file, in natural order.

## How a venue is chosen

The first of these that applies wins.

1. `-l`/`--local` runs the command in your tool session.
2. `-v`/`--venue` names a destination.
3. A site is chosen at random from those holding the pre-staged application.
4. A site is chosen at random from all configured sites.

Venues that cannot meet the run's resource request are not considered.
Venues are usually configured with limits on cores, walltime or core-hours.

`-r`/`--redundancy` sends the same run to more than one venue at once. The
first one to finish successfully cancels the others; if none succeed, the
results of one of them are returned. Redundant submission is not allowed
with parameter sweeps.

## Submission mechanisms

`submit` supports several mechanisms, and the set is extensible.

- **local** — batch submission available on the submit host itself,
  including condorHT and Pegasus queue submission.
- **ssh** — direct use of ssh. `submit` manages access to a venue with a
  shared key and acts as a proxy for the hub user.
- **ssh with remote batch submission** — ssh to the venue and submit there.
  Methods are provided for PBS, condorHT, Pegasus and SLURM, with further
  interfaces to SGE, Load Leveler, BOINC, LSF and Tapis.

## Files in and files out

Files you name and the scripts `submit` generates are packed into a tarball
for delivery. Individual files and whole directory trees can be listed with
`-i`/`--inputfile`, and command arguments that name existing files or
directories are packed too. With the ssh mechanisms the tarball travels by
`scp`.

The wrapper script then runs remotely, directly or through a batch queue,
and the job is subject to every restriction of that queue.

Remote batch jobs are monitored with the methods appropriate to the queuing
system, at a configurable frequency — typically about a minute. Status
changes are reported to you as they happen, and a report is made at least
every few minutes even when nothing has changed. Completion is detected the
same way.

Output travels back the way the input came. Any file or directory the
application created or changed is retrieved as a tarball and expanded into
your home base directory. **Nothing stops it overwriting what is already
there**, so keep runs in their own directories.

Alongside the application's own output, a run leaves `RUNID.stdout` and
`RUNID.stderr` in the home base directory, and possibly a second pair
holding the output of the batch submission script itself. `RUNID` is the
identifier `submit` assigns, unless `--runName` gave it one.
