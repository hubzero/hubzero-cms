<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/updates
source-id: 3680
modified: 2017-08-17
imported: 2026-09-09
source-state: unpublished
-->
# Updates

The host operating system should be updated on a regular basis to ensure operating system security updates are promptly installed.

```
# yum upgrade
```

The above will also update HUBzero packages but they won't all take effect until they are applied to your site. To apply updates to your site run

> **Note:** This will regenerate your apache configuration files. If you modified them directly they will be overwritten. Be sure to apply apache configuration changes to /etc/httpd/sites-m4/hub.m4 and hub-ssl.m4 files in order to retain the changes between updates

```
# hzcms update
```
