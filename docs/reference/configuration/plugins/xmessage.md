<!--
status: generated
source: core/plugins/xmessage/*/*.xml
-->

# Xmessage plugins

Parameters of every plugin in the `xmessage` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## XMessage - Email (`plg_xmessage_email`)

Allows an XMessage to be sent via email

This plugin has no parameters.

## XMessage - Handler (`plg_xmessage_handler`)

Enables system messages

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `time_limit` | Time Limit (seconds) | text | `30` | The waiting period, in seconds, between non-system messages |
| `daily_limit` | Daily Limit | text | `100` | The daily limit that non-system messages can be sent |

## XMessage - Internal (`plg_xmessage_internal`)

Allows an XMessage to be sent via Internal (site) system

This plugin has no parameters.
