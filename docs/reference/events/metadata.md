<!--
status: generated
source: Event::trigger('metadata.*') call sites and core/plugins/metadata/
-->

# Metadata events

Events in the `metadata` group. A plugin in `core/plugins/metadata/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `metadata.onFileMove`

Fired from:

- [`core/plugins/projects/files/connections.php:1329`](../../../core/plugins/projects/files/connections.php#L1329) with `[$oldName, $item->getAbsolutePath()]`
- [`core/plugins/projects/files/connections.php:1420`](../../../core/plugins/projects/files/connections.php#L1420) with `[$oldName, $entity->getAbsolutePath()]`

Listeners:

- `plg_metadata_local` — [`onFileMove($old, $new)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataEdit`

Fired from:

- [`core/plugins/projects/files/connections.php:1527`](../../../core/plugins/projects/files/connections.php#L1527)

No plugin in the source tree listens for this event.

## `metadata.onMetadataGet`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1_0.php:940`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L940) with `[$entity]`
- [`core/components/com_projects/api/controllers/filefsv1_0.php:1028`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L1028) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1176`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1176) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1271`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1271) with `[$entity]`
- [`core/plugins/projects/files/connections.php:1535`](../../../core/plugins/projects/files/connections.php#L1535) with `[$entity]`

Listeners:

- `plg_metadata_local` — [`onMetadataGet(Hubzero\Filesystem\File $file, $maxEntries = 1)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataSave`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1_0.php:1031`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L1031) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1274`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1274) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/plugins/projects/files/connections.php:1597`](../../../core/plugins/projects/files/connections.php#L1597) with `[ $entity, $metadata ]`

Listeners:

- `plg_metadata_local` — [`onMetadataSave(Hubzero\Filesystem\File $file, $metadata)`](../../../core/plugins/metadata/local/local.php)
