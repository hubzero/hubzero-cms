<!--
status: generated
source: Event::trigger('metadata.*') call sites and core/plugins/metadata/
-->

# Metadata events

Events in the `metadata` group. A plugin in `core/plugins/metadata/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `metadata.onFileMove`

Fired from:

- [`core/plugins/projects/files/connections.php:1345`](../../../core/plugins/projects/files/connections.php#L1345) with `[$oldName, $item->getAbsolutePath()]`
- [`core/plugins/projects/files/connections.php:1429`](../../../core/plugins/projects/files/connections.php#L1429) with `[$oldName, $entity->getAbsolutePath()]`

Listeners:

- `plg_metadata_local` — [`onFileMove($old, $new)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataEdit`

Fired from:

- [`core/plugins/projects/files/connections.php:1532`](../../../core/plugins/projects/files/connections.php#L1532)

No plugin in the source tree listens for this event.

## `metadata.onMetadataGet`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:983`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L983) with `[$entity]`
- [`core/components/com_projects/api/controllers/filefsv1r0.php:1066`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1066) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1102`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1102) with `[$entity]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1186`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1186) with `[$entity]`
- [`core/plugins/projects/files/connections.php:1539`](../../../core/plugins/projects/files/connections.php#L1539) with `[$entity]`

Listeners:

- `plg_metadata_local` — [`onMetadataGet(\Hubzero\Filesystem\File $file, $maxEntries = 1)`](../../../core/plugins/metadata/local/local.php)

## `metadata.onMetadataSave`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1r0.php:1069`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L1069) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1189`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1189) with `[$entity, array_merge($oldmetadata, $metadata)]`
- [`core/plugins/projects/files/connections.php:1594`](../../../core/plugins/projects/files/connections.php#L1594) with `[ $entity, $metadata, ]`

Listeners:

- `plg_metadata_local` — [`onMetadataSave(\Hubzero\Filesystem\File $file, $metadata)`](../../../core/plugins/metadata/local/local.php)
