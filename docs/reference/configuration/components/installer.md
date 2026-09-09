<!--
status: generated
source: core/components/com_installer/config/config.xml
-->

# Installer (com_installer)

Installer component for adding, removing and upgrading extensions

Parameters from [`core/components/com_installer/config/config.xml`](../../../../core/components/com_installer/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Preferences

Fine-tune how extensions installation and updates work

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `cachetimeout` | Updates caching (in hours) | integer | `6` | For how many hours should the CMS cache extension update information |
| `system_user` | Muse username | text | `hubadmin` | The system user that the 'muse' command should be run as |
