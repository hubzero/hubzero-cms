<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/telequotad
source-id: 3700
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# telequotad

## Install

```
sudo yum install -y hubzero-telequotad

sudo service telequotad start
sudo chkconfig telequotad on
```

## Configure

> **Warning:** In order for filesystems quotas to work they must be enabled when they are mounted. Determine which filesystem contains your home directories and add "quota" to the mount option of the corresponding entry in the /etc/fstab file. Only the filesystem with /home on it matters to telequotad.

If quotas weren't already in affect, the run something like the following (depending on your filesystem configuration) to start up the quota system. The following example assumes you want to enable quotas at the root level

```
sudo mount -oremount /home
sudo quotacheck -cugm /home
sudo quotacheck -avugm
sudo quotaon -u /home
```

## Test

```
sudo repquota -a
```

Should show disk usage for all users.
