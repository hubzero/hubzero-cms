<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/conventions/databaseschema
source-id: 3435
modified: 2015-07-29
imported: 2026-09-09
-->
# Database Schema Conventions

## Table Names

Table names have all lowercase letters and underscores between words, also all table names need to be plural, e.g. invoice_items, orders.

If the table name contains serveral words, only the last one should be plural:

```
applications
application_functions
application_function_roles
```

## Field Names

Field names will be lowercase, generally singular case, and words are separated by underscores, e.g. `order_amount`, `first_name`

## Foreign Keys

The foreign key is named with the singular version of the target table name with \_id appended to it, e.g. `order_id` in the items table where we have items linked to the orders table.

## Many-To-Many Link Tables

Tables used to join two tables in a many to many relationship is named using the model names they link, with the table names in alphabetical order, for example `item_order`.

## Indexes

Indexes should follow the naming pattern of `idx_{column name}`. For example, an index for the column `created_by` on a table would have an indexed named `idx_created_by`.

```sql
ALTER TABLE `#__my_table` ADD INDEX `idx_created_by` (`created_by`);
```

For indexes that use multiple columns, list each column by order of cardinality.

```sql
ALTER TABLE `#__my_table` ADD INDEX `idx_category_referenceid` (`category`, `referenced`);
```

### Unique Indexes

Unique indexes follow the same pattern as above but should start with `uidx_`.

```sql
ALTER TABLE `#__my_table` ADD UNIQUE `uidx_alias` (`alias`);
```

### Fulltext Indexes

Fulltext indexes follow the same pattern as above but should start with `ftidx_`.

```sql
ALTER TABLE `#__my_table` ADD FULLTEXT `ftidx_content` (`content`);
```
