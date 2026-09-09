<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/webserver
source-id: 3683
modified: 2016-12-16
imported: 2026-09-09
-->
# Apache HTTP Server

## Install Apache HTTP Server

The full HUBzero® Platform currently requires Apache HTTP Server 2.4.x. The basic HUBzero CMS product available here should work with any web server that can be configured to run PHP applications (such as Nginx), but will be limited in what additional HUBzero Platform components can be added later.

> $ sudo dnf -y install httpd
> $ sudo systemctl enable --now httpd
