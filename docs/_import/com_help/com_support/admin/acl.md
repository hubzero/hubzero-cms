<!--
status: imported
source: core/components/com_support/admin/help/en-GB/acl.phtml
imported: 2026-09-09
-->
# Support: ACL

## Overview

The ACL (Access Control Layer) works in conjunction with a user login system to allow or deny a user access to support tickets and various information or features of a ticket.

## Adding/Editing

Permissions may be set for individuals or entire groups of users. All members of a group will inherit any permissions set for the group. If a user is a member of multiple groups with ACL settings, the highest permissions will take affect. Take, for example, the following group settings:

<table>
<caption>Table 1: ACL</caption>
<thead>
<tr>
<th> </th>
<th> </th>
<th colspan="3">Tickets</th>
<th colspan="2">Comments</th>
<th colspan="2">Private Comments</th>
</tr>
<tr>
<th>Object</th>
<th>Model</th>
<th>Read</th>
<th>Update</th>
<th>Delete</th>
<th>Create</th>
<th>Read</th>
<th>Create</th>
<th>Read</th>
</tr>
</thead>
<tbody>
<tr>
<th>dev (111)</th>
<td>group</td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
</tr>
<tr>
<th>support (222)</th>
<td>group</td>
<td><span>yes</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
</tr>
</tbody>
</table>

If user "john" is a member of both groups, his permissions will result as follows:

<table>
<caption>Table 2: Resulting permissions</caption>
<thead>
<tr>
<th> </th>
<th> </th>
<th colspan="3">Tickets</th>
<th colspan="2">Comments</th>
<th colspan="2">Private Comments</th>
</tr>
<tr>
<th>Object</th>
<th>Model</th>
<th>Read</th>
<th>Update</th>
<th>Delete</th>
<th>Create</th>
<th>Read</th>
<th>Create</th>
<th>Read</th>
</tr>
</thead>
<tbody>
<tr>
<th>john (333)</th>
<td>user</td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
</tr>
</tbody>
</table>

Individual ACL settings take precedence over any other settings. Once again, take the following two groups and their permissions settings. User "john" is a member of both groups. We then add specific permissions settings for "john":

<table>
<caption>Table 3: ACL</caption>
<thead>
<tr>
<th> </th>
<th> </th>
<th colspan="3">Tickets</th>
<th colspan="2">Comments</th>
<th colspan="2">Private Comments</th>
</tr>
<tr>
<th>Object</th>
<th>Model</th>
<th>Read</th>
<th>Update</th>
<th>Delete</th>
<th>Create</th>
<th>Read</th>
<th>Create</th>
<th>Read</th>
</tr>
</thead>
<tbody>
<tr>
<th>dev (111)</th>
<td>group</td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
</tr>
<tr>
<th>support (222)</th>
<td>group</td>
<td><span>yes</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>yes</span></td>
<td><span>yes</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
</tr>
<tr>
<th>john (333)</th>
<td>user</td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
</tr>
</tbody>
</table>

The ACL in table 3 will result in the following permissions for user "john":

<table>
<caption>Table 4: Resulting permissions</caption>
<thead>
<tr>
<th> </th>
<th> </th>
<th colspan="3">Tickets</th>
<th colspan="2">Comments</th>
<th colspan="2">Private Comments</th>
</tr>
<tr>
<th>Object</th>
<th>Model</th>
<th>Read</th>
<th>Update</th>
<th>Delete</th>
<th>Create</th>
<th>Read</th>
<th>Create</th>
<th>Read</th>
</tr>
</thead>
<tbody>
<tr>
<th>john (333)</th>
<td>user</td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
<td><span>no</span></td>
</tr>
</tbody>
</table>

> **Note:** A user will always have read access to tickets they submitted or are assigned to. This will even override any specific ACL settings.
