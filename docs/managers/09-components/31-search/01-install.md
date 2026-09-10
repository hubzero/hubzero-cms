<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/install
source-id: 3391
modified: 2019-09-11
-->
# Installation and first-time configuration

Switching a hub from Basic search to Solr takes three things: an Apache Solr
service running somewhere the hub can reach, the connection settings that
point the hub at it, and a first pass over the hub's content to fill the
index.

> **Note:** Solr itself is not part of this repository. The hub ships only
> the client side — the Solarium 6.2 library from Composer and the two
> adapters,
> [`SolrIndexAdapter`](../../../../core/libraries/Hubzero/Search/Adapters/SolrIndexAdapter.php)
> and
> [`SolrQueryAdapter`](../../../../core/libraries/Hubzero/Search/Adapters/SolrQueryAdapter.php).
> No Solr schema, config set, service file, or package script is in the tree,
> so nothing on this page about installing or running Solr could be checked
> against code. Treat the service instructions as a pointer to your platform
> packaging, not as verified detail.

## Install and start Solr

Hubzero packages Solr for RedHat and Debian as `hubzero-solr`. Your platform
packaging supplies the schema the hub expects, the service definition, and
the port it listens on. Install it with the system package manager and start
the service before configuring anything in the administrator interface.

Once the service answers, the hub needs no shell access to it. Everything
below happens in the administrator interface.

## Point the hub at Solr

1. Go to **Components > Search**.
2. Select **Options**.
3. On the first tab, set **Engine** to **Apache Solr**.
4. Open the **Solr** tab and check the connection settings against your
   installation.
5. Select **Save & Close**.

The connection is assembled from five of those fields — host, port, path,
context, and core — into one Solarium endpoint. Their defaults are:

| Field | Default |
|---|---|
| **Solr Host** | `localhost` |
| **Solr Port** | `8445` |
| **Solr Core** | `hubzero-solr-core` |
| **Solr Context** | `solr` |
| **Solr Path** | `/` |
| **Solr Log Path** | `/srv/hubzero-solr/logs/solr.log` |

**Solr Log Path** is read from the web server, not over the network, so it
has to be a path the hub's PHP process can open. The remaining fields on the
tab tune indexing and querying rather than the connection; they are all
listed in the
[configuration reference](../../../reference/configuration/components/search.md)
and described in [Maintaining the index](05-index.md).

> **Warning:** The defaults match a stock `hubzero-solr` install on the same
> machine as the hub. Changing the host, core, path, or context means the
> corresponding change on the Solr side; there is no validation of these
> fields and no test-connection button.

## What the first visit creates

The first time you open a Solr screen after switching engines, the component
sets up an account for the indexer if `app/config/solr.json` does not already
exist. It creates:

- a user named `hubzerosolrworker` with a random password and no login shell;
- an API application called **HUBzero - Solr Indexing**;
- `app/config/solr.json`, holding that application's client id and secret,
  the account's credentials, and the Solr host and port.

The file is written by `configure()` in the Solr controller:

<!--include: core/components/com_search/admin/controllers/solr.php:157-169-->

> **Warning:** That account is added to **every** access group on the hub, so
> that the indexer can read content at every access level. Do not delete the
> user or the application, and treat `app/config/solr.json` as a secret.

## Check the connection

Return to **Components > Search**. The **Overview** tab shows a Solr Status
panel with the mechanism in use and the time of the last document insert. A
green check and *The search engine is responding* means the hub reached Solr.

If instead you see *The search engine is not responding*, the hub could not
open the endpoint. Check the host, port, core, path, and context on the
**Solr** tab, and check that the Solr service is running.

## Build the first index

A fresh Solr core is empty; nothing is findable until the hub has pushed its
content into it. Two things have to be enabled first, both under
**Extensions > Plug-ins**:

- **System - HUBzero** (`plg_system_content`), which raises the event when a
  record is saved or destroyed. Enabled by default.
- **Search - Solr** (`plg_search_solr`), which writes those records to Solr.
  **Ships disabled** — you have to enable it.

Then build the index from the **Searchable Components** tab:

1. Select **Discover Searchable Components**. The component scans
   `core/components` and `app/components` for a model implementing
   `Hubzero\Search\Searchable` and adds a row for each one it finds.
2. For each component you want indexed, select the **Not Indexed** state
   icon in the **Active?** column. The hub indexes that component's records in
   batches; the row reports its progress and ends at **Indexed**.

The first pass is slow and only needs doing once per component. On a large
hub it is better run from the command line, where it is not bounded by the
web server's time limit:

```bash
muse searchmigration run --all -url='https://yourhub.org'
```

See [`muse searchmigration`](../../../reference/muse.md#muse-searchmigration) for
its options: `-components` indexes a named list instead of `--all`, and
`--rebuild` includes components that have already been indexed once.

> **Note:** `-url` is required — the command stops without it — but nothing
> reads it. Documents get their identity from the hub's **Site Code**
> configuration setting, not from this argument. Pass the hub's own URL and
> ignore it.

Once a component reads **Indexed**, saving a record of that type updates Solr
without further action. [Maintaining the index](05-index.md) covers what
happens after that.
