<!--
status: generated
source: core/components/com_usage/config/config.xml
-->

# Usage (com_usage)

Parameters from [`core/components/com_usage/config/config.xml`](../../../../core/components/com_usage/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `statsDBDriver` | Stats DB Driver | text | `mysql` | Stats DB Driver |
| `statsDBHost` | Stats DB Host | text | `localhost` | Stats DB Host |
| `statsDBPort` | Stats DB Port | text | — | Stats DB Port |
| `statsDBUsername` | Stats DB Username | text | — | Stats DB Username |
| `statsDBPassword` | Stats DB Password | text | — | Stats DB Password |
| `statsDBDatabase` | Stats Database | text | — | Stats Database |
| `statsDBPrefix` | Stats DB Prefix | text | — | Stats DB Prefix |
| `statsDBSSLCa` | Stats DB SSL CA Path | text | — | If you don't know what this means, don't use it. Specify the path to your CA SSL cert. This requires mysql server configuration to function properly. This is currently only supported by the PDO driver. |
| `mapsApiKey` | Maps API Key | text | — | Google Maps API Key for this site |
| `stats_path` | Path | text | `/site/usage` | General path for storing stats related files |
| `maps_path` | Maps | text | `/site/usage/maps` | Path to maps |
| `plots_path` | Plots | text | `/site/usage/plots` | Path to plots |
| `charts_path` | Charts | text | `/site/usage/charts` | Path to charts |
