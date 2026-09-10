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
| `solr_host` | Solr Host | text | `localhost` | Host name of the machine running Solr. |
| `solr_port` | Solr Port | text | `8445` | Port Solr listens on. |
| `solr_core` | Solr Core | text | `hubzero-solr-core` | Name of the Solr core holding the hub's index. |
| `solr_context` | Solr Context | text | `solr` | Solr Context |
| `solr_path` | Solr Path | text | `/` | Base path of the Solr endpoint. |
| `solr_log_path` | Solr Log Path | text | `/srv/hubzero-solr/logs/solr.log` | Path to the Solr log file, as read from the hub's web server. |
| `solr_commit` | CommitWithin | text | `300000` | Time between full commits (in milliseconds). |
| `solr_batchsize` | Batch Size | text | `2000` | Number of records indexed at a time |
| `solr_tagsearch` | Tag Search Box | list | `0 (Off)` | Toggle on/off filtering by tags. Options: `1` On, `0` Off. |
| `solr_queryfields` | Query Fields | text | `url^10 title^5 description fulltext author` | The metadata fields and relative weights to use when querying |
| `solr_phrasefields` | Phrase Fields | text | `title^5 description fulltext author` | The metadata fields and relative weights to use when querying with a phrase |
| `solr_phraseslop` | Phrase Slop | text | `10` | The number of terms between query phrase terms to allow in a match |
