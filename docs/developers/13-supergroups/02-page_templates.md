<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/page_templates
source-id: 3521
modified: 2014-09-10
imported: 2026-09-09
-->
# Page Templates

## Overview

You'll probably want most of your group pages to look about the same. Sometimes, though, you may need a specific page, or a group of pages, to display or behave differently. This is easily accomplished with page templates.

## Specialized Page Templates

Create a template for one Page: Intended for one specific page, you can create a specialized template, named with that page's alias or ID:

1. page-{alias}.php
2. page-{id}.php

For example: Your About Us page has an alias of 'about-us' and an ID of 6. If template has a file named page-about-us.php or page-6.php, then it will automatically find and use that file to render the About Us page.

> **Note:** To be used, specialized page templates must be in your groups template directory: /{web_root}/site/groups/{group_id}/template/

## Custom Page Templates

Create a template that can be used by any page: A custom page template can be used by multiple pages. To create a custom page template make a new file starting with a template name inside a PHP comment. Here's the syntax:

```php
<?php
/*
Template Name: My Custom Page
*/
```

> **Note:** To be used, custom page templates must be in your groups template directory: /{web_root}/site/groups/{group_id}/template/

## Selecting a Page Template

Once you upload the file to your template's folder, the template name, "My Custom Page", will list in the edit page screen's Template dropdown.

[![Custom template selection](../media/page-templates-customtemplateselect.png)](https://help.hubzero.org/app/site/documentation/1-3-0/webdevs/supergroups/customtemplateselect.png)

## Template Hierarchy

The order below defines which page template gets loaded on any given page. The first match found is used.

1. Custom Template â€” If the page has a custom template assigned, the HUB will looks for that file and, if found, use it.
2. page-{alias}.php â€” Else the HUB looks for and, if found, uses a specialized template named with the page's alias.
3. page-{id}.php â€” Else the HUB looks for and, if found, uses a specialized template named with the page's ID.
4. page.php â€” Else the HUB looks for and, if found, uses the default page template.
5. index.php â€” Else the HUB uses a the template's index file.

## Page Includes

The following group include tags can be used within a group page.

- `<group:include type="modules" postion="{position}" />`
  - `<group:include type="modules" title="{title}" />`

    - `<group:include type="script" base="" source="{file_path}" />`

      - `<group:include type="stylesheet" base="" source="{file_path}" />`

> **Note:** For Script & Stylesheet group includes you can specify a base param of "template" which will automatically prepend "/template/assets/js" or "/template/assets/css" to the source. If no base is specified, it will look for the file in the groups "uploads" directory.
