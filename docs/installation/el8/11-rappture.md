<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/rappture
source-id: 3702
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# Rappture

## Install

```
sudo yum install -y hubzero-rappture-deb7
```

## Configure

Rappture is used from inside a container and needs several other packages installed to allow use of all its features. This process has been simplified by using the hubzero-rappture-session which only contains the dependencies needed to pull in these other packages.

```
sudo chroot /var/lib/vz/template/debian-7.0-amd64-maxwell
apt-get update; apt-get upgrade
apt-get install -y hubzero-rappture-session
exit
```

> **Note:** A workspace may need to be opened and closed a few times before the changes to the session template appear in a workspace.

## Test

A user must setup their runtime environment in order to use the Rappture toolkit. Run the following command inside a Workspace tool session before attempting to run any Rappture tests.

```
use rappture
```

Rappture comes with several demostration scripts that can effectively test many parts of the package. These demonstrations must be copied to a user's home directory within a workspace before running.

```
$ mkdir examples
$ cp -r /apps/share/rappture/examples/* examples/.
$ cd examples
$ ./demo.bash
```

A window should open on the workspace showing that part of the demonstration. Close that window to see the next demonstration. Some demonstrations may need something inputted to work properly (such as the graphing calculator).
