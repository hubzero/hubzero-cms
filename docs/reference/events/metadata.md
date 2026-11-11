<!--
status: generated
source: Event::trigger('metadata.*') call sites and core/plugins/metadata/
-->

# Metadata events

Events in the `metadata` group. A plugin in `core/plugins/metadata/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `metadata.onFileMove`

Fired from:

- [`core/plugins/projects/files/connections.php:1353`](../../../core/plugins/projects/files/connections.php#L1353) with `[$oldName, $item->getAbsolutePath()]`
- [`core/plugins/projects/files/connections.php:1437`](../../../core/plugins/projects/files/connections.php#L1437) with `[$oldName, $entity->getAbsolutePath()]`

Listeners:

- `plg_metadata_local` — [`onFileMove($old, $new)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataEdit`

Fired from:

- [`core/plugins/projects/files/connections.php:1540`](../../../core/plugins/projects/files/connections.php#L1540)

No plugin in the source tree listens for this event.

## `metadata.onMetadataGet`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:980`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L980) with `[$entity]`
- [`core/components/com_projects/api/controllers/filefsv1r0.php:1063`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1063) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1107`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1107) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1191`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1191) with `[$entity]`
- [`core/plugins/projects/files/connections.php:1547`](../../../core/plugins/projects/files/connections.php#L1547) with `[$entity]`

Listeners:

- `plg_metadata_local` — [`onMetadataGet(\Hubzero\Filesystem\File $file, $maxEntries = 1)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataSave`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:1066`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1066) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1194`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1194) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/plugins/projects/files/connections.php:1602`](../../../core/plugins/projects/files/connections.php#L1602) with `[ $entity, $metadata, ]`

Listeners:

- `plg_metadata_local` — [`onMetadataSave(\Hubzero\Filesystem\File $file, $metadata)`](../../../core/plugins/metadata/local/local.php)
