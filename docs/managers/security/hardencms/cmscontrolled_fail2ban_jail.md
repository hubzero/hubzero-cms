<!--
status: merged
source: https://help.hubzero.org/documentation/22/security_considerations/hardencms/cmscontrolled_fail2ban_jail
source-id: 2829
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# CMS-Controlled Fail2Ban Jail

## Configuration and Details

## Objective

To have the CMS handle user login banning in an attempt to deter brute force attacks.

## CMS Configuration Page

The following settings are accessible from the CMS administrative backend.

![fail2ban1](../../media/cmscontrolled-fail2ban-jail-fail2ban1.png)

These options are accessible by going to the User Menu ­ Members and Member Options.

## User Password Reset Limit

The number of password resets per time period is limited. If the user attempts to reset their password at a threshold deemed by the HUB administrator as excessive, the following message is displayed.

![fail2ban2](../../media/cmscontrolled-fail2ban-jail-fail2ban2.png)

## User Failed Login Limit

The number of failed logins per time period is limited. If the user attempts to reset their password at a threshold deemed by the HUB administrator as excessive, the following message is displayed. This means that an individual's account is temporarily blocked until the time period expires.

![fail2ban3](../../media/cmscontrolled-fail2ban-jail-fail2ban3.png)

## IP­based Blocked User Limit

When the threshold of blocked user accounts per IP network is met, the CMS will trigger a Fail2Ban rule which will block incoming requests from an IP address for a period of time. This is the last line of defense as blocking an IP address may have unintended consequences ­ such as blocking a NAT­ed IP address which several valid users are using to access the hub.

## Assumptions

This approach assumes that the system administrator has configured a jail and a system user account to execute (with sudo) Fail2Ban via the fail2ban­client utility.

On Debian Hosts, Fail2Ban should be version 0.9.5.

0.9.5­1~nd70+1 from <http://neuro.debian.net/debian/>wheezy/main amd64 Packages

## Setup & Configuration

There are a number of subsystems which need to be configured for this scheme to work properly.

## sudo Configuration

A privileged user which can execute:

(root) NOPASSWD: /usr/bin/fail2ban­client set hub­login banip [0­9.]\* (root) NOPASSWD: /usr/bin/fail2ban­client set hub­login unbanip [0­9.]\*

This can be accomplished by adding a sudoers rule in /etc/sudoers.d/ that looks like:

www­data ALL=(root)NOPASSWD: /usr/bin/fail2ban­client set hub­login banip [0­9.]\*

## Fail2Ban Configuration

The system administrator should configure Fail2Ban to create jails which the CMS can add offending IP address into. The amount of time that the ban is valid is configured in Fail2Ban.

The CMS will simply add IP addresses to the Fail2Ban jail which will trigger the Ban Action as specified in the rule set.

The following configuration are more of an example than anything. A seasoned system administrator will have crafted better rules.

*[Sample] Jail Configuration*

#

\# JAILS

\# /etc/fail2ban/jail.local

#

[hub­login] enabled = true

port = http,https filter = hub­login

logpath = /var/log/messages banaction = hublogin­failure bantime = 600

findtime = 1

maxretry = 1

*[Sample] Filter Configuration*

/etc/fail2ban/filter.d/hub­login.conf

\# Fail2Ban configuration file

#

[Definition]

\# Option: failregex

\# Notes.: Regexp to catch known spambots and software alike. Please verify

\# that it is your intent to block IPs which were driven by

\# abovementioned bots.

\# Values: TEXT

#

#We choose something that will never happen

\# Since the CMS will control IP’s placed in the jails failregex = ^&amp;lt;HOST>thisfilterwillneverbefound

\# Option: ignoreregex

\# Notes.: regex to ignore. If this regex matches, the line is ignored.

\# Values: TEXT

#

ignoreregex =

*[Sample] Action Configuration*

\# Fail2Ban configuration file

\# cat /etc/fail2ban/action.d/hublogin­failure.conf [INCLUDES]

before = iptables­common.conf

[Definition]

\# Option: actionstart

\# Notes.: command executed once at the start of Fail2Ban.

\# Values: CMD

#

actionstart = iptables ­N fail2ban­hublogin

iptables ­A INPUT ­j DROP

iptables ­I INPUT ­p tcp ­j fail2ban­hublogin

\# Option: actionstop

\# Notes.: command executed once at the end of Fail2Ban

\# Values: CMD

#

actionstop = iptables ­D fail2ban­hublogin ­p tcp ­j fail2ban­hublogin iptables ­F fail2ban­hublogin

iptables ­X fail2ban­hublogin

\# Option: actioncheck

\# Notes.: command executed once before each actionban command

\# Values: CMD

#

actioncheck = iptables ­n ­L fail2ban­hublogin | grep ­q fail2ban­hublogin

\# Option: actionban

\# Notes.: command executed when banning an IP. Take care that the

\# command is executed with Fail2Ban user rights.

\# Tags: &amp;lt;ip> IP address

\# &amp;lt;failures> number of failures

\# &amp;lt;time> unix timestamp of the ban time

\# Values: CMD

#

actionban = iptables ­I fail2ban­hublogin ­p tcp ­­dport 443 ­s &amp;lt;ip> ­j DROP

iptables ­I fail2ban­hublogin ­p tcp ­­dport

80 ­s &amp;lt;ip> ­j DROP

\# Option: actionunban

\# Notes.: command executed when unbanning an IP. Take care that the

\# command is executed with Fail2Ban user rights.

\# Tags: &amp;lt;ip> IP address

\# &amp;lt;failures> number of failures

\# &amp;lt;time> unix timestamp of the ban time

\# Values: CMD

#

actionunban = iptables ­D fail2ban­hublogin ­p tcp ­­dport 443 ­s &amp;lt;ip> ­j DROP iptables ­D fail2ban­hublogin ­p tcp ­­dport 80 ­s &amp;lt;ip> ­j DROP

[Init]

\# Defaut name of the chain

#

name = DEFAULT

\# Option: protocol

\# Notes.: internally used by config reader for interpolations.

\# Values: [ tcp | udp | icmp | all ] Default: tcp

#

protocol = tcp

\# Option: chain

\# Notes specifies the iptables chain to which the fail2ban rules should be

\# added

\# Values: STRING Default: INPUT chain = INPUT

*[Sample] Banned IP address results* root@example:/var/www/example# fail2ban­client status hub­login Status for the jail: hub­login

|­ Filter

| |­ Currently failed: 0

| |­ Total failed: 4

| `­ File list: /var/log/messages

`­ Actions

|­ Currently banned: 1

|­ Total banned: 4

`­ Banned IP list: 192.168.226.1

root@example:/var/www/example# iptables ­L Chain INPUT (policy DROP)

target prot opt source destination fail2ban­hublogin tcp ­­ anywhere anywhere ACCEPT all ­­ anywhere anywhere

ACCEPT all ­­ anywhere anywhere state RELATED,ESTABLISHED

| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:ssh |
|---|---|---|---|---|---|---|
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:smtp |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:mysql |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:ldap |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:http |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:https |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:http­alt |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpts:830:831 |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:http |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:https |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:http­alt |
| ACCEPT | tcp | ­­ | anywhere | anywhere | tcp | dpt:1170 |
| ACCEPT | icmp | ­­ | anywhere | anywhere |  |  |
| DROP | all | ­­ | anywhere | anywhere |  |  |

Chain FORWARD (policy DROP)

<table>
<tbody>
<tr>
<td>
<p>target ACCEPT ACCEPT</p>
</td>
<td>
<p>prot all all</p>
</td>
<td>
<p>opt</p>
<p>­­</p>
<p>­­</p>
</td>
<td>
<p>source 10.0.0.0/8</p>
<p>anywhere</p>
</td>
<td>
<p>destination anywhere anywhere</p>
</td>
<td colspan="2">
<p> </p>
<p> </p>
<p>ctstate</p>
</td>
</tr>
<tr>
<td colspan="7">
<p>RELATED,ESTABLISHED,DNAT</p>
</td>
</tr>
<tr>
<td>
<p>ACCEPT</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>­­</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>dpts:830:831</p>
</td>
</tr>
<tr>
<td>
<p>ACCEPT</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>­­</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>dpt:http</p>
</td>
</tr>
<tr>
<td>
<p>ACCEPT</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>­­</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>dpt:https</p>
</td>
</tr>
<tr>
<td>
<p>ACCEPT</p>
</td>
<td>
<p>udp</p>
</td>
<td>
<p>­­</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>udp</p>
</td>
<td>
<p>dpt:domain</p>
</td>
</tr>
</tbody>
</table>

Chain OUTPUT (policy ACCEPT)

target prot opt source destination

<table>
<tbody>
<tr>
<td>
<p>Chain fail2ban­hublogin (1 references)</p>
</td>
<td>
<p> </p>
</td>
<td colspan="2" rowspan="2">
<p> </p>
</td>
</tr>
<tr>
<td>
<p>target      prot opt source</p>
</td>
<td>
<p>destination</p>
</td>
</tr>
<tr>
<td>
<p>DROP        tcp  ­­  container.localhost</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>dpt:http</p>
</td>
</tr>
<tr>
<td>
<p>DROP        tcp  ­­  container.localhost</p>
</td>
<td>
<p>anywhere</p>
</td>
<td>
<p>tcp</p>
</td>
<td>
<p>dpt:https</p>
</td>
</tr>
</tbody>
</table>

root@example:/var/www/example#

## User Impact

When a large number of people intend on using the CMS, it may be wise to temporarily disable this feature (e.g. conference, class activity, etc). In the past, many conference goers have mistyped their password in a short period of time creating a false positive for normal Fail2Ban operation. This risk is mitigated by the fact that the number of blocked users is observed before triggering Fail2Ban.
