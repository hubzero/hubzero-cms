<!--
status: generated
source: Event::trigger('metadata.*') call sites and core/plugins/metadata/
-->

# Metadata events

Events in the `metadata` group. A plugin in `core/plugins/metadata/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `metadata.onFileMove`

Fired from:

- [`core/plugins/projects/files/connections.php:1370`](../../../core/plugins/projects/files/connections.php#L1370) with `[$oldName, $item->getAbsolutePath()]`
- [`core/plugins/projects/files/connections.php:1451`](../../../core/plugins/projects/files/connections.php#L1451) with `[$oldName, $entity->getAbsolutePath()]`

Listeners:

- `plg_metadata_local` — [`onFileMove($old, $new)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataEdit`

Fired from:

- [`core/plugins/projects/files/connections.php:1553`](../../../core/plugins/projects/files/connections.php#L1553)

No plugin in the source tree listens for this event.

## `metadata.onMetadataGet`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:979`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L979) with `[$entity]`
- [`core/components/com_projects/api/controllers/filefsv1r0.php:1057`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1057) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1125`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1125) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1204`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1204) with `[$entity]`
- [`core/plugins/projects/files/connections.php:1560`](../../../core/plugins/projects/files/connections.php#L1560) with `[$entity]`

Listeners:

- `plg_metadata_local` — [`onMetadataGet(\Hubzero\Filesystem\File $file, $maxEntries = 1)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataSave`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:1060`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1060) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1207`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1207) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/plugins/projects/files/connections.php:1614`](../../../core/plugins/projects/files/connections.php#L1614) with `[ $entity, $metadata, ]`

Listeners:

- `plg_metadata_local` — [`onMetadataSave(\Hubzero\Filesystem\File $file, $metadata)`](../../../core/plugins/metadata/local/local.php)
