<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/largedatapaths
source-id: 3556
modified: 2016-01-25
imported: 2026-09-09
-->
# Large Data Paths

## Overview

A directory structure for the storage of large data may be available upon request.

Communication with the specific Hub PI is required to determine the scope of resources needed for the implementation of large data sets. Hard disk space allocation will take into consideration the amount of available disk space remaining and the size of the data set that is to be placed on the hub. When approved, please submit a support ticket to have the directory created.

## Shared data directory for a specific tool and its tool developers

A possible directory of "/data/tools/[toolname]" is to be set with permissions of 775, including write access for only members listed as developers of the tool (app-[toolname] group). Data stored here is intended to be used in a tool.

/data/tools should be mounted in the tool containers.

## Shared data directory for a specific group and its members

A possible directory of "/data/groups/[groupname]" is set with permissions of 775, including write access for only members listed members of the group. Data stored here is intended to be maintained by group members and will be available for use in one or more tools.

/data/groups should be mounted in the tool containers.
