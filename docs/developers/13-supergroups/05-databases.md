<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/databases
source-id: 3524
modified: 2014-09-10
imported: 2026-09-09
-->
# Databases

## Overview

Each super group comes with its own database. This database can be used to store data for that group. The credentials for accessing that database can be found in the super groups database config file.

## Config Path

`/{web_root}/site/groups/{group_id}/config/db.php`

## Config File Contents

```php
<?php
return array(
	'host' => 'localhost',
	'port' => '',
	'user' => 'sgmanager',
	'password' => 'xxxxx',
	'database' => 'sg_{group_cn}',
	'prefix'   => ''
);
```

## Using the Database

You can use the database anywhere you want in your template, a PHP page, a group component, etc. Anywhere you can run PHP code basically.

Getting a reference to the group database object is very easy:

```php
$database = \Hubzero\User\Group\Helper::getDBO();
```

> **Note:** You can access the group database and the HUB database at the same time. Use the above call to get access to the group database and JFactory::getDBO(); to get access to the HUB database. All you have to do is store them in two different variables.
