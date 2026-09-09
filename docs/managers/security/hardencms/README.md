<!--
status: merged
source: https://help.hubzero.org/documentation/22/security_considerations/hardencms
source-id: 2828
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Hardening the Content Management System

## Application Scanning

A web application scanner will look for typical mistakes made in PHP applications: XSS, CSRF and SQL injections, and more. We use AppScan, but many free application scanners are available. You should scan any component you code yourself, or 3rd party component, that is not part of the HUBzero release. Also, if you modify a component in a HUBzero release, you should scan it for vulnerabilities the change may have introduced. If you find a vulnerability in the HUBzero release itself, please file a ticket at hubzero.org!
