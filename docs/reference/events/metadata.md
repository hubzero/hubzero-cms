<!--
status: generated
source: Event::trigger('metadata.*') call sites and core/plugins/metadata/
-->

# Metadata events

Events in the `metadata` group. A plugin in `core/plugins/metadata/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `metadata.onFileMove`

Fired from:

- [`core/plugins/projects/files/connections.php:1457`](../../../core/plugins/projects/files/connections.php#L1457) with `[$oldName, $item->getAbsolutePath()]`
- [`core/plugins/projects/files/connections.php:1548`](../../../core/plugins/projects/files/connections.php#L1548) with `[$oldName, $entity->getAbsolutePath()]`

Listeners:

- `plg_metadata_local` — [`onFileMove($old, $new)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataEdit`

Fired from:

- [`core/plugins/projects/files/connections.php:1655`](../../../core/plugins/projects/files/connections.php#L1655)

No plugin in the source tree listens for this event.

## `metadata.onMetadataGet`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1_0.php:967`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L967) with `[$entity]`
- [`core/components/com_projects/api/controllers/filefsv1_0.php:1062`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L1062) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1195`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1195) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1290`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1290) with `[$entity]`
- [`core/plugins/projects/files/connections.php:1663`](../../../core/plugins/projects/files/connections.php#L1663) with `[$entity]`

Listeners:

- `plg_metadata_local` — [`onMetadataGet(Hubzero\Filesystem\File $file, $maxEntries = 1)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataSave`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1_0.php:1065`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L1065) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1293`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1293) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/plugins/projects/files/connections.php:1725`](../../../core/plugins/projects/files/connections.php#L1725) with `[ $entity, $metadata ]`

Listeners:

- `plg_metadata_local` — [`onMetadataSave(Hubzero\Filesystem\File $file, $metadata)`](../../../core/plugins/metadata/local/local.php)
