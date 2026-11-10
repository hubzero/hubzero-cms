<!--
status: generated
source: Event::trigger('metadata.*') call sites and core/plugins/metadata/
-->

# Metadata events

Events in the `metadata` group. A plugin in `core/plugins/metadata/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `metadata.onFileMove`

Fired from:

- [`core/plugins/projects/files/connections.php:1319`](../../../core/plugins/projects/files/connections.php#L1319) with `[$oldName, $item->getAbsolutePath()]`
- [`core/plugins/projects/files/connections.php:1403`](../../../core/plugins/projects/files/connections.php#L1403) with `[$oldName, $entity->getAbsolutePath()]`

Listeners:

- `plg_metadata_local` — [`onFileMove($old, $new)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataEdit`

Fired from:

- [`core/plugins/projects/files/connections.php:1503`](../../../core/plugins/projects/files/connections.php#L1503)

No plugin in the source tree listens for this event.

## `metadata.onMetadataGet`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:976`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L976) with `[$entity]`
- [`core/components/com_projects/api/controllers/filefsv1r0.php:1056`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1056) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1080`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1080) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1161`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1161) with `[$entity]`
- [`core/plugins/projects/files/connections.php:1510`](../../../core/plugins/projects/files/connections.php#L1510) with `[$entity]`

Listeners:

- `plg_metadata_local` — [`onMetadataGet(\Hubzero\Filesystem\File $file, $maxEntries = 1)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataSave`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:1059`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1059) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1164`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1164) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/plugins/projects/files/connections.php:1565`](../../../core/plugins/projects/files/connections.php#L1565) with `[ $entity, $metadata, ]`

Listeners:

- `plg_metadata_local` — [`onMetadataSave(\Hubzero\Filesystem\File $file, $metadata)`](../../../core/plugins/metadata/local/local.php)
