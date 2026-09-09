<!--
status: merged
source: https://help.hubzero.org/documentation/22/security_considerations/faqs
source-id: 2826
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Frequently Asked Questions

## Open-source Security Patch for "Dirty Cow"

Apply the kernel update from OpenVZ using your normal package tools (yum on RHEL or CentOS and apt on Debian) and reboot ASAP. Don't forget to shut down tool sessions before rebooting; if you forgot, clean up the database "session" and "display" table.

2.6.32-openvz-042stab120.5-amd64 (from the output of "uname -a").

Get it by placing this in /etc/apt/sources.list: deb <http://download.openvz.org/debian> wheezy main

## Joomla! Exploits Privilege Vulnerability - Not an issue for Hubzero

Joomla! code is not executed anymore in HUBzero, although some definitions remain. The main application object has been altered to return Hubzero objects; Joomla components have been rewritten or replaced, and all plugins & modules were rewritten. We do not use Joomla’s main application, request objects, user registration system, user object, or related component.
