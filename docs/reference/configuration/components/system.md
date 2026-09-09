<!--
status: generated
source: core/components/com_system/config/config.xml
-->

# System (com_system)

Utility component for managing various parts of the system such as the cache.

Parameters from [`core/components/com_system/config/config.xml`](../../../../core/components/com_system/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Geodb

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `geodb_driver` | Geolocation Database type | text | `mysql` | TIPDTATABASETYPE |
| `geodb_host` | Geolocation DB Hostname | text | `localhost` | TIPDATABASEHOSTNAME |
| `geodb_port` | Geolocation DB Port | text | — | TIPDATABASEPORT |
| `geodb_user` | Geolocation DB Username | text | — | TIPDATABASEUSERNAME |
| `geodb_password` | Geolocation DB Password | password | — | TIPDATABASEPASSWORD |
| `geodb_database` | Geolocation DB Schema | text | — | TIPDATABASENAME |
| `geodb_prefix` | Geolocation DB Table Prefix | text | — | TIPDATABASEPREFIX |

## Ldap

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `ldap_primary` | LDAP Primary Host URI | text | `ldap://localhost` | TIPPRIMARYLDAP |
| `ldap_secondary` | LDAP Secondary Host URI | text | — | TIPSECONDARYLDAP |
| `ldap_basedn` | LDAP Base DN | text | — | TIPLDAPBASEDN |
| `ldap_searchdn` | LDAP Search DN | text | — | TIPLDAPSEARCHDN |
| `ldap_searchpw` | LDAP Search Password | password | — | TIPLDAPSEARCHPASSWORD |
| `ldap_managerdn` | LDAP Manager DN | text | — | TIPLDAPMANAGERDN |
| `ldap_managerpw` | LDAP Manager Password | password | — | TIPLDAPMANAGERPASSWORD |
| `ldap_tls` | Use LDAP TLS | radio | `0 (No)` | TIPLDAPTLS. Options: `0` No, `1` Yes. |

## Api

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `COM_SYSTEM_PARAM_WHITELIST_LABEL` |  | textarea | `127.0.0.1` | A comma-separated list of white-listed IP addresses that can access the API for system reporting. |
