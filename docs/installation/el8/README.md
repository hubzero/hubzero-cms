<!--
status: merged
source: https://help.hubzero.org/documentation/240/installation/el8
source-id: 3674
modified: 2016-12-16
imported: 2026-09-09
merged-from: 2.2
-->
# Enterprise Linux 8 (RHEL/CentOS/Rocky/etc)

## The HUBzero Platform

Installation instructions for Enterprise Linux 8 (Redhat/CentOS/Rocky/etc). The Hubzero tools infrastructure is not available at this time. If you are interested in running tools then see about our hosted solution at https://hubzero.org.

Please use the side menu to navigate this documentation.

## Target Audience

This document and the installation and maintenance of a HUBzero system has a target audience of **experienced** Linux administrators (preferably experienced with Enterprise Linux (RedHat/CentOS/Rocky/etc distributions).

## Minimum System Requirements

HUBzero (EL) installations require one or more dedicated hosts running Enterprise Linux 8 (RedHat/CentOS/Rocky). Rocky 8 is currently the preferred distribution.

A typical starter HUBzero installation might consist of a single physical server with dual 64-bit quad-core CPUs, 24 Gigabytes of RAM and a terabyte of disk.

Production systems should try to not limit hardware resources, HUBzero is designed to run on systems with many CPU cores and lots of RAM. If you are looking for a system to run a small site with limited physical or virtual resources this is probably not the system for you. However, for demonstration or development purposes we often create VM images with less than a gigabyte of RAM and 5 gigabytes of disk. While fully functional, these virtual machines would only be suitable for a single user doing development or testing.

## System Architecture

All hardware, filesystem partitions, RAID configurations, backup models, security models, etc. and base configurations of the hosts email server, SSH server, network, etc. are the responsibility of the system administrator managing the host.

The Hubzero software expects to be installed on a headless server from a minimal ISO.

## Material from the 2.2 documentation

> **Note:** The text below comes from the older 2.2 page of the same name, where it differed substantially from the 2.4 page above. Reconcile the two when reviewing.

## Target Audience

This document and the installation and maintenance of a HUBzero system has a target audience of **experienced** Linux administrators (preferably experienced with Enterprise Linux (RedHat/CentOS/Rocky/etc distributions).

## Minimum System Requirements

HUBzero (EL) installations require one or more dedicated hosts running Enterprise Linux 8 (RedHat/CentOS/Rocky). Rocky 8 is currently the preferred distribution.

A typical starter HUBzero installation might consist of a single physical server with dual 64-bit quad-core CPUs, 24 Gigabytes of RAM and a terabyte of disk.

Production systems should try to not limit hardware resources, HUBzero is designed to run on systems with many CPU cores and lots of RAM. If you are looking for a system to run a small site with limited physical or virtual resources this is probably not the system for you. However, for demonstration or development purposes we often create VM images with less than a gigabyte of RAM and 5 gigabytes of disk. While fully functional, these virtual machines would only be suitable for a single user doing development or testing.

## System Architecture

All hardware, filesystem partitions, RAID configurations, backup models, security models, etc. and base configurations of the hosts email server, SSH server, network, etc. are the responsibility of the system administrator managing the host.

The Hubzero software expects to be installed on a headless server from a minimal ISO.
