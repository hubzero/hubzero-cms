<!--
status: imported
source: core/components/com_oaipmh/admin/help/en-GB/oaipmh.phtml
imported: 2026-09-09
-->
# OAIPMH

- [Overview](#overview)
- [Schemas](#schemas)
- [Data Providers](#providers)
- [Manual Harvesting Guide](#guide)
- [Toolbar](#toolbar)

<a id="overview"></a>

## Overview

The Open Archives Initiative Protocol for Metadata Harvesting (OAI-PMH) is a low-barrier mechanism for repository interoperability. Data Providers are repositories that expose structured metadata via OAI-PMH. Service Providers then make OAI-PMH service requests to harvest that metadata. OAI-PMH is a set of six verbs or services that are invoked within HTTP.

<a id="schemas"></a>

## Schemas

The OAI-PMH component allows for output in various data schemas. Click the **Schemas** sub-menu item to view the installed schemas and the `metadataPrefix` used to output data in the respective schema.

<a id="providers"></a>

## Data Providers

The OAI-PMH component uses data providers for feeding records to the service. Each provider is represented by a plugin. This allows for easy addition, removal, and configuration of data types without having to modify the core OAIPMH code. The list of available plugins can be found and configured by clicking the **Plugins** menu item above or by navigating to the **Plugins Manager** and filtering by type "oaipmh".

<a id="guide"></a>

## Manual Harvesting Guide

Begin with:

```
yourhub.com/oaipmh?
```

Add a verb:

```
verb=GetRecord&identifier=(a unique record ID)
verb=ListSets
verb=Identify
verb=ListMetadataFormats
verb=ListIdentifiers
verb=ListRecords
```

Specify your metadata format (not needed for ListSets, Identify, ListMetadataFormats):

```
&metadataPrefix=oai_dc
```

If using ListIdentifiers or ListRecords, you may specify a date range:

```
&from=YYYY-MM-DD&until=YYYY-MM-DD
```

To view the next page of results, find the &amp;lt;resumptionToken>XXXXX&amp;lt;/resumptionToken> at the bottom of the XML and place it at the end of the query string:

```
&resumptionToken=XXXXX
```

Example:

```
yourhub.com/oaipmh?verb=ListRecords&metadataPrefix=oai_dc
```

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Options**  
  Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**  
  Opens this help screen.
