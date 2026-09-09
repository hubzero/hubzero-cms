<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/search/plugins
source-id: 3396
modified: 2019-09-11
imported: 2026-09-09
-->
# Plugins

Search plugins allow developers to add support for different component data in the hub. In order to appear in the search index, the plugin must be enabled. This can be accomplished by going to the Administrative backend > Plug-in Manager > [Filter by Type: Search] > and ensuring that the types are enabled. For example Solr will index wiki pages when the “Search Wiki” plugin is enabled.

The plugin provides some necessary information for indexing and other search-related operation. By default, the categories in the search interface correspond with these plugins.

## System Search Plugins

In order to keep the search index “fresh”, a couple of new system Events have been made that capture when items using the Relational Class / ORM are created, edited, or deleted. Once the system event fires, it calls an event in the Search - Index plugin which handles placing the newly-updated data into the processing queue. A migration has been written to ensure that the System - Content plugin and the Search - Solr plugin have been enabled. If you notice that the index is not being refreshed with new content, check that both of these plugins are enabled.
