<!--
status: generated
source: core/components/com_search/config/config.xml
-->

# Search (com_search)

Configure search settings

Parameters from [`core/components/com_search/config/config.xml`](../../../../core/components/com_search/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `engine` | Engine | list | `basic (Basic (default))` | Select the primary search engine to use. Options: `basic` Basic (default), `solr` Apache Solr. |

## Solr

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `solr_host` | Solr Host | text | `localhost` | COM_SEARCH_PARAM_SOLR_HOST_LABEL |
| `solr_port` | Solr Port | text | `8445` | COM_SEARCH_PARAM_SOLR_PORT_LABEL |
| `solr_core` | Solr Core | text | `hubzero-solr-core` | COM_SEARCH_PARAM_SOLR_CORE_LABEL |
| `solr_context` | Solr Context | text | `solr` | Solr Context |
| `solr_path` | Solr Path | text | `/` | COM_SEARCH_PARAM_SOLR_PATH_LABEL |
| `solr_log_path` | Solr Log Path | text | `/srv/hubzero-solr/logs/solr.log` | COM_SEARCH_PARAM_SOLR_LOG_PATH_LABEL |
| `solr_commit` | CommitWithin | text | `300000` | Time between full commits (in milliseconds). |
| `solr_batchsize` | Batch Size | text | `2000` | Number of records indexed at a time |
| `solr_tagsearch` | Tag Search Box | list | `0 (Off)` | Toggle on/off filtering by tags. Options: `1` On, `0` Off. |
| `solr_queryfields` | Query Fields | text | `url^10 title^5 description fulltext author` | The metadata fields and relative weights to use when querying |
| `solr_phrasefields` | Phrase Fields | text | `title^5 description fulltext author` | The metadata fields and relative weights to use when querying with a phrase |
| `solr_phraseslop` | Phrase Slop | text | `10` | COM_SEARCH_PARAM_SOLR_PHRASESLOP_LABEL |
