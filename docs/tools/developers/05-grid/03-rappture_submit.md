<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid/rappture_submit
source-id: 3542
modified: 2009-10-13
imported: 2026-09-09
-->
# Submitting from a Rappture tool

How a Rappture wrapper script calls `submit` to run its simulation on a
cluster instead of in the tool session. The page is kept for hubs that
still run Rappture tools.

> **Warning:** Rappture is deprecated. `rappture.org` now redirects to a
> nanoHUB page that is not readable without an account, and no new tool
> should be built on it. If you are writing a tool today, drive `submit`
> from a notebook instead: see
> [Submitting from a Jupyter notebook](04-jupyter_submit.md).

> **Note:** Rappture and `submit` both belong to the tool execution
> platform, which is separate software and is **not in this repository**,
> so none of this could be checked against the code here. The examples are
> the ones that shipped with the original documentation, with import damage
> repaired: escape sequences that had been doubled are back to single
> backslashes, and the errors noted in each section below are corrected.
> They are Python 2 era code and are illustrations, not a tested library.

The pattern is the same in every language. Build a small shell script that
runs `submit` in the background, run that script through the Rappture
helper that forwards signals, and gather the job's output files afterwards.
Handling the signal matters: without it, pressing **Abort** in the tool
leaves the remote job running and consuming the hub's resource allocation.

## TCL wrapper script

Catch the Abort interrupt first. Setting `execctl` to 1 terminates the
process and its children.

```tcl
package require Rappture
Rappture::signal SIGHUP sHUP {
   puts "Caught SIGHUP"
   set execctl 1
}
Rappture::signal SIGTERM sTERM {
   puts "Caught SIGTERM"
   set execctl 1
}
```

Then build a script to run with `Rappture::exec`. The `trap` statement
catches the interrupt raised when the wrapper is aborted. Putting `submit`
in the background allows several `submit` commands in one script, and
`wait` holds the script open until they have all finished.

```tcl
   set    submitScript "#!/bin/sh\n\n"
   append submitScript "trap cleanup HUP INT QUIT ABRT TERM\n\n"
   append submitScript "cleanup()\n"
   append submitScript "{\n"
   append submitScript "   kill -TERM `jobs -p`\n"
   append submitScript "   exit 1\n"
   append submitScript "}\n\n"

   append submitScript "cd [pwd]\n"
   append submitScript "submit -v cluster -n $cores -w $walltime\\\n"
   append submitScript "       COMMAND ARGUMENTS &\n"
   append submitScript "sleep 5\n"
   append submitScript "wait\n"

   set submitScriptPath [file join [pwd] submit_script.sh]
   set fid [open $submitScriptPath w]
   puts $fid $submitScript
   close $fid
   file attributes $submitScriptPath -permissions 00755
```

Run it the usual way. The output of every `submit` command in the script is
streamed to the display and kept in `out`.

```tcl
set status [catch {Rappture::exec $submitScriptPath} out]
```

Each `submit` command writes the command's standard output and error to
`JOBID.stdout` and `JOBID.stderr`, where `JOBID` is the run identifier
`submit` assigned. Gather them like this.

```tcl
   set out2 ""
   foreach errfile [glob -nocomplain *.stderr] {
      if [file size $errfile] {
         if {[catch {open $errfile r} fid] == 0} {
            set info [read $fid]
            close $fid
            append out2 $info
         }
      }
      file delete -force $errfile
   }
   foreach outfile [glob -nocomplain *.stdout] {
      if [file size $outfile] {
         if {[catch {open $outfile r} fid] == 0} {
            set info [read $fid]
            close $fid
            append out2 $info
         }
      }
      file delete -force $outfile
   }
```

Remove the script, then present the gathered text as the job's output log.
Everything after that is ordinary result processing.

```tcl
file delete -force $submitScriptPath
$driver put output.log $out2
```

## Python wrapper script

Import the helper that runs a command and traps the signals the **Abort**
button raises. `submit` must finish before `RapptureExec` returns.

```python
import os
import re
from Rappture.tools import executeCommand as RapptureExec
```

Build the command as a list and run it. Its output is streamed to the
display and kept in `stdOutput`.

```python
   submitCommand = ["submit", "-v", venue, "-n", cores,
                    "-w", walltime, COMMAND, ARGUMENTS]
   exitStatus, stdOutput, stdError = RapptureExec(submitCommand)
```

Gather the job's own output files afterwards, then hand the text to the
output log.

```python
   reStdout = re.compile(".*.stdout$")
   reStderr = re.compile(".*.stderr$")

   out2 = ""
   errFiles = list(filter(reStderr.search, os.listdir(os.getcwd())))
   for errFile in errFiles:
      errFilePath = os.path.join(os.getcwd(), errFile)
      if os.path.getsize(errFilePath) > 0:
         f = open(errFilePath, 'r')
         outFileLines = f.readlines()
         f.close()
         out2 += '\n' + ''.join(outFileLines)
      os.remove(errFilePath)

   outFiles = list(filter(reStdout.search, os.listdir(os.getcwd())))
   for outFile in outFiles:
      outFilePath = os.path.join(os.getcwd(), outFile)
      if os.path.getsize(outFilePath) > 0:
         f = open(outFilePath, 'r')
         outFileLines = f.readlines()
         f.close()
         out2 += '\n' + ''.join(outFileLines)
      os.remove(outFilePath)

   lib.put("output.log", out2, append=1)
```

> **Note:** The published version of this example called `os.getpwd()`,
> which is not a Python function, missed the `re` import, and appended the
> letter `n` where a newline was meant. All three are corrected above.

## Perl wrapper

Catch the Abort interrupt.

```perl
use Rappture;

my $ChildPID = 0;

sub trapSig {
    print "Signal @_ trapped\n";
    if($ChildPID != 0) {
        kill 'TERM', $ChildPID;
        exit 1;
    }
}
$SIG{TERM} = \&trapSig;
$SIG{HUP}  = \&trapSig;
$SIG{INT}  = \&trapSig;
```

Build the shell script. `trap` catches the interrupt raised on abort, and
`wait` holds the script open until `submit` finishes.

```perl
$SCRPT = "submit_app.sh";
open(FID,">$SCRPT");
print FID "#!/bin/sh\n";
print FID "\n";
print FID "trap cleanup HUP INT QUIT ABRT TERM\n\n";
print FID "cleanup()\n";
print FID "{\n";
print FID "   kill -s TERM `jobs -p`\n";
print FID "   exit 1\n";
print FID "}\n\n";

print FID "submit -v cluster -n $cores -w $wallTime COMMAND ARGUMENTS &\n";
print FID "wait %1\n";
print FID "exitStatus=\$?\n";
print FID "exit \$exitStatus\n";
close(FID);
chmod 0775, $SCRPT;
```

Run it with the usual fork and exec. This route cannot stream the command's
output.

```perl
if      (!defined($ChildPID = fork())) {
    die "cannot fork: $!";
} elsif ($ChildPID == 0) {
    exec("./$SCRPT") or die "cannot exec $SCRPT: $!";
    exit(0);
} else {
    waitpid($ChildPID,0);
}
```

The `JOBID.stdout` and `JOBID.stderr` files are gathered with ordinary Perl
file matching and reading, and result processing proceeds as normal.

## Octave and MATLAB scripts

`rpExec` runs a command and terminates it on an interrupt, hangup or
terminate signal, so the process stops when **Abort** is pressed.

```text
 -- Function: [EXITSTATUS] = rpExec(COMMAND,STREAMOUTPUT)
 -- Function: [EXITSTATUS, STDOUTPUT] = rpExec(COMMAND,STREAMOUTPUT)
 -- Function: [EXITSTATUS, STDOUTPUT, STDERROR] = rpExec(COMMAND,STREAMOUTPUT)
```

`COMMAND` is the set of strings making up the command to run. When
`STREAMOUTPUT` is 1, the command's standard output and error are piped
back to the current process as it runs. `EXITSTATUS` is 0 when no error
occurred; `STDOUTPUT` and `STDERROR`, if asked for, hold copies of the two
streams.

```matlab
[exitStatus,stdOutput,stdError] = rpExec({"submit","--wallTime","30","lammps-03Mar20-serial","-in","lmp.in"},1);
```

> **Note:** The published version of this example wrote `-wallTime`, which
> `submit` reads as `-w allTime`. The long form takes two hyphens; the
> short form is `-w`. The tool name is an example — run
> `submit --help tools` for the ones your hub has.
