<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid
source-id: 3539
modified: 2012-09-20
imported: 2026-09-09
-->
# Accessing Outside Computing Resources

## Overview

Tools are hosted within a "tool session" running within the hub environment. The tool session supports the graphical interface, which helps the user set up the problem and visualize results. If the underlying calculation is fairly light weight (e.g., runs in a few minutes or less), then it can run right within the same tool session. But if the job is more demanding, it can be shipped off to another machine via the "submit" command, leaving the tool session host less taxed and more responsive.

This chapter describes the "submit" command, showing how it can be used at the command line within a workspace and also within Rappture-based tools.
