<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid/jupyter_submit
source-id: 3543
modified: 2023-05-05
imported: 2026-09-09
source-state: unpublished
-->
# Jupyter Integration with Submit

## Overview

The submit command may be used like any other shell command in a notebook cell. In addition a more integrated approach is available by using the SubmitCommand class. The SubmitCommand class provides a fully functional interface to all submit command options. For each `submit` command argument there are typically two method types - set and reset. The standard Python `help` builtin can be used to show methods and associated arguments.

## SubmitCommand class

The SubmitCommand class is available from the `hubzero.submit` library and can be easily instantiated.

```
from hubzero.submit.SubmitCommand import SubmitCommand

submitCommand = SubmitCommand()
help(submitCommand)
```

```
Help on SubmitCommand in module hubzero.submit.SubmitCommand object:

class SubmitCommand(builtins.object)
 |  Methods defined here:
 |
 |  __init__(self, configurationDirectory='/etc/submit', hubLogPath='/tmp/submit/.submit.log')
 |      Initialize self.  See help(type(self)) for accurate signature.
 |
 |  addParameters(self, parameters, separator=None)
 |      Supply additional set of parameters for submit run.
 |      parameters can be specified as a single string or
 |      of list of strings.
 |
 |  loadSubmitCommand(self, submitCommandJSONFile)
 |      Load JSON file containing submit command settings.
 |
 |  resetAttachId(self)
 |      Do not reattach to a previously detached submit run.
 |
 |  resetCommand(self)
 |      Remove previous command and command argument settings.
 |
 |  resetCommandArguments(self)
 |      Remove previous command argument settings.
 |
 |  resetDataFile(self)
 |      Remove parameter datafile for submit run.
 |
 |  resetDebug(self)
 |      Turn submit debug reporting off.
 |
 |  resetDefaultSeparator(self)
 |      Set the default parameter separator to the system default.
 |
 |  resetDefaultTailNlines(self)
 |      Set the default number of lines to report when
 |      tailing output files to the system default.
 |
 |  resetDetach(self)
 |      Disable detachment from submit run.
 |
 |  resetEnvironmentVariables(self)
 |      Remove all environment variables set for submit run.
 |
 |  resetHelp(self, detail=None)
 |      Disable requests for general help or a variety of more specific help.
 |      See setHelp() for detail values.
 |
 |  resetInputFiles(self)
 |      Remove inputfiles from submit run.
 |
 |  resetKillJobs(self)
 |      Disable killing of previously submitted runs.
 |
 |  resetLocal(self)
 |      Turn submit local execution off.
 |
 |  resetManager(self)
 |      Set manager for submit run to the default manager.
 |
 |  resetNcores(self)
 |      Set number of cores requested for submit run to the default value.
 |
 |  resetPPN(self)
 |      Set number of cores per node requested for submit run to the default value.
 |
 |  resetParameters(self)
 |      Remove parameters from submit run.
 |
 |  resetProgress(self)
 |      Specify progress reporting to the default style.
 |
 |  resetQueryJobs(self)
 |      Disable query for the status of previously submitted runs.
 |
 |  resetQuota(self)
 |      Enable quota limit for submit run.
 |
 |  resetRedundancy(self)
 |      Set redundancy factor for submit run to the default value.
 |
 |  resetReportMetrics(self)
 |      Turn submit resource metrics reporting off.
 |
 |  resetRunName(self)
 |      Set submit run name to default name.
 |
 |  resetStdin(self)
 |      Remove previous stdin settings.
 |
 |  resetSubmitCommand(self)
 |      Reset submit command settings to default values.
 |
 |  resetTailFiles(self)
 |      Disable real time reporting of files other than
 |      standard output and standard error.
 |
 |  resetTailStderr(self)
 |      Disable real time reporting of standard error file.
 |
 |  resetTailStdout(self)
 |      Disable real time reporting of standard output file.
 |
 |  resetVenue(self)
 |      Remove venue setting from submit command.
 |
 |  resetVenueStatus(self)
 |      Disable request of submit venue status.
 |
 |  resetVersion(self, detail=None)
 |      Disable requests for version information.
 |      See setVersion() for detail values.
 |
 |  resetWait(self)
 |      Do not wait for period of reduced run submission rate to submit run.
 |      If your measured rate of job submission is to high your request for
 |      a new submit run will be denied.
 |
 |  resetWallTime(self)
 |      Set wall time requested for submit run to the default value.
 |
 |  saveSubmitCommand(self, submitCommandJSONFile=None)
 |      Save JSON file containing submit command settings.
 |
 |  setAttachId(self, attachId)
 |      Reattach to a previously detached submit run.
 |
 |  setCommand(self, command)
 |      Specify command to be run on remote resource.
 |      command can be given as a single string or a list
 |      of strings.  A single string will be split to form
 |      a list.  The first list element is taken to be the
 |      command and additional elements are taken to be
 |      command arguments.
 |
 |  setCommandArguments(self, commandArguments)
 |      Specify command arguments to be used on remote resource.
 |      commandArguments can be given as a single string or a list
 |      of strings.  A single string will be split to form
 |      a list.
 |
 |  setDataFile(self, dataFile)
 |      Supply parameter datafile for submit run.
 |      dataFile is a single filename.
 |
 |  setDebug(self, debug=True)
 |      Turn submit debug reporting on or off.
 |
 |  setDefaultSeparator(self, separatorDefault)
 |      Set the default parameter separator.
 |
 |  setDefaultTailNlines(self, tailNlinesDefault)
 |      Set the default number of lines to report when
 |      tailing output files.
 |
 |  setDetach(self, detach=True)
 |      Enable or disable detachment from submit run.
 |      If a job is detached you can continue with other
 |      operations and not wait for the submit to complete.
 |      Job status will need to be monitored to determine
 |      when completion occurs.  A detached can be reattached
 |      at a later time.
 |
 |  setEnvironmentVariables(self, environmentVariables)
 |      Provide a set of environment variables to be set
 |      for submit run.  environmentVariables should be a
 |      dictionary with keys being the environment variable
 |      names.
 |
 |  setHelp(self, detail=None)
 |      Request general help or a variety of more specific help.
 |      detail       submit arguments
 |      None         --help
 |      managers     --help managers
 |      tools        --help tools
 |      venues       --help venues
 |      examples     --help examples
 |
 |  setInputFiles(self, inputFiles)
 |      Supply set of input files for submit run.
 |      inputFiles can be specified as a single filename or
 |      of list of filenames.
 |
 |  setKillJobs(self, killJobs)
 |      Kill previously submitted runs.
 |      killJobs can be given as a single integer id or list
 |      of integer ids.
 |
 |  setLocal(self, local=True)
 |      Turn submit local execution on or off.
 |
 |  setManager(self, manager)
 |      Set manager for submit run.
 |      By default manager is set by the venue or tool configuration.
 |
 |  setNcores(self, nCores)
 |      Set number of cores requested for submit run.
 |      If only one core is required no setting is necessary.
 |
 |  setPPN(self, ppn)
 |      Set number of cores per node requested for submit run.
 |      If number of cores per node is not set for the submit run
 |      a default number of cores per node is set based on the
 |      venue configuration.  This default value is typical based
 |      on the node hardware.
 |
 |  setParameters(self, parameters, separator=None)
 |      Supply set of parameters for submit run.
 |      parameters can be specified as a single string or
 |      of list of strings.  Previously set parameters are
 |      removed.
 |
 |  setProgress(self, detail=None)
 |      Specify progress reporting style.
 |      detail       submit arguments
 |      None
 |      curses       --progress curses
 |      submit       --progress submit
 |      text         --progress text
 |      pegasus      --progress pegasus
 |      silent       --progress silent
 |
 |  setQueryJobs(self, queryJobs)
 |      Do a query for the status of previously submitted runs.
 |      queryJobs can be given as a single integer id or list
 |      of integer ids.
 |
 |  setQuota(self, quota)
 |      Enable or disable quota limit for submit run.
 |      If enabled your HUB disk quota is used to limit data
 |      generation on the remote resource.  This property is
 |      enabled by default.
 |
 |  setRedundancy(self, redundancy)
 |      Set redundancy factor for submit run.
 |      The default redundancy factor is set by submit configuration.
 |
 |  setReportMetrics(self, reportMetrics=True)
 |      Turn submit resource metrics reporting on or off.
 |
 |  setRunName(self, runName)
 |      Set submit run name.
 |      Default run name is the auto generated submit jobId.
 |      The runName is used to set the standard output and
 |      standard error filenames.
 |
 |  setStdin(self, stdinPath)
 |      Specify path to stdin file.
 |
 |  setTailFiles(self, tailFiles)
 |      Enable real time reporting of files other than
 |      standard output and standard error.
 |      tailFiles can be specified as a single filename
 |      or a list of filenames.  To specify the number (#)
 |      of lines to report add :# to the filename.
 |
 |  setTailStderr(self, tailStderr=True, tailStderrNlines=None)
 |      Enable or disable real time reporting of standard error file.
 |
 |  setTailStdout(self, tailStdout=True, tailStdoutNlines=None)
 |      Enable or disable real time reporting of standard output file.
 |
 |  setVenue(self, venue)
 |      Request that run be submitted to "venue".
 |
 |  setVenueStatus(self, venueStatus)
 |      Request status of submit venue named venueStatus.
 |
 |  setVersion(self, detail=None)
 |      Request complete or partial version information.
 |      detail       submit arguments
 |      None         --version
 |      client       --version client
 |      server       --version server
 |      distributor  --version distributor
 |
 |  setWait(self, wait=True)
 |      Wait for period of reduced run submission rate to submit run.
 |      Users have a limited rate at which they are allowed to submit jobs.
 |      The measured rate of run submission is measured over time with
 |      more attention payed to the most recent submission.
 |
 |  setWallTime(self, wallTime)
 |      Set the wall time limit for batch queue runs.
 |      wallTime can be given as integer or floating
 |      point number of minutes.
 |
 |  show(self, args=None, textWidth=80)
 |      Show submit command as determined from previous settings.
 |      If args is supplied previous settings are ignored but not
 |      replaced or overwritten.
 |
 |  submit(self, args=None, stdin=None)
 |      Execute submit command as determined from previous settings.
 |      If args is supplied previous settings are ignored but not
 |      replaced or overwritten.
 |
 |  ----------------------------------------------------------------------
```

The SubmitCommand class has a method that excepts command arguments as a simple list.

```
submitCommand = SubmitCommand()
result = submitCommand.submit(['-w','5',applicationCode,'--C','0.001','--Vin','3'])
```

Another option is build the command incrementally, one argument at a time.

```
submitCommand = SubmitCommand()
submitCommand.setWallTime(5)
submitCommand.setVenue('OSGFactory')
submitCommand.setCommand(applicationCode)
submitCommand.setCommandArguments(['--C','0.001','--Vin','3'])
submitCommand.show()
result = submitCommand.submit()
```
