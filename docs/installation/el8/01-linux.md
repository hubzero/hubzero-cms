<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/linux
source-id: 3705
modified: 2016-12-16
imported: 2026-09-09
-->
# Linux

## Obtain Linux Hosting

The full HUBzero® Platform currently requires Rocky Linux 8. The basic HUBzero CMS product available here should work on any current Linux distribution, but will be limited in what additional HUBzero Platform components can be added later.

## Minimum System Requirements

HUBzero Platform installations require one or more dedicated hosts running Rocky Linux 8 (or other Red Hat 8 based distribution). A typical starter HUBzero Platform installation might consist of a single physical server with dual 64-bit quad-core CPUs, 24 Gigabytes of RAM and a terabyte of disk.

Production systems should try to not limit hardware resources, HUBzero Platform is designed to run on systems with many CPU cores and lots of RAM. If you are looking for a system to run a small site with limited physical or virtual resources this is probably not the system for you. However, for demonstration or development purposes we often create VM images with less than a gigabyte of RAM and 5 gigabytes of disk. While fully functional, these virtual machines would only be suitable for a single user doing development or testing.
