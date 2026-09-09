<!--
status: generated
source: core/plugins/oaipmh/*/*.xml
-->

# Oaipmh plugins

Parameters of every plugin in the `oaipmh` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## OAIPMH - Publications (`plg_oaipmh_publications`)

OAIPMH - Publications

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `type` | Publication Category | publicationcategory | `0` | Filter the publications provided by a specific category. |

## OAIPMH - Resources (`plg_oaipmh_resources`)

PLG_OAIPMH_XML_DESCRIPTION

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `type` | Resource Type | resourcetype | `0` | Filter the resources provided by a specific type. |
| `citations` | Include Citations | list | `1 (Yes)` | Include citations as references on records?. Options: `0` No, `1` Yes. |
