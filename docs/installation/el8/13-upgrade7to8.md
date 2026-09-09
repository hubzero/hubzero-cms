<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/upgrade7to8
source-id: 3675
modified: 2013-09-09
imported: 2026-09-09
source-state: unpublished
-->
# Upgrading from EL7 to EL8

## Upgrading from Enterprise Linux 7 (RHEL/CentOS) to Enterprise Linux 7 (RHEL/CentOS/Rocky/etc)

1. Make sure your current host is fully upgraded with the latest OS packages and latest Hubzero CMS code. See https://help.hubzero.org/documentation/22/installation/redhat/updates
2. CentOS / RHEL recommends migrating to a EL 8 host and not a in-place upgrade. Setup a new host with the EL 8 installed and follow the Hubzero installation instructions at https://help.hubzero.org/documentation/22/installation/el8/install .
3. Migrate data and some configurations. It is the responsibility of the system administrator of the host to decide what should and shouldn't be migrated to the new host. Not everything can be migrated en mass to the new operating system as it will not be compatible.
   1. CMS data migration
      1. Copy the following directories and its sub-directories to the new host at the same location. Do not copy all of /var/www/[hubname]/app/ - it will squash some of the new CMS configuration. Other directories in app/ may copied as needed if they are being used, com_component directories, in the app/components directory, for example.
         1. /var/www/[hubname]/app/site/
         2. /var/www/[hubname]/app/template/[yourtemplate]
      2. Export and Import the CMS database.
         1. Make sure the version of MariaDb are the same on the old and new host.
         2. Export the current database in it's entirety as root.
         3. On the EL8 host, as root, import the database.
         4. Update the mysql database password in /etc/hubzero.secrets/ and in /var/www/[hubname]/app/config/database.php
      3. At the discretion of the system administrator, you may want to copy log files or other data on the current host to the new host.
      4. If modifications have been made to the current host in the /etc/ directory or other conf, we recommend merging the needed configuration into the new hosts rather than a direct copy and overwrite.
