<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/templates/migrations
source-id: 3504
modified: 2019-03-13
imported: 2026-09-09
-->
# Migrations

All the common extension types for HUBzero can include their own `migrations` directory. Migrations are used for installing the extension into the required tables for the CMS to know about said extension's existence, installing any needed tables, installing sample data, etc.

To illustrate the typical component directory structures and files:

```php
/app
.. /templates
.. .. /{TemplateName}
.. .. .. /css
.. .. .. /html
.. .. .. /img
.. .. ..  /js
.. .. .. /migrations
.. .. .. .. /Migration20190301102219TplExample.php
.. .. .. error.php
.. .. .. component.php
.. .. .. index.php
.. .. .. templateDetails.xml
.. .. .. template_thumbnail.png
.. .. .. favicon.ico
```

> **Note:** See the [Migrations documentation](../06-database/02-migrations.md) for more about naming conventions, setup, etc.

Templates typically have at least one initial migration for registering the extension with the CMS. This migration typically just involves calling the `addTemplateEntry` helper method:

```php
<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for registering the example plugin
 **/
class Migration20190301102219TplExample extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Register the template
        //
        // @param   string  $element    Template element
        // @param   string  $name       (option) Template name
	// @param   int     $client     (optional, default: 1) Admin (1) or site (0) client
	// @param   int     $enabled    (optional, default: 1) Whether or not the template should be enabled (1=yes, 0=no)
	// @param   int     $home       (optional, default: 0) Whether or not this should become the enabled/home template (1=yes, 0=no)
	// @param   array   $styles     (optional) Template styles
        $this->addTemplateEntry('example', 'example', 0, 1, 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        // Unregister the template
        //
	// @param   string  $name    Template element name
	// @param   int     $client  Client id
        $this->deleteTemplateEntry('example', 0);
    }
}
```

That's all there is to it! The `addTemplateEntry` method adds the necessary entries to the needed database tables for the CMS to be aware of the template's existence.
