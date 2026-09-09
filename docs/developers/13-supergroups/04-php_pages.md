<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/php_pages
source-id: 3523
modified: 2014-09-10
imported: 2026-09-09
-->
# PHP Pages

## Overview

Super groups have the ability to include PHP code in any group page or module through the page and module managers. If you are finding this is hard to manage or the approval process is taking too long. Users with SSH access and PHP knowledge can add any number of PHP pages to their super group.

## PHP Pages Directory

`/{web_root}/site/groups/{group_id}/pages/`

## PHP Page Hierarchy

- `/{web_root}/site/groups/{group_id}/pages/features.php -> /groups/{group_cn}/features`
- `/{web_root}/site/groups/{group_id}/pages/features/one.php -> /groups/{group_cn}/features/one`
- `/{web_root}/site/groups/{group_id}/pages/features/two.php -> /groups/{group_cn}/features/two`

## PHP Page Includes

The following group include tags can be used within a PHP page.

- `<group:include type="modules" postion="{position}" />`
  - `<group:include type="modules" title="{title}" />`

    - `<group:include type="script" base="" source="{file_path}" />`

      - `<group:include type="stylesheet" base="" source="{file_path}" />`

> **Note:** For Script & Stylesheet group includes you can specify a base param of "template" which will automatically prepend "/template/assets/js" or "/template/assets/css" to the source. If no base is specified, it will look for the file in the groups "uploads" directory.
